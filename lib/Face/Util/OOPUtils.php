<?php
/**
 * @author Soufiane GHZAL
 * @copyright Laemons
 * @license BSD3
 */


namespace Face\Util;


class OOPUtils {

    /**
     * will check if the given class uses the given trait
     * @param mixed $class classname or an instance of the class to test
     * @param string $trait name of the trait to check
     * @return boolean true if the class uses the trait
     */
    public static function UsesTrait($class,$trait){
        return array_key_exists($trait, class_uses($class));
    }

}