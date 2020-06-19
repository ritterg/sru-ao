<?php

namespace Ritterg\SruAo\Tests;

use Ritterg\SruAo\Config;
use Ritterg\SruAo\Sample;

/**
 * Class SampleTest
 *
 * @category Test
 * @package  Ritterg\SruAo\Tests
 * @author   Gerold Ritter <ritter@e-hist.ch>
 */
class SampleTest extends TestCase
{

    public function testSayHello()
    {
        $config = new Config();
        $sample = new Sample($config);

        $name = 'Gerold Ritter';

        $result = $sample->sayHello($name);

        $expected = $config->get('greeting') . ' ' . $name;

        $this->assertEquals($result, $expected);

    }

}
