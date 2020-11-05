<?php

namespace Ritterg\SruAo;

use Ritterg\SruAo\Exceptions\FirstParameterIsNotArray;
use Ritterg\SruAo\Exceptions\SecondParameterIsNotInt;
use Ritterg\SruAo\Exceptions\ThirdParameterIsNotValid;

/**
 * Class SruResponse
 *
 * @author  Gerold Ritter <ritter@e-hist.ch>
 */
class SruResponse
{

    protected $keys;

    public function __construct()
    {
        $this->keys = [
            'reference' => 'reference',
            'title' => 'title',
            'date' => 'date',
            'descriptionlevel' => 'descriptionlevel',
            'extent' => 'extent',
            'creator' => 'creator',
            'score' => 'score',
            'link' => 'link',
            'beginDateISO' => 'beginDateISO',
            'beginApprox' => 'beginApprox',
            'endDateISO' => 'endDateISO',
            'endApprox' => 'endApprox',
            'hasDigitizedItems' => 'hasDigitizedItems',
        ];
    }

    /**
     * @param $results
     *
     * @return  xml string
     */
    public function composeSruResponse($results, $totalcount = null, $keys = null)
    {
        // test if first param is array
        if (!is_array($results)) {
            throw new FirstParameterIsNotArray("First parameter must be an array.");
        }
        // test if second param is present and if yes, is it an integer
        if ($totalcount !== null && !is_int($totalcount)) {
            throw new SecondParameterIsNotInt("Second parameter must be null or an integer.");
        }

        // test if third param is present and if yes, is it an array with the same number of items as $this->key
        if ($keys !== null) {
            if (is_array($keys) && count($keys) == count($this->keys)) {
                // replace internal keys array with $keys parameter if $keys is valid
                $this->keys = $keys;
            } else {
                throw new ThirdParameterIsNotValid("Third parameter must be null or an array with " . count($this->keys) . " items.");
            }
        }

        // Output
        //create the xml document
        $xmlDoc = new \DOMDocument();
        $xmlDoc->version = "1.0";
        $xmlDoc->encoding = "utf-8";
        $xmlDoc->standalone = 1;

        //create the root element
        $root = $xmlDoc->appendChild($xmlDoc->createElement("searchRetrieveResponse"));
        $root->setAttribute('xmlns', 'http://www.loc.gov/zing/srw/');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xmlns:isad', 'http://www.expertisecentrumdavid.be/xmlschemas/isad.xsd');
        $root->setAttribute('xsi:schemaLocation', 'http://www.loc.gov/zing/srw/ http://www.loc.gov/standards/sru/sru1-1archive/xml-files/srw-types.xsd');

        $root->appendChild($xmlDoc->createElement('version', '1.2'));
        // if there is a separate count of total results (i.e. not restricted by maximumRecords in query and $totalcount is > count($results), then show $totalcount, else show count($results))
        $resultscount = count($results);
        if ($totalcount !== null && $totalcount > $resultscount) {
            $root->appendChild($xmlDoc->createElement('numberOfRecords', $totalcount));
        } else {
            $root->appendChild($xmlDoc->createElement('numberOfRecords', $resultscount));
        }
        // base elements for all records
        $records = $root->appendChild($xmlDoc->createElement("records"));
        $i = 1;
        // each record
        foreach ($results as $result) {
            $record = $records->appendChild($xmlDoc->createElement('record'));
            $record->appendChild($xmlDoc->createElement('recordSchema', 'isad'));
            $record->appendChild($xmlDoc->createElement('recordPacking', 'xml'));
            $recordData = $record->appendChild($xmlDoc->createElement('recordData'));
            $archivaldescription = $recordData->appendChild($xmlDoc->createElement('isad:archivaldescription'));
            $identity = $archivaldescription->appendChild($xmlDoc->createElement('isad:identity'));
            $context = $identity->appendChild($xmlDoc->createElement('isad:context'));

            $this->appendChild($xmlDoc, 'isad:reference', $identity, $result, 'reference');
            $this->appendChild($xmlDoc, 'isad:title', $identity, $result, 'title');
            $this->appendChild($xmlDoc, 'isad:date', $identity, $result, 'date');
            $this->appendChild($xmlDoc, 'isad:descriptionlevel', $identity, $result, 'descriptionlevel');
            $this->appendChild($xmlDoc, 'isad:extent', $identity, $result, 'extent');
            $this->appendChild($xmlDoc, 'isad:creator', $context, $result, 'creator');

            $record->appendChild($xmlDoc->createElement('recordPosition', $i++));
            $extraRecordData = $record->appendChild($xmlDoc->createElement('extraRecordData'));
            $score = $extraRecordData->appendChild($xmlDoc->createElement('rel:score'));
            $score->setAttribute('xmlns:rel', 'info:srw/extension/2/relevancy-1.0');
            if (isset($result['score'])) {
                $score->appendChild($xmlDoc->createTextNode($result['score']));
            }
            $this->addExtraRecordData($xmlDoc, 'ap:link', $extraRecordData, $result, 'link');
            $this->addExtraRecordData($xmlDoc, 'ap:beginDateISO', $extraRecordData, $result, 'beginDateISO');
            $this->addExtraRecordData($xmlDoc, 'ap:beginApprox', $extraRecordData, $result, 'beginApprox');
            $this->addExtraRecordData($xmlDoc, 'ap:endDateISO', $extraRecordData, $result, 'endDateISO');
            $this->addExtraRecordData($xmlDoc, 'ap:endApprox', $extraRecordData, $result, 'endApprox');
            $this->appendChild($xmlDoc, 'ap:hasDigitizedItems', $extraRecordData, $result, 'hasDigitizedItems');
        }
        // end each record
        //make the output pretty
        $xmlDoc->formatOutput = true;

        // return formatted xml
        return $xmlDoc->saveXML();
    }

    private function appendChild($xmlDoc, $fieldname, $parent, $result, $key)
    {
        $key = $this->keys[$key];
        if (isset($result[$key])) {
            $element = $xmlDoc->createElement($fieldname);
            $element->appendChild($xmlDoc->createTextNode($result[$key]));
            $parent->appendChild($element);
        } else {
            $parent->appendChild($xmlDoc->createElement($fieldname));
        }
    }

    private function addExtraRecordData($xmlDoc, $fieldname, $parent, $result, $key)
    {
        $element = $parent->appendChild($xmlDoc->createElement($fieldname));
        $element->setAttribute('xmlns:ap', 'http://www.archivportal.ch/srw/extension/');
        if (isset($result[$key])) {
            $element->appendChild($xmlDoc->createTextNode($result[$key]));
        }
    }
}