# SRU Implementation for Archives Online

[![Latest Stable Version](https://poser.pugx.org/ritterg/sru-ao/v/stable)](https://packagist.org/packages/ritterg/sru-ao) 
[![License](https://poser.pugx.org/ritterg/sru-ao/license)](https://packagist.org/packages/ritterg/sru-ao)
[![Gerold Ritter](https://img.shields.io/badge/Author-Gerold%20Ritter-orange.svg)](http://www.e-hist.ch)



**SRU-AO** is a PHP & Laravel package to facilitate the implementation of an archives-online.org compatible SRU interfaces to your archival database.  

* Feed the archives-online.org query to the SruQuery::getQueryParams function and it will parse the query and return an array of fields and operators.
* Provide an array with query results to the SruResponse::composeSruResponse function and it will return a formatted an valid XML-Response you can serve to the archives-online.org query.


## Software Requirement
- Composer


## Installation Steps
Just require ritterg/sru-ao within your project

1. `composer require ritterg/sru-ao`
2. make sure everything is OK by running the tests `phpunit`


## Usage
### SruQuery
archives-online.org SRU queries come in a special format like
`https://server.tld/SRU/?operation=searchretrieve&version=1.2&query=Serverchoice%20all%20%22Switzerland%20Germany%22%20AND%20isad.date%20WITHIN%20%221000%202000%22&maximumRecords=50
`
The SruQuery class helps you to parse this query.

### SruResponse
```php
$sruresponse = new SruResponse;
$xml = $sruresponse->composeSruResponse($results, $totalcount, $keys);
```
`$xml` will be an XML string suitable to return to archives-online.org.

#### Parameters
`$results` is the array of records that you want to send to archives-online.org.

archives-online.org has 13 fields per record
* reference: the reference/signature of the archival unit
* title: the title of the archival unit
* date: the date string of the archival unit
* descriptionlevel: the level of description of the archival unit
* extent: the extent/size/count of the archival unit
* creator: the creator/author of the archival unit
* score: the relevance of the record regarding the search query (0 - 1)
* link: a direct link to the detail page for the archival unit
* beginDateISO: the start date of the archival unit in ISO format YYYY-MM-DD
* beginApprox: true if the start date is approximate, false if the date is exact
* endDateISO: the end date of the archival unit in ISO format YYYY-MM-DD
* endApprox: true if the start date is approximate, false if the date is exact
* hasDigitizedItems: true if the archival unit has digitized items attached, false otherwise (this field is optional)

`$totalcount` is the number of total results in your database.  
An SRU query contains a parameter "maximumRecords" to indicate how many results should be returned. If your query has more results, you can return the number of total results in $totalcount.

`$keys` is an array of alternative array keys. If your results have different keys than the standard SRU keys, you can add an array of keys to match your keys with the standard keys.

If you have e.g. German keys in your results array, a $keys array could look like this:
```php
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
```

## Test

To run the tests, run the following command from the project folder.

``` bash
$ ./vendor/bin/phpunit
```

## Links

- [archives-online.org](https://archives-online.org)

## Credits

- [Gerold Ritter](https://github.com/ritterg)


## License

The MIT License (MIT). See the [License File](https://github.com/ritterg/sru-ao/blob/master/LICENSE) for more information.
