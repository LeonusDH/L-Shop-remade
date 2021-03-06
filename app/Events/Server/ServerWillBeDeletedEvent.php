<?php
declare(strict_types = 1);

namespace app\Events\Server;

use app\Entity\Server;

class ServerWillBeDeletedEvent
{
    /**
     * @var Server
     */
    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }
}
