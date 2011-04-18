<?php

namespace Naph\PTask\Runner;

use Naph\PTask\Base;
use Naph\PTask\Runner;

use ZMQ;
use ZMQContext;

class Standard extends Base implements Runner {

    public function run( array $jobs, $port ) {
        
        $id = $this->generateId();

        $ctx = new ZMQContext();
        $req = $ctx->getSocket( ZMQ::SOCKET_REQ );
        //$req->setSockOpt( ZMQ::SOCKOPT_IDENTITY, $id );
        $req->connect( 'tcp://localhost:' . $port );
        
        $req->send(serialize( $jobs ));
        
        return unserialize( $req->recv() );
        
    }

}
