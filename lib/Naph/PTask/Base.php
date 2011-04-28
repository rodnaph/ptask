<?php

namespace Naph\PTask;

/**
 * Base class with utility functions
 * 
 */
class Base {

    /**
     * @var bool indicates if logging is enabled
     */
    protected $loggingEnabled = false;

    /**
     * Log a message
     *
     * @param string $class
     * @param string $message
     */
    protected function log( $class, $message ) {

        if ( $this->loggingEnabled ) {
            echo sprintf( "%s: %s\n", $class, $message );
        }
        
    }

    /**
     * Enables or disables logging
     *
     * @param bool $loggingEnabled
     */
    public function setLoggingEnabled( $loggingEnabled ) {

        $this->loggingEnabled = (bool) $loggingEnabled;

    }

    /**
     * Returns a new randomly generated id
     *
     * @return string
     */
    protected function generateId() {

        $id = '';

        for ( $i=0; $i<10; $i++ ) {
            $id .= chr(rand(65,90));
        }

        return $id;

    }

}
