<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Wurstpress\CoreBundle\Entity\Page;
use Wurstpress\CoreBundle\Tests\AppTestCase;

class PageTest extends AppTestCase
{
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testAccessors()
    {
        $page = new Page();

        $page->setTitle('Home');
        $page->setContent('This is sample content');

        $this->em->persist($page);
        $this->em->flush();

        $this->assertNotNull($page->getTitle(), 'Home');
        $this->assertNotNull($page->getContent(), 'Content has been set');
        $this->assertNotNull($page->getId(), 'Id has been set');
    }

    public function testCreatedUpdated()
    {
        $page = new Page();

        $page->setTitle('Cuntact us');
        $page->setContent('Call us on 0000');

        $this->em->persist($page);

        $this->assertNotNull($page->getCreated(), 'Created has been set');
        $this->assertNotNull($page->getUpdated(), 'Updated has been set');

        $date = new \DateTime('yesterday noon');

        $page->setCreated($date);
        $page->setUpdated($date);

        $this->em->persist($page);

        $this->assertEquals($date, $page->getCreated(), 'Created has been updated');
        $this->assertEquals($date, $page->getUpdated(), 'Updated has been updated');
    }

    public function testSluggable()
    {
        $page = new Page();

        $page->setTitle('This is title');
        $page->setContent('This is sample content');

        $this->em->persist($page);
        $this->em->flush();

        $this->assertEquals($page->getSlug(), 'this-is-title');

        $this->assertEquals(
            'sample-slug',
            $page->setSlug('sample-slug')->getSlug(),
            'setting manually slug is working'
        );
    }

    public function testTree()
    {
        $p1 = new Page();
        $p1->setTitle('Home');
        $p1->setContent('This is content 1');

        $this->em->persist($p1);

        $p2 = new Page();
        $p2->setTitle('About us');
        $p2->setContent('This is content 2');

        $p2->setParent($p1);

        $this->em->persist($p2);

        $this->assertEquals($p1, $p2->getParent(), 'Parent page match');

        $p3 = new Page();
        $p3->setTitle('What we do');
        $p3->setContent('This is content 3');

        $p3->setParent($p1);

        $this->em->persist($p3);

        $repository = $this->em->getRepository('Wurstpress\CoreBundle\Entity\Page');

        $this->em->flush();

        $this->assertEquals(2, $repository->childCount($p1, true), 'The number of direct children to current page should be 2');
        $this->assertEquals(2, $repository->childCount($p1), 'The number of all children to current page should be 2');

        $p4 = new Page();
        $p4->setTitle('How we do it');
        $p4->setContent('This is content 4');
        $p4->setParent($p3);

        $this->em->persist($p4);
        $this->em->flush();

        $this->assertEquals(2, $repository->childCount($p1, true), 'The number of direct children to current page should be 2');
        $this->assertEquals(3, $repository->childCount($p1), 'The number of all children to current page should be 3');

    }
}