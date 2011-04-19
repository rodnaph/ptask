<?php

namespace Naph\PTask\Server;

use Naph\PTask\Base;
use Naph\PTask\Processor;
use Naph\PTask\Server;

use ZMQ;
use ZMQContext;
use ZMQPoll;
use ZMQ\ZMsg;

/**
 * Standard implementation of a server which starts worker threads ready to process
 * jobs, then listens for requests to run jobs, passing them back when complete.
 * 
 */
class Standard extends Base implements Server {

    /**
     * Create a new task server
     * 
     * @param Processor $processor
     */
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
    public function listen( $port=Server::DEFAULT_PORT, $workerCount=Server::DEFAULT_WORKERS ) {

        $this->log( __CLASS__, "Starting server on port $port with $workerCount workers" );

        for ( $i=1; $i<=$workerCount; $i++ ) {

            $pid = pcntl_fork();

            switch ( $pid ) {

                case -1:
                    $this->log( __CLASS__, 'Forking workder process failed :(' );
                    exit;
                    break;

                case 0:
                    $this->initAsWorker( $port );
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

        $id = $this->generateId();

        $this->log( __CLASS__, "Worker #$id started" );

        $ctx = new ZMQContext();

        $worker = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $worker->connect( 'ipc://workers' );

        $zmsg = new ZMsg( $worker );

        while ( true ) {

            $zmsg->recv();

            $this->log( __CLASS__, "Worker $id received job" );

            $job = unserialize( $zmsg->body() );

            $this->processor->process( $job );

            $this->log( __CLASS__, "Worker #$id finished processing job" );

            $zmsg->body_set(serialize( $job ));
            $zmsg->send();
            
        }

    }

    /**
     * Initialise the master server that dispatches job to the worker processes
     *
     * @param int $port
     */
    protected function initAsMaster( $port ) {

        $ctx = new ZMQContext();

        $client = $ctx->getSocket( ZMQ::SOCKET_XREP );
        $client->bind( 'tcp://*:' . $port );

        $workers = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $workers->bind( 'ipc://workers' );

        $this->log( __CLASS__, "Master listening on port $port" );

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
                    $this->log( __CLASS__, "Master sending job to worker" );
                    $zmsg->set_socket($workers)->send();
                }

                else if ( $socket === $workers ) {
                    $this->log( __CLASS__,  "Master sending reply to client" );
                    $zmsg->set_socket( $client )->send();
                }
                
            }
            
        }

    }

}
