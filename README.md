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
`https://server.tld/SRU?operation=searchretrieve&version=1.2&query=Serverchoice%20all%20%22Switzerland%20Germany%22%20AND%20isad.date%20WITHIN%20%221000%202000%22&maximumRecords=50` 
The SruQuery class helps you to parse this query.

```php
use Ritterg\SruAo\SruQuery;

$sruquery = new SruQuery;
$searchparams = $sruquery->getQueryParams($request, $allowedfields = null, $allowedoperators = null);
```
`$searchparams` will be an array with all the sanitized and renamed query parameters from the sru query.

#### Parameters
`$request` is the array of input parameters from the query (i.e. $_GET).

`$allowedfields` is an array of allowed query fields and the string they should be renamed to. Default values are

```php
$allowedfields = [
	'Serverchoice' => 'fulltext', 
	'isad.reference' => 'reference', 
	'isad.title' => 'title', 
	'isad.date' => 'date', 
];
```

`$allowedoperators` is an array of allowed operators and the string they should be rewritten to. Default values are

```php
$allowedoperators = [  
	'all' => 'AND',  
	'any' => 'OR',  
	'adj' => 'ADJ',  
	'=' => 'LIKE',  
	'==' => 'LIKE',  
	'===' => '=',  
	'WITHIN' => '='  
];
```

You can override these defaults with walues that best fit your database

#### Result
The result is an array with all query parameters. For each parameter, there is another array with the value and the operator.

For the query above the result would be

```php
$searchparams = [
  "fulltext" => [
    "value" => "Switzerland Germany"
    "operator" => "AND"
  ]
  "date_start" => [
    "value" => "1000-01-01"
    "operator" => ">="
  ]
  "date_end" => [
    "value" => "2000-12-31"
    "operator" => "<="
  ]
  "limit" => 50
]
```
Take these parameters and build the query for your database.

### SruResponse
```php
use Ritterg\SruAo\SruQuery;

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

Check your XML-response with [https://archives-online.org/srutest](https://archives-online.org/srutest)

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
