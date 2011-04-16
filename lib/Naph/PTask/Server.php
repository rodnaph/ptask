<?php

namespace Naph\PTask;

interface Server {

    /**
     * Start the job server to listen on a specific port
     *
     * @param int $port
     */
    public function listen( $port );

}
