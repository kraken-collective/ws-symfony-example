<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $serverConfig       = $this->get('kraken.ws.server_config.chat');
        $connectionHelper   = $this->get('kraken.ws.connection_helper');
        $sessionId          = $request->cookies->get('PHPSESSID');

        $endpoint           = $connectionHelper->buildWebsocketAddress($serverConfig, $sessionId, '/chat');

        return $this->render('default/index.html.twig', [
            'endpoint' => $endpoint
        ]);
    }
}
