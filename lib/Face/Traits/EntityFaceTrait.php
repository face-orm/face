<?php

namespace Face\Traits;

use Face\Config;
use Face\Core\FaceLoader;
use Face\Core\Navigator;
use Face\Exception\BadParameterException;
use Face\ORM;
use Face\Sql\Query\SelectBuilder;

trait EntityFaceTrait
{
    
    protected $___faceAlreadySetMany;
    
    /**
     * take the given element and use the right way for getting the value on this instance
     * @param \Face\Core\EntityFaceElement|string $element the element to get or the path to the element
     * @return mixed
     */
    public function faceGetter($needle)
    {
        
        // look the type of $needle then dispatch
        if ( $needle instanceof \Face\Core\EntityFaceElement ) {
            /*  if is already a face element, dont beed anymore work */
            $element=$needle;
        } else if (is_string($needle)) {
            /*
             * if it is a string the string can be the name of the element or a chain of elements separated by a dot
             * e.g 
             *  - first form : "elementName"
             *  - secnd form : "elementName1.elementName2.elementName3"
             */
            // TODO catch "this.elementName" case for dont instanciate a Navigator not needed for performances
            if (false!==strpos($needle, ".")) {
                return (new Navigator($needle))->chainGet($this); // "elementName1.elementName2.elementName3" case
            } else {
                $element=$this->getEntityFace()->getElement($needle); // "elementName" case
            }
        } else {
            throw new BadParameterException("Variable of type '".gettype($needle)."' is not a valide type for faceGetter");
        }


        // if has a getter, it can be a custom callable annonymous function, or the name of the the method to call on this object
        if ($element->hasGetter()) {
            $getter = $element->getGetter();
            if (is_string($getter)) {
//method of this object
                return $this->$getter();
            } elseif (is_callable($getter)) {
// custom callable
                return $getter();
            } else {
                throw new \Exception('Getter is set but it is not usable : '.var_export($getter, true));
            }
        
        // else we use the property directly
        } else {
            $property = $element->getPropertyName();
            return $this->$property;
            
        }
        
        // TODO throw exception on no way to get element
    }
    
    
    
    /**
     * use the given element and use the right way for getting the value on this instance
     *
     * Be aware that it is really more performant to use the element instance instead of the element Name
     *
     * @param \Face\Core\EntityFaceElement $element the element to get
     * @return mixed
     */
    public function faceSetter($path, $value, FaceLoader $faceLoader = null)
    {

        // look the type of $needle then dispatch
        if ( $path instanceof \Face\Core\EntityFaceElement) {
            /*  if is already a face element, dont need anymore work */
            $element=$path;
        } elseif (is_string($path)) {
            /*
             * if it is a string the string can be the name of the element or a chain of elements separated by a dot
             * e.g 
             *  - first form : "elementName"
             *  - secnd form : "elementName1.elementName2.elementName3"
             */
            // TODO catch "this.elementName" case for dont instanciate a Navigator not needed for performances
            
            if (false!==strpos($path, ".")) {
                // "elementName1.elementName2.elementName3" case
                (new Navigator($path))->chainSet($this, $value);
                return $value;
            } else {
                $element=$this->getEntityFace($faceLoader)->getElement($path);
            }
            
        } else {
            throw new \Exception("Variable of type '".gettype($path)."' is not a valide type for path of faceSetter");
        }

        $faceLoader = $element->getParentFace()->getFaceLoader();
        
        /* @var $element \Face\Core\EntityFaceElement */
        
        // if has a getter, it can be a custom callable anonymous function, or the name of the the method to call on this object
        if ($element->hasSetter()) {
            $setter = $element->getSetter();
            if (is_string($setter)) {
                //method of this object
                return $this->$setter($value);
            } elseif (is_callable($setter)) {
                // custom callable
                return $setter($value);
            } else {
                throw new \Exception('Setter is set but it is not usable : '.var_export($setter, true));
            }
        
        // else we use the property directly
        } else {
            $property = $element->getPropertyName();
            if (!empty($property)) {
                if ($element->hasManyRelationship() || $element->hasManyThroughRelationship()) {
                    if (!isset($this->___faceAlreadySetMany[$element->getName()][$value->faceGetIdentity($faceLoader)])) {
                        if ($this->$property ==null) {
                            $this->$property=array();
                        }

                        array_push($this->$property, $value);
                        $this->___faceAlreadySetMany[$element->getName()][$value->faceGetIdentity($faceLoader)]=true;
                    }

                } else {
                    $this->$property=$value;
                }
            } else {
                // TODO  exception or something else ?
            }
        }
        // TODO chainSet in Navigator instead than in this trait
        // TODO throw exception on "no way to set element"

    }



