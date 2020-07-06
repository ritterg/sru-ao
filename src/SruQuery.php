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
    /**
     * @param $inputparams from GET query string
     *
     * @return  array with sanitized query paramaters
     */
    public function getQueryParams($inputparams)
    {
        // test if first param is array
        if (!is_array($inputparams)) {
            throw new FirstParameterIsNotArray("First parameter must be an array.");
        }
        $queryparams = [];

        /* get param "query" contains the main sru query.
        Archives Online supports only AND */
        if (isset($inputparams['query'])) {
            $query_param = rawurldecode($inputparams['query']);
            $query_parts = explode('AND', $query_param);

            $query_array = [];
        }
        /*
        foreach ($query_parts as $part) {
            $part = str_replace(' WITHIN ', ' = ', $part);
            $part = str_replace(' all ', ' = ', $part);
            $parts_array = explode(' = ', $part);
            if (count($parts_array) == 2 && trim($parts_array[1]) !== '""') {
                $query_array[trim($parts_array[0])] = trim($parts_array[1], ' "');
            }
        }

        foreach ($query_array as $key => $value) {
            if (in_array($key, $allowedfields)) {
                $query->where($key, 'LIKE', $value . '%');
            }
            if ($key == 'Serverchoice') {
                $fields_to_search = $allowedfields;
                $words = explode(' ', $value);
                foreach ($words as $word) {
                    $query->where(function ($query) use($word, $fields_to_search) {
                        $where = 'where';
                        foreach ($fields_to_search as $field) {
                            $query->{$where}($field, 'LIKE', '%' . $word . '%');
                            $where = 'orWhere';
                        }
                    });
                }
            }
            if ($key == 'date') {
                $date = date("Y-m-d", strtotime($value));
                $query->where('endDateISO', '>=', $date);
                $query->where('beginDateISO', '<=', $date);
            }
            if ($key == 'isad.date') {
                $date_parts = explode(" ", $value);
                $query->where('endDateISO', '>=', $date_parts[0] . "-12-31");
                $query->where('beginDateISO', '<=', $date_parts[1] . "-01-01");
            }
        } */

        if (isset($inputparams['maximumRecords'])) {
            $value = filter_var($inputparams['maximumRecords'], FILTER_VALIDATE_INT);
            if ($value) {
                $queryparams['limit'] = ['value' => $value, 'operator' => '='];
            }
        }

        // return array with sanitized query paramaters
        return $queryparams;
    }
}
