<?php

include __DIR__ . '/../lib/bootstrap.php';

// create 3 jobs that we'll submit to the server for processing.

$jobs = array();

for ( $i=1; $i<=10; $i++ ) {
    $job = new Naph\PTask\Job\Standard();
    $job->setParam( 'id', $i );
    $jobs[] = $job;
}

// create our job runner, this will handle submitting the jobs to the server
// that is listening on the specified port, and then returning the processed
// jobs when they are complete.

$runner = new Naph\PTask\Runner\Standard();
$runner->setLoggingEnabled( true );
$jobs = $runner->run(
    $jobs
);

// report the result of the processed jobs that were submitted.  the results
// of the job should be available via the getResult() method on the job object.

echo "Results:\n";

foreach ( $jobs as $job ) {
    echo "\t";
    echo $job == null
        ? 'Job caused an error'
        : "Job: {$job->getResult()}";
    echo "\n";
}
