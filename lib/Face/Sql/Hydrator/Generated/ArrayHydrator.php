<?php

namespace Face\Sql\Hydrator\Generated;

use Face\Core\EntityFace;
use Face\Core\EntityFaceElement;
use Face\Sql\Hydrator\GeneratedHydrator;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\SelectBuilder\QueryFace;
use Face\Sql\Reader\QueryArrayReader\PreparedFace;
use Face\Util\StringUtils;

/**
 * Hydrator that transforms a pdo statement to as set of fully hydrated and linked objects
 */
class ArrayHydrator extends GeneratedHydrator
{
    protected function generateCode(FQuery $FQuery)
    {

        $identityGetter = "\$identityGetter = [];\n";
        $arrayDefault = "\$data = [];\n";
        $entityFill = "";
        $relations = "";

        $faceList = $FQuery->getAvailableQueryFaces();


        foreach($faceList as $path => $queryFace){
            $primaries = "";

            foreach($queryFace->getFace()->getPrimaries() as $primary){
                $name = $queryFace->getColumnsReal()[$queryFace->makePath($primary->getName())]->getAlias();
                $primaries .= "\$row['$name'] . ";
            }

            $primaries = rtrim($primaries, ". ");

            $identityGetter .= "        \$identityGetter['$path'] = function(\$row){return $primaries;};\n";
            $arrayDefault .= "        \$data['$path'] = [];\n";

            $entityFill .= $this->generateEntityFill($path, $queryFace);

            $relations .= $this->generateEntityRelations($path, $queryFace, $faceList);


        }





        $code = <<<EOF

    return function(\$statement){

        \$data = [];

        \$manyAlreadySet = [];

        $identityGetter

        $arrayDefault

        while(\$array = \$statement->fetch(\PDO::FETCH_ASSOC)){

            \$identity = [];

            // CREATE ENTITIES
            $entityFill

            // MAKE RELATIONS
            $relations

        }

        return \$data;

    };


EOF;

        return $code;

    }


    private function generateEntityFill($path, QueryFace $queryFace){

        $className = $queryFace->getFace()->getClass();

        $propertiesSet = '';

        foreach($queryFace->getColumnsReal() as $columnPath => $column){
            $propertyName = $column->getEntityFaceElement()->getPropertyName();
            $columnName = $column->getAlias();
            $propertiesSet .= "\$data['$path'][\$identity['$path']]->$propertyName = \$array['$columnName'];\n                ";
        }
        $entityFill = <<<EOF

            \$identity['$path'] = \$identityGetter['$path'](\$array);
            if(\$identity['$path'] && !isset(\$data['$path'][\$identity['$path']])){
                \$data['$path'][\$identity['$path']] = new $className();
                $propertiesSet
            }

EOF;

        return $entityFill;

    }


    /**
     * @param $path
     * @param QueryFace $queryFace
     * @param QueryFace[] $faceList
     */
    private function generateEntityRelations($path, QueryFace $queryFace, $faceList){
        $str = '';
        foreach($queryFace->getFace()->getElements() as $element){

            /*
             * A . Look if the child was join by the parent
             *
             *      YES => EASY ! work's done... go to the next
             *
             *      NO  => Then it can only work with the parent
             *
             *              matching case : if current element is this.lemon.tree (where this is the tree) then it works because it refers to this
             *              no matching case : if current element is this.lemon.seed (where this is the tree) seed doesnt refers to this (tree)
             *
             *
             *          B . $basePath is made of at least 3 element (because we want to check for the parent of the parent
             *
             *          C . Take the parent and look if it is the same class as the child
             *              e.g :
             *                  assuming that "this" is a Lemon.
             *                  Assuming we have the following paths : 1 this.tree.lemons  and 2 this.tree.leafs
             *                      then in 1 "lemons" and "this" are both Lemon
             *                      then in 2 "leafs" is Leaf but "this" is Lemon => ignore it
             *
             *          D . If same class, we have to make sure that it is the same element. Let's use "related" property for that
             *
             *              YES => Here is the match, fill it now
             *
             *              NO  => Not totaly lost, maybe that there is an implied relation
             *
             *                  E . Look for implied relation
             *
             */


            // case A
            if($element->isEntity()){
                $pathToElement = $path . "." . $element->getName();
                if(isset($faceList[$pathToElement])){

                    $queryFaceChild = $faceList[$pathToElement];
                    $propertyName = $element->getPropertyName();
                    if($element->hasManyRelationship() || $element->hasManyThroughRelationship()){
                        $propertyName .= "[]";
                        $manyAlreadySet = " && !isset(\$manyAlreadySet['$path::$pathToElement'][\$identity['$pathToElement']])";
                    }else{
                        $manyAlreadySet = "";
                    }

                    $str .= <<<EOF

            if (!empty(\$identity['$pathToElement']) $manyAlreadySet) {
                \$childInstance = \$data['$pathToElement'][\$identity['$pathToElement']];
                \$data['$path'][\$identity['$path']]->$propertyName = \$childInstance;
                \$manyAlreadySet['$path::$pathToElement'][\$identity['$pathToElement']] = true;
            }

EOF;
                } else {



                    // before we continue, we check that the element is explicitly related to something
                    $elementFace = $element->getFace();
                    $relatedBy = $element->getRelatedBy();
                    if($relatedBy){
                        $related = $elementFace->getDirectElement($relatedBy);
                    }else{
                        $related = null;
                    }

                    if($related) {

                        // case B
                        if (substr_count($path, '.') >= 1) {
                            $relatedBasePath = StringUtils::subStringBeforeLast($path, ".");

                            if (isset($faceList[$relatedBasePath])) {
                                /* @var $parentQueryFace QueryFace */
                                $parentQueryFace = $faceList[$relatedBasePath];

                                // case C
                                if ($parentQueryFace->getFace()->getName() === $element->getName()) {

                                    /* @var $parentQueryFace QueryFace */
                                    // D
                                    // Look if parent and child refer to the same one
                                    if ($parentQueryFace
                                            ->getFace()
                                            ->getDirectElement($element->getRelatedBy())
                                            ->getRelatedBy()
                                        == $element->getName()
                                    ) {
                                        $relatedBasePath = substr($path, 0, strrpos($path, '.'));

                                        substr("this.lemon", 0, strrpos("this.lemon", '.'));

                                        // if $related is not in $facelist, then it means that $related is not a part of the query, ignore it..
                                        if (isset($faceList[$relatedBasePath . "." . $related->getName()])) {
                                            $str .= $this->compileForwardJoin($element, $path , $relatedBasePath);
                                        }
                                    } else {
                                        // TODO case E
                                        //$this->operationsList[$pathToElement]=new Operation(self::OPERATION_IMPLIED);
                                    }

                                }
                            }
                        }
                    }

                }

            }

        }

        return $str;
    }


    private function compileForwardJoin(EntityFaceElement $element, $currentInstancePath, $childInstancePath){

        $elementProperty = $element->getPropertyName();
        $str = <<<EOF

        // FORWARD JOIN $currentInstancePath::$elementProperty WITH $childInstancePath
        if(!empty(\$identity['$currentInstancePath']) && !empty(\$identity['$childInstancePath'])){

            \$parent = \$data['$currentInstancePath'][\$identity['$currentInstancePath']];
            \$child  = \$data['$childInstancePath'][\$identity['$childInstancePath']];
            \$parent->$elementProperty = \$child;
        }
EOF;
        return $str;
    }
}
