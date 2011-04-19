<?php

include __DIR__ . '/../lib/bootstrap.php';

$jobs = array();

for ( $i=1; $i<=3; $i++ ) {
    $job = new Naph\PTask\Job\Standard();
    $job->setParam( 'id', $i );
    $jobs[] = $job;
}

$runner = new Naph\PTask\Runner\Standard();
$jobs = $runner->run(
    $jobs,
    5555
);

echo "Results:\n";

foreach ( $jobs as $job ) {
    echo "\tJob: {$job->getResult()}\n";
}
