<?php

namespace KrakenCollective\WsSymfonyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KrakenCollectiveWsSymfonyBundle:Default:index.html.twig');
    }
}
