<?php

namespace Wurstpress\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WurstpressCoreBundle:Default:index.html.twig');
    }

    public function navigationAction()
    {
        return $this->render('WurstpressCoreBundle:Default:navigation.html.twig');
    }
}
