<?php

namespace Naph\PTask;

/**
 * This class controls processing jobs and is meant to be implemented by the
 * application using the library.
 *
 */
interface Processor {

    /**
     * Initialise is called when worker processes are started, and should be the
     * time to perform jobs like opening database connections, etc... that the
     * job processor will use.
     *
     */
    public function init();

    /**
     * Process a single job definition, and set the result on the job
     *
     * @param Naph\PTask\Job $job
     */
    public function process( Job $job );

}
