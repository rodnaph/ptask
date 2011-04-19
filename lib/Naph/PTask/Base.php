<?php

namespace Naph\PTask;

/**
 * Base class with utility functions
 * 
 */
class Base {

    /**
     * Log a message
     *
     * @param string $class
     * @param string $message
     */
    protected function log( $class, $message ) {
        
        echo sprintf( "%s: %s\n", $class, $message );
        
    }

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
