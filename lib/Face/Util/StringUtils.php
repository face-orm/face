<?php
/**
 * @author Soufiane GHZAL
 * @copyright Laemons
 * @license BSD3
 */


namespace Face\Util;


class StringUtils {




    public static function subStringBefore($haystack,$needle,$n=1){
        while($n>0){
            $haystack=substr($haystack, 0, strrpos( $haystack, $needle));
            $n--;
        }
        return $haystack;
    }

    /**
     * look if $subject begins with $search
     * @param string $search the string that we want to find in $subject
     * @param string $subject the string in which we search
     * @return boolean true if $subject beguins with $search
     */
    public static function beginsWith($search,$subject){
        return 0 === strncmp($subject,$search,  strlen($search)) ;
    }

    /**
     * look if $subject ends with $search
     * @param $search string the term that we are searching for
     * @param $subject string the subject we are searching in
     * @return bool true if $subject ends with $search
     */
    public static function endsWith($search,$subject){
        return (substr($subject,strlen($search)*-1) === $search);
    }




}