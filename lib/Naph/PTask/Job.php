<?php

namespace Naph\PTask;

/**
 * Interface for parrallel jobs run in ptask.  Jobs can have arbitrary parameters
 * set on them by either using the get/setParam method or as properties directly.
 * The job processor can then use these parameters to work out what the job is
 * meant to do.
 *
 * When the job has been processed the result should be set using the setResult()
 * method, this data can then be accessed by the client application.
 *
 * Jobs that throw an error during processing will be returned as null.
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
