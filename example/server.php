<?php

include __DIR__ . '/../lib/bootstrap.php';

use Naph\PTask\Base;
use Naph\PTask\Processor;

/**
 * Example class to show how to implement a basic job processor
 * 
 */
class ExampleProcessor extends \Naph\PTask\Processor\Standard {

    /**
     * Init time can be used to open database connections and get the class
     * ready to process jobs.
     * 
     */
    public function init() {
        
        $this->log( __CLASS__, 'Initialise processor' );

        srand( rand(1,1000000000) * microtime(true) );
        
    }

    /**
     * Process a job, just set the result to a message indicating when the job
     * was processed.
     *
     * @param Naph\PTask\Job $job
     */
    public function process( Naph\PTask\Job $job ) {

        $result = 'Started at ' . date('H:i:s');
        $sleep =  rand( 1, 3 );

        $this->log( __CLASS__, "Processing job id " . $job->getParam('id') );
        $this->log( __CLASS__, "Sleeping for $sleep seconds" );

        sleep( $sleep );

        $result .= ', and finished at ' . date('H:i:s');

        $job->setResult( $result );

    }
    
}

// create our class which will process the jobs, this will be an instance of
// your application with all your data source connections and stuff set up so
// your jobs can do some real useful work.

$processor = new ExampleProcessor();
$processor->setLoggingEnabled( true );

// start the server and listen for submitted jobs.  this will continue forever
// until the server is stopped.  You can specify the number of worker threads
// to use.  If these die because of fatal errors then they will be restarted (@todo)

$server = new Naph\PTask\Server\Standard( $processor );
$server->setLoggingEnabled( true );
$server->listen();
