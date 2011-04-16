<?php

namespace Naph\PTask\Server;

use ZMQ;
use ZMQContext;
use ZMQPoll;

/**
 * Standard implementation of a server which starts worker threads ready to process
 * jobs, then listens for requests to run jobs, passing them back when complete.
 * 
 */
class Standard implements \Naph\PTask\Server {

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
                        echo "Worker quit, restarting...\n";
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

        $pullPort = $port + 1;
        $pushPort = $port + 2;

        echo "Worker #$id: pull: $pullPort, push: $pushPort\n";

        $ctx = new ZMQContext( 1 );

        $pull = $ctx->getSocket( ZMQ::SOCKET_PULL );
        $pull->connect( 'tcp://127.0.0.1:' . $pullPort );

        $push = $ctx->getSocket( ZMQ::SOCKET_PUSH );
        $push->connect( 'tcp://127.0.0.1:' . $pushPort );

        while ( true ) {

            $job = unserialize( $pull->recv() );

            echo "#$id Processing\n";

            sleep( rand( 1, 2 ) );

            $job->setResult( 'PROCESSED BY WORKER' );

            echo "#$id Done\n";

            $push->send(serialize( $job ));
            
        }

        echo "## Worked Finished\n";
        exit;

    }

    protected function initAsMaster( $port ) {

        $pushPort = $port + 1;
        $pullPort = $port + 2;

        $ctx = new ZMQContext( 1 );

        $req = $ctx->getSocket( ZMQ::SOCKET_REP );
        $req->bind( 'tcp://*:' . $port );

        $push = $ctx->getSocket( ZMQ::SOCKET_PUSH );
        $push->bind( 'tcp://*:' . $pushPort );

        $pull = $ctx->getSocket( ZMQ::SOCKET_PULL );
        $pull->bind( 'tcp://*:' . $pullPort );

        $poll = new ZMQPoll();
        $poll->add( $req, ZMQ::POLL_IN );
        $poll->add( $pull, ZMQ::POLL_IN );

        echo "Master on $port: push: $pushPort, pull: $pullPort\n";

        $readable = $writable = array();

        while ( true ) {
            
            $events = $poll->poll( $readable, $writable );

            if ( $events > 0 ) {

                foreach ( $readable as $socket ) {
                    if ( $socket == $req ) {

                        //
                        //  JOBS REQUEST RECEIVED !!!
                        //
                        
                        $jobs = unserialize( $req->recv() );
                        echo "\nGot " . count($jobs) . " jobs\n";
                        $req->send(serialize( $jobs ));

                        foreach ( $jobs as $job ) {
                            $push->send(serialize( $job ));
                        }

                    }

                    else if ( $socket == $pull ) {

                        //
                        //  WORKER HAS COMPLETED !!!
                        //

                        $pull->recv();
                        echo "** GOT PULL RESPONSE FROM WORKER !!!\n";

                    }
                }

            }
            
        }

        exit;

        while ( true ) {
            
            $jobs = unserialize( $req->recv() );
            
            echo "\n\nGot " . count($jobs) . " jobs\n";

            foreach ( $jobs as $job ) {
                echo "Processing Job\n";
                $push->send(serialize( $job ));
            }
            
            echo "Sending Response\n";

            $req->send(serialize( $jobs ));
            
        }

        echo "Master Finished\n";
        exit;

    }

}