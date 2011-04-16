<?php

include __DIR__ . '/../lib/bootstrap.php';

$jobs = array(
    new Naph\PTask\Job\Standard(),
    new Naph\PTask\Job\Standard(),
    new Naph\PTask\Job\Standard()
);

$runner = new Naph\PTask\Runner\Standard();
$runner->run(
    $jobs,
    5555
);

echo "Results:\n";

foreach ( $jobs as $job ) {
    echo "\tJob: {$job->getResult()}\n";
}
