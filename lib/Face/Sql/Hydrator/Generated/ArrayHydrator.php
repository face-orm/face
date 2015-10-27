<?php

namespace Face\Sql\Hydrator\Generated;

use Face\Sql\Hydrator\GeneratedHydrator;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\SelectBuilder\QueryFace;
use Face\Sql\Reader\QueryArrayReader\PreparedFace;

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
    private function generateEntityRelations($path, QueryFace $queryFace, array $faceList){
        $str = '';
        foreach($queryFace->getFace()->getElements() as $element){
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
                }

            }
        }

        return $str;
    }

}
