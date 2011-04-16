<?php

namespace Naph\PTask;

/**
 * Interface for parrallel jobs run in ptask
 * 
 */
interface Job {

    /**
     * Sets a job parameter
     *
     * @param string $name
     * @param string $value
     */
    public function setParam( $name, $value );

    /**
     * Returns the value of a job parameter
     *
     * @param string $name
     *
     * @return string
     */
    public function getParam( $name );

    /**
     * Sets the result of processing the job
     *
     * @param mixed $result
     */
    public function setResult( $result );

    /**
     * Returns the result of processing the job
     *
     * @return mixed
     */
    public function getResult();

}
