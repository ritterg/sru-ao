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
    public function testComposeSruWithNoRecords()
    {
        $records = [];
        $sruresponse = new SruResponse;
        $result = $sruresponse->composeSruResponse($records);
        $expected = file_get_contents(__DIR__ . '/testfiles/' . 'noresults.xml');
        $this->assertEquals($result, $expected);
    }

    public function testComposeSruWithEmptyRecord()
    {
        $records = [[]];
        $sruresponse = new SruResponse;
        $result = $sruresponse->composeSruResponse($records);
        $expected = file_get_contents(__DIR__ . '/testfiles/' . 'emptyresult.xml');
        $this->assertEquals($result, $expected);
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
        $sruresponse = new SruResponse;
        $result = $sruresponse->composeSruResponse($records);
        $expected = file_get_contents(__DIR__ . '/testfiles/' . 'oneresult.xml');
        $this->assertEquals($result, $expected);
    }

    public function testComposeSruWithTotalCount()
    {
        $records = [[]];
        $totalcount = 500;
        $sruresponse = new SruResponse;
        $result = $sruresponse->composeSruResponse($records, $totalcount);
        $expected = "<numberOfRecords>" . $totalcount . "</numberOfRecords>";
        $this->assertStringContainsString($expected, $result);
    }

    public function testWillThrowExceptionWhenFirstParamterIsNotArray()
    {
        $records = 'string';
        $this->expectException(FirstParameterIsNotArray::class);
        $sruresponse = new SruResponse;
        $result = $sruresponse->composeSruResponse($records);
    }

    public function testWillThrowExceptionWhenSecondParamterIsNotInt()
    {
        $records = [[]];
        $totalcount = "string";
        $this->expectException(SecondParameterIsNotInt::class);
        $sruresponse = new SruResponse;
        $result = $sruresponse->composeSruResponse($records, $totalcount);
    }
}