    /**
     * @see EntityInterface::getEntityFace
     */
    public static function getEntityFace(FaceLoader $faceLoader = null)
    {
        if(null === $faceLoader){
            $faceLoader = Config::getDefault()->getFaceLoader();
        }
        return $faceLoader->getFaceForClass(__CLASS__);
    }
    
    
    public function faceGetIdentity(FaceLoader $faceLoader = null)
    {
        $array=self::getEntityFace($faceLoader)->getIdentifiers();

        if (!$array || 0==count($array)) {
            throw new \Exception("The Class ".__CLASS__." has no face identifier.");
        }

        
        $identityString="";
        
        foreach ($array as $element) {
            $identityString.=$this->faceGetter($element);
        }
        
        return $identityString;
        
    }



    /**
     * Shortcut to construct a FQuery
     * @return SelectBuilder
     */
    public static function faceQueryBuilder(Config $config = null)
    {
        if(null == $config) {
            return new SelectBuilder(self::getEntityFace());
        }else{
            return new SelectBuilder(self::getEntityFace($config->getFaceLoader()));
        }
    }

    /**
     * Fast querying by only specifying one field
     * @param $item string name of the item (without the leading tild) e.g : "id"
     * @param $itemValue string|array value to find. You may specify an array to use an WHERE item IN (...) instead of WHERE item = ...
     * @param $pdo
     * @return \Face\Sql\Result\ResultSet
     */
    public static function faceQueryBy($item, $itemValue, $pdo)
    {
        if (self::getEntityFace()->getDirectElement($item)) {
            $fQuery = self::faceQueryBuilder();

            if (is_array($itemValue)) {
                $fQuery->whereIN("~$item", $itemValue);
            } else {
                $fQuery->where("~$item=:itemValue")
                       ->bindValue(":itemValue", $itemValue);
            }

            return ORM::execute($fQuery, $pdo);

        }
    }

    /**
     * shortcut to create a @see \Face\Sql\Query\QueryString
     * @param string $string the SQL query
     * @param array $options fields to select and It doesnt work anymore, to be fixed in 1.0
     */
    public static function queryString($string, $options = [])
    {
        
        $joins = [];
        $selectedColumns = [];
        
//        if (is_array($options)) {
//            if (isset($options["join"])) {
//                foreach ($options["join"] as $j) {
//                    $joins["this.$j"] = self::getEntityFace()->getElement($j)->getFace();
//                }
//            }
//
//            if (isset($options["select"])) {
//                foreach ($options["select"] as $k => $j) {
//                    $basePath = is_numeric($k) ? $j : $k;
//
//                    if (!StringUtils::beginsWith("this", $basePath)) {
//                        $basePath = "this." . $basePath;
//                    }
//
//                    $basePath = substr($basePath, 0, strrpos($basePath, "."));
//
//                    self::__queryStringDoColumnRecursive($selectedColumns, $k, $j, $basePath);
//
//
//                }
//            }
//
//        }
        $qS = new \Face\Sql\Query\QueryString(self::getEntityFace(), $string, $joins, $selectedColumns);
        
        return $qS;
        
    }
    
//    private static function __queryStringDoColumnRecursive(&$selectedColumns, $k, $v, $basePath)
//    {
//
//
//        // case   "lemons.tree_id"
//        if (is_numeric($k)) {
//            $pos = strrpos($v, ".");
//            $columnName = false ===  $pos ? $v : substr($v, $pos+1);
//            $selectedColumns["$basePath.$columnName"] = $columnName;
//
//        } else {
//            // case  "lemons" => [ "tree_id" ]
//            if (is_array($v)) {
//                foreach ($v as $kk => $vv) {
//                    $newBasePath = "$basePath.$k." . (is_numeric($kk) ? $vv : $kk);
//                    $newBasePath = substr($newBasePath, 0, strrpos($newBasePath, "."));
//                    self::__queryStringDoColumnRecursive($selectedColumns, $kk, $vv, $newBasePath);
//
//                }
//
//            // case  "lemons.tree_id"=>"tree_id"
//            } else {
//                $pos = strrpos($k, ".");
//                $nameEnd = false ===  $pos ? $k : substr($k, $pos+1);
//                $selectedColumns["$basePath.$nameEnd"] = $v;
//            }
//
//        }
//
//
//    }
}
