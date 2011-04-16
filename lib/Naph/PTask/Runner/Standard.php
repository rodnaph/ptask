<?php

namespace Naph\PTask\Runner;

class Standard implements \Naph\PTask\Runner {

    public function run( array $jobs, $port ) {
        
        $ctx = new \ZMQContext();
        $req = $ctx->getSocket( \ZMQ::SOCKET_REQ );
        $req->connect( 'tcp://localhost:' . $port );
        
        $req->send(serialize( $jobs ));
        
        return unserialize( $req->recv() );
        
    }

}
