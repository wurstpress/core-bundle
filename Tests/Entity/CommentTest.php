<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Wurstpress\CoreBundle\Entity\Comment;
use Wurstpress\CoreBundle\Entity\Post;
use Wurstpress\CoreBundle\Tests\AppTestCase;

class CommentTest extends AppTestCase
{
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testAccessors()
    {
        $comment = new Comment();
        
        $comment->setContent('This is sample content');

        $this->em->persist($comment);
        $this->em->flush();

        $this->assertNotNull($comment->getContent(), 'Content has been set');
        $this->assertNotNull($comment->getId(), 'Id has been set');
        $this->assertNull($comment->getPost(), 'Comment is not assigned to any post yet');
        $this->assertFalse($comment->isApproved(), 'Comment is not approved yet');

        $this->assertEquals('comment', $comment->getSource(), 'Default source is comment');

        $comment->setSource('trackback');
        $this->em->persist($comment);

        $this->assertEquals('trackback', $comment->getSource(), 'source has been set to trackback');

        $comment->setSource('pingback');
        $this->em->persist($comment);

        $this->assertEquals('pingback', $comment->getSource(), 'source has been set to pingback');

        $comment->setApproved(true);
        $this->em->persist($comment);

        $this->assertTrue($comment->isApproved(), 'Comment is approved now');

        $comment->setApproved(false);
        $this->em->persist($comment);

        $this->assertFalse($comment->isApproved(), 'Comment is not approved now');

    }

    public function testSetPost()
    {
        $comment = new Comment();
        $comment->setContent('Dont like it');

        $post = new Post();
        $post->setTitle('Post that people dont like');
        $post->setContent('Content');

        $this->em->persist($post);

        $comment->setPost($post);

        $this->em->persist($comment);

        $this->assertNotNull($comment->getPost(), 'Comment is assigned to Post');
        $this->assertEquals($post, $comment->getPost(), 'Post object is the same');
    }

    public function testToString()
    {
        $comment = new Comment();
        $comment->setContent('Dont like it');

        $this->em->persist($comment);

        $this->assertEquals('Dont like it', "$comment", 'toString implementation works');
    }

    public function testCreatedUpdated()
    {
        $comment = new Comment();

        $comment->setContent('Dont like it');

        $this->em->persist($comment);

        $this->assertNotNull($comment->getCreated(), 'Created has been set');
        $this->assertNotNull($comment->getUpdated(), 'Updated has been set');

        $date = new \DateTime('yesterday noon');

        $comment->setCreated($date);
        $comment->setUpdated($date);

        $this->em->persist($comment);

        $this->assertEquals($date, $comment->getCreated(), 'Created has been updated');
        $this->assertEquals($date, $comment->getUpdated(), 'Updated has been updated');
    }

    public function testTree()
    {
        $c1 = new Comment();
        $c1->setContent('This is comment1');

        $this->em->persist($c1);

        $c2 = new Comment();
        $c2->setContent('This is comment2');

        $c2->setParent($c1);

        $this->em->persist($c2);

        $this->assertEquals($c1, $c2->getParent(), 'Parent comment match');

        $post = new Post();
        $post->setTitle('Commented post');
        $post->setContent('Content of the post');

        $this->em->persist($post);

        $c1->setPost($post);

        $this->em->persist($c1);

        $c3 = new Comment();
        $c3->setContent('This is comment3');
        $c3->setParent($c1);

        $this->em->persist($c3);

        $this->assertNull($c3->getPost(), 'Relation to Post does not propagate on children');

        $repository = $this->em->getRepository('Wurstpress\CoreBundle\Entity\Comment');

        $this->em->flush();

        $this->assertEquals(2, $repository->childCount($c1, true), 'The number of direct replies to current comment should be 2');
        $this->assertEquals(2, $repository->childCount($c1), 'The number of all replies to current comment should be 2');

        $c4 = new Comment();
        $c4->setContent('This is comment4');
        $c4->setParent($c3);

        $this->em->persist($c4);
        $this->em->flush();

        $this->assertEquals(2, $repository->childCount($c1, true), 'The number of direct replies to current comment should be 2');
        $this->assertEquals(3, $repository->childCount($c1), 'The number of all replies to current comment should be 3');

    }
}
