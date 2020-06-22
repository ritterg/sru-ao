<?php

namespace Ritterg\SruAo;

/**
 * Class SruResponse
 *
 * @author  Gerold Ritter <ritter@e-hist.ch>
 */
class SruResponse
{

    /**
     * @var  \Ritterg\SruAo\Config
     */
    private $config;

    /**
     * Sample constructor.
     *
     * @param \Ritterg\SruAo\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param $results
     *
     * @return  xml string
     */
    public function composeSruResponse($results, $totalcount = null)
    {
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
        if ($totalcount !== null) {
            $root->appendChild($xmlDoc->createElement('numberOfRecords', $totalcount));
        } else {
            $root->appendChild($xmlDoc->createElement('numberOfRecords', count($results)));
        }
        $records = $root->appendChild($xmlDoc->createElement("records"));
        $i = 1;
        foreach ($results as $result) {
            $record = $records->appendChild($xmlDoc->createElement('record'));
            $record->appendChild($xmlDoc->createElement('recordSchema', 'isad'));
            $record->appendChild($xmlDoc->createElement('recordPacking', 'xml'));
            $recordData = $record->appendChild($xmlDoc->createElement('recordData'));
            $archivaldescription = $recordData->appendChild($xmlDoc->createElement('isad:archivaldescription'));
            $identity = $archivaldescription->appendChild($xmlDoc->createElement('isad:identity'));
            $context = $identity->appendChild($xmlDoc->createElement('isad:context'));
            $context->appendChild($xmlDoc->createElement('isad:creator'));
            $identity->appendChild($xmlDoc->createElement('isad:reference', $result['reference']));
            $title = $xmlDoc->createElement('isad:title');
            $title->appendChild($xmlDoc->createTextNode($result['title']));
            $identity->appendChild($title);
            $date = $xmlDoc->createElement('isad:date');
            $date->appendChild($xmlDoc->createTextNode($result['date']));
            $identity->appendChild($date);
            $identity->appendChild($xmlDoc->createElement('isad:descriptionlevel'));
            $identity->appendChild($xmlDoc->createElement('isad:extent'));

            $record->appendChild($xmlDoc->createElement('recordPosition', $i++));

            $extraRecordData = $record->appendChild($xmlDoc->createElement('extraRecordData'));
            $score = $extraRecordData->appendChild($xmlDoc->createElement('rel:score'));
            $score->setAttribute('xmlns:rel', 'info:srw/extension/2/relevancy-1.0');
            $link = $extraRecordData->appendChild($xmlDoc->createElement('ap:link'));
            $link->appendChild($xmlDoc->createTextNode($result['link']));
            $link->setAttribute('xmlns:ap', 'http://www.archivportal.ch/srw/extension/');
            $beginDateISO = $extraRecordData->appendChild($xmlDoc->createElement('ap:beginDateISO', $result['beginDateISO']));
            $beginDateISO->setAttribute('xmlns:ap', 'http://www.archivportal.ch/srw/extension/');
            $beginApprox = $extraRecordData->appendChild($xmlDoc->createElement('ap:beginApprox', 0));
            $beginApprox->setAttribute('xmlns:ap', 'http://www.archivportal.ch/srw/extension/');
            $endDateISO = $extraRecordData->appendChild($xmlDoc->createElement('ap:endDateISO', $result['endDateISO']));
            $endDateISO->setAttribute('xmlns:ap', 'http://www.archivportal.ch/srw/extension/');
            $endApprox = $extraRecordData->appendChild($xmlDoc->createElement('ap:endApprox', 0));
            $endApprox->setAttribute('xmlns:ap', 'http://www.archivportal.ch/srw/extension/');
        }

        //make the output pretty
        $xmlDoc->formatOutput = true;

        return $xmlDoc->saveXML();
    }
}
