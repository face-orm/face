<?php

namespace Face\Util;

/**
 * Class Operation
 * @package Face\Core
 *
 * An operation is something named that embeds some options
 *
 */
class Operation
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options=array();


    public function __construct($name)
    {
        $this->name=$name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($name, $options)
    {
        $this->options[$name] = $options;
    }

    /**
     * @return mixed
     */
    public function getOptions($name)
    {
        return $this->options[$name];
    }
}
