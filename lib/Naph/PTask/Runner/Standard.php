<?php

namespace Naph\PTask\Runner;

use Naph\PTask\Base;
use Naph\PTask\Runner;
use Naph\PTask\Server;

use ZMQ;
use ZMQContext;
use ZMQPoll;
use ZMQSocket;
use ZMQ\Zmsg;

/**
 * Runs jobs by sending them to the server, then waiting till they have all been
 * processed before returning them.  The processed jobs will have the job results
 * available via $job->getResults()
 *
 */
class Standard extends Base implements Runner {

    /**
     * Submit the specified jobs to the server listening on the specified port
     *
     * @param array $jobs
     * @param int $port
     *
     * @return array
     */
    public function run( array $jobs, $port=Server::DEFAULT_PORT ) {
        
        $ctx = new ZMQContext();
        $req = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $req->connect( 'tcp://localhost:' . $port );

        $this->log( __CLASS__, "Sending " . count($jobs) . " for processing" );

        $this->submitJobs( $req, $jobs );

        return $this->waitForReplies( $req, count($jobs) );

    }

    /**
     * Wait for replies from the server after the jobs have been processed and
     * then return an array of processed jobs.
     *
     * @param ZMQSocket $req
     * @param int $totalJobs
     *
     * @return array
     */
    protected function waitForReplies( ZMQSocket $req, $totalJobs ) {

        $completed = array();

        $read = $write = array();

        $poll = new ZMQPoll();
        $poll->add( $req, ZMQ::POLL_IN );

        while ( true ) {

            $events = $poll->poll( $read, $write, 10000 );

            if ( $events ) {
                
                $zmsg = new Zmsg( $req );
                $zmsg->recv();

                $this->log( __CLASS__, "Job complete reply received" );

                $job = unserialize( $zmsg->body() );

                $completed[] = $job;

                if ( count($completed) == $totalJobs ) {
                    $this->log( __CLASS__, "All jobs done" );
                    return $completed;
                }
                
            }

        }
        
    }

    /**
     * Submit the $jobs to the server to be processed
     *
     * @param ZMASocket $req
     *
     * @param array $jobs
     */
    protected function submitJobs( ZMQSocket $req, array $jobs ) {

        foreach ( $jobs as $job ) {

            $this->log( __CLASS__, "Sending job to server" );

            $zmsg = new Zmsg( $req );
            $zmsg->body_set(serialize( $job ))
                 ->send();

        }

    }

}
