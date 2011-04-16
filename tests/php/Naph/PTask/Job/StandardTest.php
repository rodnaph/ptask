<?php

namespace Naph\PTask\Job;

require_once 'tests/php/bootstrap.php';

class StandardTest extends \PHPUnit_Framework_TestCase {

    private $job;
    
    public function setUp() {
        $this->job = new Standard();
    }

    public function testParametersCanBeSetAndRetreivedFromJob() {
        $this->job->setParam( 'foo', 'bar' );
        $this->assertEquals( 'bar', $this->job->getParam('foo') );
    }

    public function testGettingUnknownParametersReturnNull() {
        $this->assertNull( $this->job->getParam('foo') );
        $this->assertNull( $this->job->foo );
    }

    public function testParametersCanBeGottenAsProperties() {
        $this->job->setParam( 'foo', 'bar' );
        $this->assertEquals( 'bar', $this->job->foo );
    }

    public function testParametersCanBeSetAsProperties() {
        $this->job->foo = 'bar';
        $this->assertEquals( 'bar', $this->job->getParam('foo') );
        $this->assertEquals( 'bar', $this->job->foo );
    }

}
