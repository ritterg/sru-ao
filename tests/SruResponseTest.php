<?php

namespace Ritterg\SruAo\Tests;

use Ritterg\SruAo\SruResponse;
use Ritterg\SruAo\Exceptions\FirstParameterIsNotArray;
use Ritterg\SruAo\Exceptions\SecondParameterIsNotInt;

/**
 * Class SampleTest
 *
 * @category Test
 * @package  Ritterg\SruAo\Tests
 * @author   Gerold Ritter <ritter@e-hist.ch>
 */
class SruResponseTest extends TestCase
{
    protected $sruresponse;

    public function setUp() : void
    {
        parent::setUp();
        $this->sruresponse = new SruResponse;
    }

    public function testComposeSruWithNoRecords()
    {
        $records = [];
        $result = $this->sruresponse->composeSruResponse($records);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/testfiles/' . 'noresults.xml', $result);
    }

    public function testComposeSruWithEmptyRecord()
    {
        $records = [[]];
        $result = $this->sruresponse->composeSruResponse($records);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/testfiles/' . 'emptyresult.xml', $result);
    }

    public function testComposeSruWithOneRecord()
    {
        $records = [[
            'creator' => 'creator',
            'title' => 'title',
            'date' => 'date',
            'descriptionlevel' => 'descriptionlevel',
            'extent' => 'extent',
            'score' => 1,
            'link' => 'https://link.net',
            'beginDateISO' => '2020-01-01',
            'beginApprox' => 1,
            'endDateISO' => '2020-01-01',
            'endApprox' => 0,
        ]];
        $result = $this->sruresponse->composeSruResponse($records);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/testfiles/' . 'oneresult.xml', $result);
    }

    public function testComposeSruWithTotalCount()
    {
        $records = [[]];
        $totalcount = 500;
        $result = $this->sruresponse->composeSruResponse($records, $totalcount);
        $expected = "<numberOfRecords>" . $totalcount . "</numberOfRecords>";
        $this->assertStringContainsString($expected, $result);
    }

    public function testWillThrowExceptionWhenFirstParamterIsNotArray()
    {
        $records = 'string';
        $this->expectException(FirstParameterIsNotArray::class);
        $result = $this->sruresponse->composeSruResponse($records);
    }

    public function testWillThrowExceptionWhenSecondParamterIsNotInt()
    {
        $records = [[]];
        $totalcount = "string";
        $this->expectException(SecondParameterIsNotInt::class);
        $result = $this->sruresponse->composeSruResponse($records, $totalcount);
    }
}
