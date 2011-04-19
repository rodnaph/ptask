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

        echo "Worker #$id: pull: $pullPort, push: $pushPort\n";

        $ctx = new ZMQContext( 1 );

        $worker = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $worker->connect( 'ipc://workers' );

        $zmsg = new ZMsg( $worker );

        while ( true ) {

            $zmsg->recv();

            $job = unserialize( $zmsg->body() );

            echo "#$id Processing for " . $zmsg->address() . "\n";

            $this->processor->process( $job );

            echo "#$id Done\n";

            $zmsg->send(serialize( $job ));
            
        }

        echo "## Worked Finished\n";
        exit;

    }

    protected function initAsMaster( $port ) {

        $ctx = new ZMQContext( 1 );

        $client = $ctx->getSocket( ZMQ::SOCKET_XREP );
        $client->bind( 'tcp://*:' . $port );

        $workers = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $workers->bind( 'ipc://workers' );

        echo "Master on $port: push\n";

        $readable = $writable = array();

        $poll = new ZMQPoll();
        $poll->add( $client, ZMQ::POLL_IN );
        $poll->add( $workers, ZMQ::POLL_IN );

        $addy = array();

        $i = 0;

        while ( true ) {

            $poll->poll( $readable, $writable );

            foreach ( $readable as $socket ) {

                $zmsg = new Zmsg( $socket );
                $zmsg->recv();

                if ( $socket === $client ) {

                    $zmsg->set_socket( $workers );


                    //
                    //  JOBS REQUEST RECEIVED !!!
                    //

                    $address = $zmsg->address();
                    $jobs = unserialize( $zmsg->body() );

                    $addy[ $address ] = array( 'jobs' => count($jobs) );

                    echo "\nGot " . count($jobs) . " jobs\n";

                    $zmsg->body_set(serialize( $jobs[0] ));
                    $zmsg->send();

                }

                else if ( $socket === $workers ) {

                    $i++;
echo $zmsg->__toString();
                    $data = $zmsg->body();
//print_r( $data );
            if ( $i == 3 ) {

                    $zmsg->set_socket( $client );
                    $zmsg->body_set( serialize(array(1,2,3)) );
                    $zmsg->send();
            }

                    //
                    //  WORKER HAS COMPLETED !!!
                    //

                    //$pull->recv();
                    echo "** GOT PULL RESPONSE FROM WORKER !!!\n";

                }
            }
            
        }

    }

}