<?php

namespace Face\Exception;
use Face\Util\StringUtils;
use Face\Core\EntityFace;

/**
 * FaceNameDoesntExistsException
 *
 * @author bobito
 */
class FaceElementDoesntExistsException extends FaceDoesntExistsException
{

    function __construct($name,EntityFace $face)
    {

        $names = $this->debugGetRelatedName($name, $face);


        if (count($names)>0) {
            $relatedStr  = "Did you mean '";
            $relatedStr .= implode("' , '", $names);
            $relatedStr .= "' ?";
        } else {
            $relatedStr ="";
        }

        $message = "Face : '" . $face->getName() . "' has no element called '$name' $relatedStr";

        parent::__construct($message);
    }

    /**
     * Allows to debug typos etc..
     *
     * For instance if someone writes 'lemon' instead of 'lemons' we will say "hey ! did you means 'lemons' ? "
     *
     * @param $e
     * @return array
     */
    protected function debugGetRelatedName($e, EntityFace $face)
    {

        $names=[];

        foreach ($face->getElements() as $elm) {
            if (StringUtils::beginsWith($e, $elm->getName())) {
                $names[] = $elm->getName();
            }else if (StringUtils::endsWith($e,$elm->getName())){
                $names[] = $elm->getName();
            }

            // TODO more global matches
        }

        return $names;
    }


}
