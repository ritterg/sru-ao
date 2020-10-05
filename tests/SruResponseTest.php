<?php

namespace Ritterg\SruAo\Tests;

use Ritterg\SruAo\SruResponse;
use Ritterg\SruAo\Exceptions\FirstParameterIsNotArray;
use Ritterg\SruAo\Exceptions\SecondParameterIsNotInt;
use Ritterg\SruAo\Exceptions\ThirdParameterIsNotValid;

/**
 * Class SruResponseTest
 *
 * @category Test
 * @package  Ritterg\SruAo\Tests
 * @author   Gerold Ritter <ritter@e-hist.ch>
 */
class SruResponseTest extends TestCase
{
    protected $sruresponse;

    public function setUp(): void
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
            'reference' => 'reference',
            'title' => 'title',
            'date' => 'date',
            'descriptionlevel' => 'descriptionlevel',
            'extent' => 'extent',
            'creator' => 'creator',
            'score' => 1,
            'link' => 'https://link.net',
            'beginDateISO' => '2020-01-01',
            'beginApprox' => 1,
            'endDateISO' => '2020-01-01',
            'endApprox' => 0,
            'hasDigitizedItems' => 1,
        ]];
        $result = $this->sruresponse->composeSruResponse($records);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/testfiles/' . 'oneresult.xml', $result);
    }

    public function testComposeSruWithOneRecordAndNonStandardFieldlabels()
    {
        $records = [[
            'signatur' => 'reference',
            'titel' => 'title',
            'datum' => 'date',
            'stufe' => 'descriptionlevel',
            'umfang' => 'extent',
            'autor' => 'creator',
            'relevanz' => 1,
            'url' => 'https://link.net',
            'anfangsdatum' => '2020-01-01',
            'anfangca' => 1,
            'enddatum' => '2020-01-01',
            'endca' => 0,
            'digitalisate' => 1,
        ]];
        $totalcount = 1;
        $keys = [
            'reference' => 'signatur',
            'title' => 'titel',
            'date' => 'datum',
            'descriptionlevel' => 'stufe',
            'extend' => 'umfang',
            'creator' => 'autor',
            'score' => 'relevanz',
            'link' => 'url',
            'beginDateISO' => 'anfangsdatum',
            'beginApprox' => 'anfangca',
            'endDateISO' => 'enddatum',
            'endApprox' => 'endca',
            'hasDigitizedItems' => 'digitalisate',
        ];
        $result = $this->sruresponse->composeSruResponse($records, $totalcount, $keys);
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

    public function testWillThrowExceptionWhenThirdParamterIsNotValid()
    {
        $records = [[]];
        $totalcount = 100;
        $keys = [];
        $this->expectException(ThirdParameterIsNotValid::class);
        $result = $this->sruresponse->composeSruResponse($records, $totalcount, $keys);
    }
}