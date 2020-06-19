<?php

namespace Ritterg\SruAo;

/**
 * Class SruResponse
 *
 * @author  Gerold Ritter <ritter@e-hist.ch>
 */
class Sample
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
     * @param $name
     *
     * @return  string
     */
    public function sayHello($name)
    {
        $greeting = $this->config->get('greeting');

        return $greeting . ' ' . $name;
    }

}
