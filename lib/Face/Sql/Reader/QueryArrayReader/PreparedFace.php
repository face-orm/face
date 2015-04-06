<?php


namespace Face\Sql\Reader\QueryArrayReader;


use Face\Config;
use Face\Core\EntityFace;
use Face\Core\EntityInterface;
use Face\Core\InstancesKeeper;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\SelectBuilder\QueryFace;
use Face\Util\Operation;
use Face\Util\StringUtils;

class PreparedFace extends SoftPreparedFace{

    /**
     * @var Operation[]
     */
    protected $operationsList=[];

    public function _build()
    {
        parent::_build();
        $this->_prepareOperations(true);
    }


    public function runOperations(EntityInterface $instance, $array,InstancesKeeper $instanceKeeper, &$unfound){

        foreach($this->operationsList as $k=> $operation) {


            switch ($operation->getName()) {
                case self::OPERATION_FORWARD_JOIN:

                    $identity = $operation->getOptions("relatedPreparedFace")->rowIdentity($array);
                    $element = $operation->getOptions("element");


                    if ($instanceKeeper->hasInstance($element->getClass(), $identity)) {
                        $instance->faceSetter($element, $instanceKeeper->getInstance($element->getClass(), $identity));
                    } else {
                        $unfound[] = ["instance" => $instance, "elementToSet" => $element, "identityOfElement" => $identity];
                    }

                    break;

                case self::OPERATION_EXISTING_ENTITY:

                    $identity =  $operation->getOptions("relatedPreparedFace")->rowIdentity($array);
                    $element = $operation->getOptions("element");

                    if (!empty($identity)) {
                        if ($instanceKeeper->hasInstance($element->getClass(), $identity)) {
                            // if element is already instanced
                            $childInstance = $instanceKeeper->getInstance($element->getClass(), $identity);
                            $instance->faceSetter($element, $childInstance);
                        } else {
                            //var_dump($identity);
                            //var_dump($this->instancesKeeper);
                            throw new \Exception("TODO : precedence");
                        }
                    }

                    break;

                case self::OPERATION_DO_VALUES:


                    $cName = $operation->getOptions("columnName");
                    $value = isset($array[$cName]) ? $array[$cName] : null;

                    $instance->faceSetter($operation->getOptions("element"), $value);


                    break;
            }
        }

    }

    /**
     * search to put children/parent instance as reference of the given entity
     * @throws \Exception
     */
    protected function _prepareOperations($doValues = false)
    {

        $faceLoader = $this->getFace()->getFaceLoader();
        $face = $this->face;
        $faceList = $this->preparedOperation->getPreparedFaces();

        foreach ($face->getElements() as $element) {
            $pathToElement = $this->path . "." . $element->getName();
            if ($element->isEntity()) {
                // if element is joined we know what to do
                if (isset($faceList[$pathToElement])) {

                    $queryFace = $faceList[$pathToElement]->getQueryFace();

                    $operation = new Operation(self::OPERATION_EXISTING_ENTITY);
                    $operation->setOptions("element",$element);
                    $softPFace = new SoftPreparedFace($queryFace, $this->preparedOperation);
                    $softPFace->_build();
                    $operation->setOptions("relatedPreparedFace", $softPFace );

                    $this->operationsList[$pathToElement]=$operation;




                } else {
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

                    // A was the previous step


                    $elementFace = $faceLoader->getFaceForClass($element->getClass());

                    $relatedBy = $element->getRelatedBy();
                    if($relatedBy){
                        $related = $elementFace->getDirectElement($relatedBy);
                    }else{
                        $related = null;
                    }

                    if ($related) {
                        // B
                        // this.tree => bad
                        // this.tree.lemon => good
                        if (substr_count($this->path, ".")<1) {
                            $this->operationsList[$pathToElement] = new Operation(self::OPERATION_PASS);

                        } else {
                            // find the related base path and take its face
                            $relatedBasePath = StringUtils::subStringBeforeLast($this->path, ".");
                            $parentQueryFace = $faceList[$relatedBasePath]->getQueryFace();

                            // C
                            // Same class ?
                            if ($parentQueryFace->getFace()->getClass() != $element->getClass()) {
                                $this->operationsList[$pathToElement] = new Operation(self::OPERATION_PASS);
                            } else {
                                /* @var $parentQueryFace QueryFace */
                                // D
                                // Look if parent and child refer to the same one
                                if ($parentQueryFace->getFace()->getDirectElement($element->getRelatedBy())->getRelatedBy() == $element->getName()) {
                                    $relatedBasePath=substr($this->path, 0, strrpos($this->path, '.'));

                                    // if $related is not in $facelist, then it means that $related is not a part of the query, ignore it..
                                    if (isset($faceList[$relatedBasePath.".".$related->getName()])) {
                                        $operation = new Operation(self::OPERATION_FORWARD_JOIN);
                                        $operation->setOptions("element",$element);

                                        $softPFace = new SoftPreparedFace($parentQueryFace, $this->preparedOperation);
                                        $softPFace->_build();
                                        $operation->setOptions("relatedPreparedFace", $softPFace );

                                        $this->operationsList[$pathToElement]=$operation;

                                    } else {
                                        //$this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);
                                    }
                                } else {
                                    // TODO ?
                                    //$this->operationsList[$pathToElement]=new Operation(self::OPERATION_IMPLIED);
                                }

                            }


                        }


                    } else {
                        //$this->operationsList[$pathToElement] = new Operation(self::OPERATION_PASS);
                    }

                }

            } else {




                if(isset($this->columns[$pathToElement])){
                    $column = $this->columns[$pathToElement];
                    $operation = new Operation(self::OPERATION_DO_VALUES);
                    $operation->setOptions("columnName", $column->getAlias() );
                    $operation->setOptions("element", $column->getEntityFaceElement());
                    $this->operationsList[$element->getName()] = $operation;
                }


            }
        }
    }

}