<?php

namespace Naph\PTask;

interface Server {

    const DEFAULT_PORT = 5555;

    const DEFAULT_WORKERS = 10;

    /**
     * Start the job server to listen on a specific port
     *
     * @param int $port
     */
    public function listen( $port=self::DEFAULT_PORT );

}
