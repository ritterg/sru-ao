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
        $fulltextstring = "fulltextstring";
        $reference = "reference";
        $title = "title";
        $unknown = "unknown";
        $date_start = "1000";
        $date_end = "2000";
        $limit = "50";
        $params = [
            "query" => 'Serverchoice all "'.$fulltextstring.'" AND isad.reference == "'.$reference.'" AND isad.title === "'.$title.'" AND isad.unknown === "'.$unknown.'" AND isad.date WITHIN "'.$date_start.' '.$date_end.'"',
            /* "query" => 'Serverchoice%20all%20%22Regierungsrat%22%20AND%20isad.date%20WITHIN%20%221000%202000%22', */
            "maximumRecords" => $limit,
        ];
        $result = $this->sruquery->getQueryParams($params);
        $this->assertEquals(['value' => $fulltextstring, 'operator' => 'AND'], $result['fulltext']);
        $this->assertEquals(['value' => $reference, 'operator' => 'LIKE'], $result['reference']);
        $this->assertEquals(['value' => $date_start."-01-01", 'operator' => '>='], $result['date_start']);
        $this->assertEquals(['value' => $date_end."-12-31", 'operator' => '<='], $result['date_end']);
        $this->assertArrayNotHasKey($unknown, $result);
        $this->assertEquals(['value' => $limit, 'operator' => '='], $result['limit']);
    }
    public function testAcceptsCustomParams()
    {
        $customkey = "custom.key";
        $customvalue = "customvalue";
        $customoperator = "adj";
        $customfields = [$customkey => 'key'];
        $customoperators = ["adj" => "near"];
        $params = [
            "query" => $customkey .' '.$customoperator.' "'.$customvalue .'"',
        ];
        $result = $this->sruquery->getQueryParams($params, $customfields, $customoperators);
        $this->assertEquals(['value' => $customvalue, 'operator' => 'near'], $result['key']);
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
