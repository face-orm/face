<?php
/**
 * @author Soufiane GHZAL
 */


namespace Face\Util;

class StringUtils
{


    /**
     * Get the substring before the last occurence of a character
     * @param $haystack
     * @param $needle
     * @param int $n
     * @return string
     */
    public static function subStringBeforeLast($haystack, $needle, $n = 1)
    {
        while ($n>0) {
            $haystack = substr($haystack, 0, strrpos($haystack, $needle));
            $n--;
        }
        return $haystack;
    }

    /**
     * Get the substring after the last occurrence of a character
     * @param $haystack
     * @param $needle
     * @param int $n
     * @return string
     * TODO : tests suit
     */
    public static function subStringAfterLast($haystack, $needle, $n = 1)
    {
        $string = "";
        while ($n>0) {
            $string .= substr($haystack, strrpos($haystack, $needle) + 1);
            if($n>1){
                $string = $needle . $string;
            }
            $n--;
        }
        return $string;
    }


    /**
     * look if $subject begins with $search
     * @param string $search the string that we want to find in $subject
     * @param string $subject the string in which we search
     * @return boolean true if $subject beguins with $search
     */
    public static function beginsWith($search, $subject)
    {
        return 0 === strncmp($subject, $search, strlen($search)) ;
    }

    /**
     * look if $subject ends with $search
     * @param $search string the term that we are searching for
     * @param $subject string the subject we are searching in
     * @return bool true if $subject ends with $search
     */
    public static function endsWith($search, $subject)
    {
        return (substr($subject, strlen($search)*-1) === $search);
    }
}
