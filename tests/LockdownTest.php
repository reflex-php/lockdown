<?php

use Mockery as m;

class LockdownTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    
}