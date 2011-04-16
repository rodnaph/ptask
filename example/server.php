<?php

include __DIR__ . '/../lib/bootstrap.php';

class MyProcessor implements Naph\PTask\Processor {
    
    public function process( Naph\PTask\Job $job ) {
        $job->setResult( 'Done at ' . date('H:i:s') );
    }
    
}

$processor = new MyProcessor();

$server = new Naph\PTask\Server\Standard();
$server->listen( 5555, 3 );
