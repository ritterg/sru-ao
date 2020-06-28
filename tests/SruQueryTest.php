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

    public function testWillThrowExceptionWhenFirstParamterIsNotArray()
    {
        $params = 'string';
        $this->expectException(FirstParameterIsNotArray::class);
        $result = $this->sruquery->getQueryParams($params);
    }

    public function testReturnsAnArray()
    {
        $params = [];
        $result = $this->sruquery->getQueryParams($params);
        $this->assertIsArray($result);
    }
    public function testReturnsCorrectParams()
    {
        $limit = 50;
        $params = [
            "maximumRecords" => $limit,
        ];
        $result = $this->sruquery->getQueryParams($params);
        $this->assertEquals($limit, $result['limit']);
    }
    public function testFiltersIncorrectParams()
    {
        $params = [
            "maximumRecords" => "string",
        ];
        $result = $this->sruquery->getQueryParams($params);
        $this->assertEquals([], $result);
    }
}
