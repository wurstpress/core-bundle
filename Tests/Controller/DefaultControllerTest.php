<?php

namespace Wurstpress\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $route = $client->getContainer()->get('router')->generate('wurstpress_core_homepage', array(), false);

        $crawler = $client->request('GET', $route);

        $this->assertTrue($crawler->filter('html:contains("Welcome")')->count() > 0);
    }
}
