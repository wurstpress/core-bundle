<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Wurstpress\CoreBundle\Entity\Post;
use Wurstpress\CoreBundle\Entity\Tag;
use Wurstpress\CoreBundle\Entity\Tagging;
use Wurstpress\CoreBundle\Tests\AppTestCase;

class TaggableTest extends AppTestCase
{
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testAccessors()
    {
        $tag = new Tag();
        $tag->setTagging(5);
        $this->assertEquals(5, $tag->getTagging(), 'getter and setter do not talk to each other');

        $tagging = new Tagging();
        $tagging->setTag(3);
        $this->assertEquals(3, $tagging->getTag(), 'getter and setter do not talk to each other');
    }

    public function testPersistence()
    {
        $tagManager = $this->container->get('fpn_tag.tag_manager');
        $tag = $tagManager->loadOrCreateTag('test');

        $tagging = new Tagging();
        $tagging->setTag($tag);
        $tagging->setResource($this->getPost());

        $this->assertNotNull($tag->getId(), 'Id is not set');
        $this->assertNull($tagging->getId(), 'Id is set');

        $this->em->persist($tagging);
        $this->em->flush();

        $this->assertNotNull($tag->getId(), 'Id is not set');
        $this->assertNotNull($tagging->getId(), 'Id is not set');
    }

    protected function getPost()
    {
        $post = new Post();

        $post->setTitle('7th post');
        $post->setContent('This is content');

        $this->em->persist($post);
        $this->em->flush();
        return $post;
    }
}