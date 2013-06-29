<?php

namespace Wurstpress\CoreBundle\Tests\Entity;

use Doctrine\Common\Util\Inflector;
use Wurstpress\CoreBundle\Entity\Collection;
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
            'path',
            'file'
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

    public function testToString()
    {
        $entity = new Document();

        $this->assertEquals('', "$entity", 'Entity toString is not working');

        $entity->setPath('test.png');

        $this->assertEquals('uploads/documents/test.png', "$entity", 'Entity toString is not working');
    }

    public function testConstructor()
    {
        $entity = new Document();

        $this->assertNull($entity->getAbsolutePath(), 'Absolute path is not null');
        $this->assertNull($entity->getWebPath(), 'web path is not null');

        $entity->setPath('path.jpg');

        $this->assertEquals(
            realpath(sprintf('%s/../../../../../web/uploads/documents/path.jpg', __DIR__)),
            realpath($entity->getAbsolutePath()),
            'Absolute path is not correct'
        );
        $this->assertEquals('uploads/documents/path.jpg', $entity->getWebPath(), 'WebPath is not web');

        $entity = new Document('/var/www/public_html','wp-uploads');
        $entity->setPath('path.jpg');

        $this->assertEquals(
            '/var/www/public_html/wp-uploads/path.jpg',
            $entity->getAbsolutePath(),
            'Absolute path is not correct'
        );
        $this->assertEquals('wp-uploads/path.jpg', $entity->getWebPath(), 'WebPath is not web');
    }

    public function testSetWebAndUploadDir()
    {
        $entity = new Document();
        $entity->setPath('test.png');

        $this->assertEquals('uploads/documents/test.png', "$entity", 'Entity toString is not working');

        $entity->setUploadDir('wp-uploads');
        $entity->setWebDir('/var/www/public_html');

        $this->assertEquals('wp-uploads/test.png', "$entity", 'Entity toString is not working');

        $this->assertEquals(
            '/var/www/public_html/wp-uploads/test.png',
            $entity->getAbsolutePath(),
            'Absolute path is not correct'
        );
    }

    public function testRemoveUpload()
    {
        $entity = new Document(sys_get_temp_dir(),'.');

        $entity->setPath('test.file');
        touch($entity->getAbsolutePath());

        $this->assertTrue(file_exists($entity->getAbsolutePath()));

        $entity->removeUpload();

        $this->assertFalse(file_exists($entity->getAbsolutePath()));
    }

    public function testUpload()
    {
        $entity = new Document(sys_get_temp_dir(),'.');
        $entity->setPath('test.file');

        $file = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $file
            ->expects($this->once())
            ->method('move');

        $entity->setFile($file);

        $entity->upload();
    }

    public function testIsImage()
    {
        $entity = new Document();

        $entity->setMimeType('text/html');

        $this->assertFalse($entity->isImage());

        $entity->setMimeType('image/jpeg');

        $this->assertTrue($entity->isImage());
    }

    public function testPreUpload()
    {
        $entity = new Document();

        $file = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $file
            ->expects($this->once())
            ->method('guessExtension');

        $file
            ->expects($this->once())
            ->method('getClientOriginalName');

        $file
            ->expects($this->once())
            ->method('getClientSize');

        $file
            ->expects($this->once())
            ->method('getClientMimeType');

        $entity->setFile($file);

        $entity->preUpload();
    }

    public function testRelations()
    {
        $entity = new Document();
        $collection = new Collection();

        $this->assertNull($entity->getCollection());

        $entity->setCollection($collection);

        $this->assertNotNull($entity->getCollection());
    }
}