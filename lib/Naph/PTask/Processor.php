<?php

namespace Naph\PTask;

interface Processor {

    /**
     * Process a single job definition, and set the result on the job
     *
     * @param Naph\PTask\Job $job
     */
    public function process( Job $job );

}
