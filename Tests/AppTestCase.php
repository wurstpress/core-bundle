<?php

namespace Wurstpress\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class AppTestCase extends WebTestCase
{
    protected $application;
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->application = new Application($this->client->getKernel());
        $this->application->setAutoExit(false);
        $this->runConsole("doctrine:schema:drop", array("--force" => true));
        $this->runConsole("doctrine:schema:create");
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
}
