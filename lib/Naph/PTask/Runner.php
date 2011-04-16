<?php

namespace Naph\PTask;

interface Runner {

    /**
     * Run the specified jobs using the server listening on the specified port
     *
     * @param array $jobs
     * @param int $port
     */
    public function run( array $jobs, $port );

}
