<?php
namespace KrakenCollective\WsSymfonyBundle\Exception;

use Exception;

class ServerNotFoundException extends WsSymfonyException
{
    public function __construct($serveAlias, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Server aliased "%s" does not exist.', $serveAlias);

        parent::__construct($message, $code, $previous);
    }
}
