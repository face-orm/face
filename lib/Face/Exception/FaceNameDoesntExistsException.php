<?php

namespace Face\Exception;

/**
 * FaceNameDoesntExistsException
 *
 * @author bobito
 */
class FaceNameDoesntExistsException extends FaceDoesntExistsException
{

    function __construct($name)
    {
        $message = "Face named $name doesn't exist.";
        parent::__construct($message);
    }

}
