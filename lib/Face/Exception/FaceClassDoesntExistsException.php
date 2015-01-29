<?php

namespace Face\Exception;

/**
 * FaceClassDoesntExistsException
 *
 * @author bobito
 */
class FaceClassDoesntExistsException extends FaceDoesntExistsException
{

    function __construct($className)
    {
        $message = "Not Face found for class $className.";

        if(!class_exists($className)) {
            $message = " Additionaly it appears that this class doesn't exist";
        }

        parent::__construct($message);
    }

}
