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
    public function getQuery($params)
    {
        // test if first param is array
        if (!is_array($params)) {
            throw new FirstParameterIsNotArray("First parameter must be an array.");
        }


        // return array with sanitized query paramaters
        return [];
    }
}
