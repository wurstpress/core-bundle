<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Doctrine\Common\Util\Inflector;
use Wurstpress\CoreBundle\Entity\Collection;
use Wurstpress\CoreBundle\Entity\Document;
use Wurstpress\CoreBundle\Tests\AppTestCase;

class CollectionTest extends AppTestCase
{
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testAccessors()
    {
        $entity = new Collection();

        foreach ([
            'name',
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
    }

    public function testTimestamps()
    {
        $entity = new Collection();

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
        $entity = new Collection();
        $entity->setName('test collection');

        $this->assertNull($entity->getId());

        $this->em->persist($entity);
        $this->em->flush();

        $this->assertNotNull($entity->getId());
    }

    public function testRelations()
    {
        $entity = new Collection();
        $document = new Document();

        $this->assertEquals(0, count($entity->getDocuments()));

        $entity->addDocument($document);

        $this->assertEquals(1, count($entity->getDocuments()));

        $entity->removeDocument($document);

        $this->assertEquals(0, count($entity->getDocuments()));
    }

    public function testToString()
    {
        $entity = new Collection();
        $entity->setName('Test Name');

        $this->assertEquals('Test Name', "$entity");
    }
}
