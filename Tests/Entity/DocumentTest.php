<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Doctrine\Common\Inflector\Inflector;
use Wurstpress\CoreBundle\Entity\Document;
use Wurstpress\CoreBundle\Tests\AppTestCase;

class DocumentTest extends AppTestCase
{
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testAccessors()
    {
        $entity = new Document();

        foreach ([
            'name',
            'mime_type',
            'path'
         ] as $field)
        {

            $setter = sprintf('set%s', ucfirst(Inflector::camelize($field)));
            $getter = sprintf('get%s', ucfirst(Inflector::camelize($field)));

            $this->assertNull($entity->$getter());
            $fluent = $entity->$setter('ok');
            $this->assertEquals($entity, $fluent, 'Fluent interface is not working');
            $this->assertEquals('ok', strtolower($entity->$getter()));
            $entity->$setter('ok ok');
            $this->assertEquals('ok ok', strtolower($entity->$getter()));
        }

        $this->assertNull($entity->getSize(), 'Size is not null');
        $fluent = $entity->setSize(500);
        $this->assertEquals($entity, $fluent, 'Fluent interface is not working');
        $this->assertEquals(500, $entity->getSize(), 'Size does not match');
    }

    public function testTimestamps()
    {
        $entity = new Document();

        foreach([
            'created',
            'updated'
        ] as $field)
        {
            $setter = sprintf('set%s', ucfirst(Inflector::camelize($field)));
            $getter = sprintf('get%s', ucfirst(Inflector::camelize($field)));

            $this->assertNull($entity->$getter());
            $fluent = $entity->$setter(new \DateTime());
            $this->assertEquals($entity, $fluent, 'Fluent interface is not working');
            $this->assertNotNull($entity->$getter());
        }
    }

    public function testPersistence()
    {
        $entity = new Document();
        $entity->setName('test.png');
        $entity->setMimeType('image/png');
        $entity->setPath('/path/to/document');
        $entity->setSize(500);

        $this->assertNull($entity->getId());

        $this->em->persist($entity);
        $this->em->flush();

        $this->assertNotNull($entity->getId());
    }
}