<?php

namespace Naph\PTask\Job;

/**
 * Standard implementation of a job definition
 * 
 */
class Standard implements \Naph\PTask\Job {

    /**
     * @var array internal data store
     */
    protected $data;

    /**
     * @var mixed result of processing job
     */
    protected $result;

    /**
     * Create a new blank job
     *
     */
    public function __construct() {
        
        $this->data = array();
        
    }

    /**
     * Set a job parameter
     *
     * @param string $name
     * @param mixed $value
     */
    public function setParam( $name, $value ) {

        $this->data[ $name ] = $value;

    }

    /**
     * Return the value of a job parameter
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParam( $name ) {

        return isset( $this->data[$name] )
            ? $this->data[ $name ]
            : null;

    }

    /**
     * Magic wrapper to enable property access to parameters
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get( $name ) {

        return $this->getParam( $name );
        
    }

    /**
     * Magic wrapper to enable property access to parameters
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value ) {

        $this->setParam( $name, $value );
        
    }

    /**
     * Sets the result of processing a job
     *
     * @param mixed $result
     */
    public function setResult( $result ) {
        
        $this->result = $result;
        
    }

    /**
     * Return the result of processing the job
     *
     * @return mixed
     */
    public function getResult() {
        
        return $this->result;

    }

}
