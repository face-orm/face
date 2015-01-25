<?php
/**
 * @author Soufiane GHZAL
 * @copyright Laemons
 * @license BSD3
 */

namespace Face;


use Face\Core\EntityFace;
use Face\Core\EntityFaceElement;
use Face\Traits\EntityFaceTrait;

class Debugger {


    /**
     * @param \Face\Traits\EntityFaceTrait[]|\Face\Traits\EntityFaceTrait $data
     */
    public static function dumpFaceData($data){
        if(!is_array($data) && !$data instanceof \Traversable )
            $dataArray[]=$data;
        else
            $dataArray = $data;

        echo PHP_EOL;

        foreach($data as $d){

            self::_proccessDumpFaceData($d,[],0);

        }

    }

    private static function _proccessDumpFaceData($data,$alreadyPrinted,$depth,$maxDepth=30){

        $nlStr=PHP_EOL;

        // no endless loop
        if($depth>$maxDepth)
            return ;

        $spacesToIndent=2;


        $face=$data->getEntityFace();


        echo str_repeat(" ",$depth*$spacesToIndent) . $face->getClass() . "::" . $data->faceGetidentity();

        if(in_array($data,$alreadyPrinted)){
            echo "::RECURSION::" . $nlStr;
            return ;
        }

        echo $nlStr;

        $alreadyPrinted[]=$data;


        $depth++;

        foreach($face as $elm){
            /* @var $elm EntityFaceElement */
            if($elm->isValue()){

                echo str_repeat(" ",$depth*$spacesToIndent) . $elm->getName() . "::" . $data->faceGetter($elm->getName()) . $nlStr;

            }else{

                $arr = $data->faceGetter($elm->getName());

                if(!$arr)
                    echo str_repeat(" ",$depth*$spacesToIndent) . "::NULL::".$elm->getName();
                else if( is_array( $arr ) )
                    foreach($arr as $a)
                        self::_proccessDumpFaceData($a,$alreadyPrinted,$depth+1);
                else
                    self::_proccessDumpFaceData($data->faceGetter($elm->getName()),$alreadyPrinted,$depth);

            }

        }

    }


}