<?php

namespace Ritterg\SruAo;

use Ritterg\SruAo\Exceptions\FirstParameterIsNotArray;

/**
 * Class SruQuery
 *
 * @author  Gerold Ritter <ritter@e-hist.ch>
 */
class SruQuery
{
    protected $allowedfields;
    protected $allowedoperators;

    public function __construct()
    {
        $this->allowedfields = [
            'Serverchoice' => 'fulltext',
            'isad.reference' => 'reference',
            'isad.title' => 'title',
            'isad.date' => 'date',
        ];
        $this->allowedoperators = [
            'all' => 'AND',
            'any' => 'OR',
            'adj' => 'ADJ',
            '=' => 'LIKE',
            '==' => 'LIKE',
            '===' => '=',
            'WITHIN' => '='
        ];
    }
    /**
     * @param $inputparams from GET query string
     *
     * @return  array with sanitized query paramaters
     */
    public function getQueryParams($inputparams, $allowedfields = null, $allowedoperators = null)
    {
        if ($allowedfields === null) {
            $allowedfields = $this->allowedfields;
        }
        if ($allowedoperators === null) {
            $allowedoperators = $this->allowedoperators;
        }
        // test if first param is array
        if (!is_array($inputparams)) {
            throw new FirstParameterIsNotArray("First parameter must be an array.");
        }
        $queryparams = [];

        /* get param "query" contains the main sru query. */
        /* Archives Online supports only AND */
        if (isset($inputparams['query'])) {
            $query = rawurldecode($inputparams['query']);
            $subqueries = explode('AND', $query);

            $query_array = [];
            foreach ($subqueries as $subquery) {
                preg_match_all('/(.*) (all|any|adj|=|==|===|WITHIN) "(.*)"/', $subquery, $parts_array, PREG_PATTERN_ORDER);
                if (count($parts_array) == 4 && trim($parts_array[3][0]) !== '""' && array_key_exists(trim($parts_array[1][0]), $allowedfields)) {
                    $originalkey = trim($parts_array[1][0]);
                    $key = $allowedfields[$originalkey];
                    $operator = $allowedoperators[trim($parts_array[2][0])];
                    $value = trim($parts_array[3][0]);
                    if ($originalkey == 'isad.date') {
                        $date_parts = explode(" ", $value);
                        $queryparams[$key . '_start'] = ['value' => $date_parts[0] . "-01-01", 'operator' => '>='];
                        $queryparams[$key . '_end'] = ['value' => $date_parts[1] . "-12-31", 'operator' => '<='];
                    } else {
                        $queryparams[$key] = ['value' => $value, 'operator' => $operator];
                    }
                }
            }

            if (isset($inputparams['maximumRecords'])) {
                $value = filter_var($inputparams['maximumRecords'], FILTER_VALIDATE_INT);
                if ($value) {
                    $queryparams['limit'] = ['value' => $value, 'operator' => '='];
                } else {
                    // 50 is the default limit for Archives Online
                    $queryparams['limit'] = 50;
                }
            } else {
                // 50 is the default limit for Archives Online
                $queryparams['limit'] = 50;
            }

            // return array with sanitized query paramaters
            return $queryparams;
        } else {
            return [];
        }
    }
}