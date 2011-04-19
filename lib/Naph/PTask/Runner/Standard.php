<?php

namespace Naph\PTask\Runner;

use Naph\PTask\Base;
use Naph\PTask\Runner;

use ZMQ;
use ZMQPoll;
use ZMQContext;
use ZMQ\Zmsg;

class Standard extends Base implements Runner {

    public function run( array $jobs, $port ) {
        
        $id = $this->generateId();
        $completed = array();

        $ctx = new ZMQContext();
        $req = $ctx->getSocket( ZMQ::SOCKET_XREQ );
        $req->connect( 'tcp://localhost:' . $port );

        foreach ( $jobs as $job ) {
            echo "SEND JOB\n";
            $zmsg = new Zmsg( $req );
            $zmsg->body_set(serialize( $job ))
                 ->send();
        }

        $read = $write = array();

        $poll = new ZMQPoll();
        $poll->add( $req, ZMQ::POLL_IN );

        while ( true ) {

            $req->send(serialize( $jobs ));

            $events = $poll->poll( $read, $write, 10000 );

            if ( $events ) {
                
                $zmsg = new Zmsg( $req );
                $zmsg->recv();

                echo "Client got job reply!\n";

                $job = unserialize( $zmsg->body() );

                $completed[] = $job;

                if ( count($completed) == count($jobs) ) {
                    echo "All jobs done\n";
                    return $completed;
                }
                
            }

        }
        
    }

}
