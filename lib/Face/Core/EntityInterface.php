<?php

namespace Face\Core;

use Face\Config;

interface EntityInterface{


    /**
     * @see EntityFaceTrait::faceGetter
     */
    public function faceGetter($needle);

    /**
     * @see EntityFaceTrait::faceSetter
     */
    public function faceSetter($path, $value, FaceLoader $faceLoader = null);

    /**
     * @see EntityFaceTrait::getEntityFace
     */
    public static function getEntityFace(FaceLoader $faceLoader = null);

    /**
     * @see EntityFaceTrait::faceGetIdentity
     */
    public function faceGetIdentity(FaceLoader $faceLoader = null);

    /**
     * @see EntityFaceTrait::faceQueryBuilder
     */
    public static function faceQueryBuilder(Config $config = null);

    /**
     * @see EntityFaceTrait::faceQueryBy
     */
    public static function faceQueryBy($item, $itemValue, $pdo);

    /**
     * @see EntityFaceTrait::queryString
     */
    public static function queryString($string, $options);

}