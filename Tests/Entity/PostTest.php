<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Wurstpress\CoreBundle\Entity\Collection;
use Wurstpress\CoreBundle\Entity\Post;
use Wurstpress\CoreBundle\Entity\Comment;
use Doctrine\Common\Collections\ArrayCollection;
use Wurstpress\CoreBundle\Tests\AppTestCase;

class PostTest extends AppTestCase
{
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testSluggable()
    {
        $post = new Post();

        $post->setTitle('This is title');
        $post->setContent('This is sample content');

        $this->em->persist($post);
        $this->em->flush();

        $this->assertNotNull($post->getSlug(), 'Slug is correct');
        $this->assertNotNull($post->getCreated(), 'Created has been set');
        $this->assertNotNull($post->getUpdated(), 'Updated has been set');
        $this->assertNotNull($post->getTitle(), 'Title has been set');
        $this->assertNotNull($post->getContent(), 'Content has been set');
        $this->assertNotNull($post->getId(), 'Id has been set');

        $this->assertEquals(
            'sample-slug',
            $post->setSlug('sample-slug')->getSlug(),
            'setting manually slug is working'
        );
    }

    public function testAddComment()
    {
        $post = new Post();

        $post->setTitle('Another post');
        $post->setContent('This is content');

        $c1 = new Comment();
        $c1->setContent('Like it');

        $this->em->persist($c1);

        $post->addComment($c1);

        $this->em->persist($post);

        $this->assertEquals(1, count($post->getComments()), 'Post has 1 comment');
    }

    public function testSetComments()
    {
        $post = new Post();

        $post->setTitle('Third post');
        $post->setContent('This is content');

        $c1 = new Comment();
        $c1->setContent('Like it');

        $this->em->persist($c1);

        $post->addComment($c1);

        $this->em->persist($post);

        $this->assertEquals(1, count($post->getComments()), 'Post has 1 comment');

        $comments = new ArrayCollection();

        for($i = 0; $i < 5; $i++)
        {
            $comments[$i] = new Comment();
            $comments[$i]->setContent('Like it');
            $this->em->persist($comments[$i]);
        }

        $post->setComments($comments);

        $this->em->persist($post);

        $this->assertEquals(5, count($post->getComments()), 'Post has 5 comments, not 6 as the collection is overridden');
    }

    public function testCollectionRelation()
    {
        $entity = new Post();

        $collection = new Collection();

        $this->assertNull($entity->getCollection());
        $fluent = $entity->setCollection($collection);
        $this->assertEquals($entity, $fluent);
        $this->assertEquals($collection, $entity->getCollection());
    }

    public function testRemoveComment()
    {
        $post = new Post();

        $post->setTitle('Fourth post');
        $post->setContent('This is content');

        $comments = new ArrayCollection();

        for($i = 0; $i < 5; $i++)
        {
            $comments[$i] = new Comment();
            $comments[$i]->setContent('Like it');
            $this->em->persist($comments[$i]);
        }

        $post->setComments($comments);

        $this->em->persist($post);

        $this->assertEquals(5, count($post->getComments()), 'Post has 5 comments');

        $post->removeComment($comments[1]);

        $this->em->persist($post);

        $this->assertEquals(4, count($post->getComments()), 'Post has 4 comments');
    }

    public function testCreatedUpdated()
    {
        $post = new Post();

        $post->setTitle('Fifth post');
        $post->setContent('This is content');

        $this->em->persist($post);

        $this->assertNotNull($post->getCreated(), 'Created has been set');
        $this->assertNotNull($post->getUpdated(), 'Updated has been set');

        $date = new \DateTime('yesterday noon');

        $post->setCreated($date);
        $post->setUpdated($date);

        $this->em->persist($post);

        $this->assertEquals($date, $post->getCreated(), 'Created has been updated');
        $this->assertEquals($date, $post->getUpdated(), 'Updated has been updated');
    }

    public function testToString()
    {
        $post = new Post();

        $post->setTitle('Sixth post');
        $post->setContent('This is content');

        $this->em->persist($post);

        $this->assertEquals('Sixth post', "$post", 'toString implementation works');
    }

    public function testTaggable()
    {
        $post = new Post();

        $post->setTitle('7th post');
        $post->setContent('This is content');

        $this->em->persist($post);
        $this->em->flush();

        $this->assertEquals('wurstpress_post', $post->getTaggableType(), 'taggable type is not correct');
        $this->assertEquals($post->getId(), $post->getTaggableId(), 'taggable id is not correct');
        $this->assertEquals(new ArrayCollection(), $post->getTags(), 'should be empty, but it isn\'t');

        $tagManager = $this->container->get('fpn_tag.tag_manager');
        $fooTag = $tagManager->loadOrCreateTag('foo');
        $tagManager->addTag($fooTag, $post);

        $tagManager->saveTagging($post);

        $tagManager->loadTagging($post);
        $this->assertEquals(1, count($post->getTags()), 'should be empty, but it isn\'t');
    }

}
