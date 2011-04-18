<?php

namespace Naph\PTask;

class Base {

    /**
     * Returns a new randomly generated id
     *
     * @return string
     */
    public function generateId() {
    
        $id = '';

        for ( $i=0; $i<10; $i++ ) {
            $id .= chr(rand(65,90));
        }

        return $id;

    }

}
