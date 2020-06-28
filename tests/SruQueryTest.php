<?php

namespace Ritterg\SruAo\Tests;

use Ritterg\SruAo\SruQuery;
use Ritterg\SruAo\Exceptions\FirstParameterIsNotArray;

/**
 * Class SruQueryTest
 *
 * @category Test
 * @package  Ritterg\SruAo\Tests
 * @author   Gerold Ritter <ritter@e-hist.ch>
 */
class SruQueryTest extends TestCase
{
    protected $sruquery;

    public function setUp() : void
    {
        parent::setUp();
        $this->sruquery = new SruQuery;
    }

    public function testGetQueryWithNoRecords()
    {
        $params = [];
        $result = $this->sruquery->getQuery($params);
        $this->assertIsArray($result);
    }
    public function testWillThrowExceptionWhenFirstParamterIsNotArray()
    {
        $params = 'string';
        $this->expectException(FirstParameterIsNotArray::class);
        $result = $this->sruquery->getQuery($params);
    }

}
