<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $server             = $this->get('kraken.ws.server.test_server');
//        $connectionHelper   = $this->get('kraken.ws.connection_helper');
        $sessionId          = $request->cookies->get('PHPSESSID');

//        $endpoint = $connectionHelper->buildWebsocketAddress($server, $sessionId, '/test');
        $endpoint = "ws://127.0.0.1:6080/chat?token=$sessionId";

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'endpoint' => $endpoint
        ]);
    }
}
