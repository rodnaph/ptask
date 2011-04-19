<?php

namespace Naph\PTask;

/**
 * The server waits listening on the specified port to be passed jobs from the
 * Runner, which it then dispatches to parallel worker threads and returns them
 * back to the runner when each completes.
 *
 */
interface Server {

    /**
     * Default port for the server to listen on
     */
    const DEFAULT_PORT = 5555;

    /**
     * Default number of worker threads to spawn
     */
    const DEFAULT_WORKERS = 10;

    /**
     * Start the job server to listen on a specific port
     *
     * @param int $port
     */
    public function listen( $port=self::DEFAULT_PORT );

}
