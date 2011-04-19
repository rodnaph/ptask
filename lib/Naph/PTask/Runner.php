<?php

namespace Naph\PTask;

/**
 * This defines an object to handle dispatching jobs to be executed to the server
 * and then waiting for their completion before returning them.
 * 
 */
interface Runner {

    /**
     * Run the specified jobs using the server listening on the specified port.
     * The processed jobs will then be returned as an array when they have all
     * completed.  The order the jobs are returned in will not always correspond
     * with the order they were supplied in.
     *
     * If a job throws an error then null will be returned for that job, otherwise
     * it will be a Naph\PTask\Job object and should have the result of being
     * processed available via the getResult() method.
     *
     * @param array $jobs
     * @param int $port
     *
     * @return array
     */
    public function run( array $jobs, $port=Server::DEFAULT_PORT );

}
