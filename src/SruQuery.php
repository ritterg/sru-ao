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
     * @param $params
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

        if (isset($inputparams['maximumRecords'])) {
            $value = filter_var($inputparams['maximumRecords'], FILTER_VALIDATE_INT);
            if ($value) {
                $queryparams['limit'] = $value;
            }
        }

        // return array with sanitized query paramaters
        return $queryparams;
    }
}
