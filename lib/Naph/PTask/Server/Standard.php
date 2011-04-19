<?php

namespace Naph\PTask\Server;

use Naph\PTask\Processor;

use ZMQ;
use ZMQContext;
use ZMQPoll;
use ZMQ\ZMsg;

/**
 * Standard implementation of a server which starts worker threads ready to process
 * jobs, then listens for requests to run jobs, passing them back when complete.
 * 
 */
class Standard implements \Naph\PTask\Server {

    public function __construct( Processor $processor ) {
        
        $this->processor = $processor;
        
    }

    /**
     * Listen for jobs on the specified port, forking the specified number of
     * child processes to act as the parallel worker threads.
     *
     * @param int $port
     * @param int $workerCount
     */
    public function listen( $port, $workerCount=10 ) {

        echo "\nStarting on port $port with $workerCount workers...\n\n";

        for ( $i=1; $i<=$workerCount; $i++ ) {

            $pid = pcntl_fork();

            switch ( $pid ) {

                case -1:
                    die( 'Forking workder process failed :(' );
                    break;

                case 0:
                    while ( true ) {
                        $this->initAsWorker( $port );
                        exit;
                    }
                    exit;
                    break;

                // @todo handle zombie processes

            }

        }

        $this->initAsMaster( $port );

    }

    /**
     * Initialise the process as a worker from the base port
     *
     * @param int $port
     */
    protected function initAsWorker( $port ) {

        $id = rand( 1, 100000 );

        echo "Worker #$id\n";

        $ctx = new ZMQContext();

        $worker = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $worker->connect( 'ipc://workers' );

        $zmsg = new ZMsg( $worker );

        while ( true ) {

            $zmsg->recv();

            $job = unserialize( $zmsg->body() );

            $this->processor->process( $job );

            echo "#$id Done\n";

            $zmsg->send(serialize( $job ));
            
        }

        echo "## Worked Finished\n";
        exit;

    }

    protected function initAsMaster( $port ) {

        $ctx = new ZMQContext();

        $client = $ctx->getSocket( ZMQ::SOCKET_XREP );
        $client->bind( 'tcp://*:' . $port );

        $workers = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $workers->bind( 'ipc://workers' );

        echo "Master on $port: push\n";

        $readable = $writable = array();
        
        while ( true ) {

            $poll = new ZMQPoll();
            $poll->add( $client, ZMQ::POLL_IN );
            $poll->add( $workers, ZMQ::POLL_IN );
            
            $poll->poll( $readable, $writable );

            foreach ( $readable as $socket ) {

                $zmsg = new Zmsg( $socket );
                $zmsg->recv();

                if ( $socket === $client ) {
                    echo "\nServer got job\n";
                    $zmsg->set_socket($workers)->send();
                }

                else if ( $socket === $workers ) {
                    echo "Job Complete - REPLY TO CLIENT\n";
                    $zmsg->set_socket( $client )->send();
                }
                
            }
            
        }

    }

}