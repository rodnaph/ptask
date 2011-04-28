<?php

namespace Naph\PTask\Processor;

use Naph\PTask\Base;
use Naph\PTask\Processor;

/**
 * Standard processor class that can be used as a base class as it provides
 * default implementation of some optional methods in the interface.
 *
 */
abstract class Standard extends Base implements Processor {

    /**
     * By default no initialisation is done, this can then be overridden by
     * subclasses.
     *
     */
    public function init() {}

}
