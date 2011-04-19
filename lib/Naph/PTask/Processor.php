<?php

namespace Naph\PTask;

/**
 * This class controls processing jobs and is meant to be implemented by the
 * application using the library.
 *
 */
interface Processor {

    /**
     * Process a single job definition, and set the result on the job
     *
     * @param Naph\PTask\Job $job
     */
    public function process( Job $job );

}
