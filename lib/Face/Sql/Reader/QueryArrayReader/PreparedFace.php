<?php


namespace Face\Sql\Reader\QueryArrayReader;


use Face\Config;
use Face\Core\EntityFace;
use Face\Core\InstancesKeeper;
use Face\Sql\Query\FQuery;
use Face\Util\Operation;
use Face\Util\StringUtils;

class PreparedFace extends SoftPreparedFace{


    protected $operationsList=[];

    public function _build()
    {
        foreach($this->face->getElements() as $e){
            $this->columnNames[$e->getName()] = $this->makeColumnName($e);
        }

        $this->rowIdentityCb = $this->_compileRowIdentity();

        $this->_prepareOperations(true);
    }


    public function runOperations($instance, $array,InstancesKeeper $instanceKeeper, &$unfound){

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
                            // if element is already instanciated
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

                    if ($value) {
                        $instance->faceSetter($operation->getOptions("element"), $value);
                    }

                    break;
            }
        }

    }

    /**
     * search to put children/parent instance as reference of the given entity
     * @param type $instance
     * @param \Face\Core\EntityFace $face
     * @param type $array
     * @param type $basePath
     * @param type $faceList
     * @throws \Exception
     */
    protected function _prepareOperations($doValues = false)
        //protected function instanceHydrateAndForwardEntities($instance, \Face\Core\EntityFace $face, $array, $basePath, $faceList, $doValues = false)
    {

        $config = Config::getDefault();

        $face = $this->face;

        $faceList = $this->preparedOperation;

        foreach ($face->getElements() as $element) {
            if ($element->isEntity()) {
                $pathToElement=$this->path . "." . $element->getName();

// if element is joined we know what to do
                if (isset($faceList[$pathToElement])) {

                    $operation = new Operation(self::OPERATION_EXISTING_ENTITY);
                    $operation->setOptions("element",$element);
                    $softPFace = new SoftPreparedFace($this->path.".".$element->getName(), $element->getFace(), $this->preparedOperation);
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



                    $related = $config->getFaceLoader()->getFaceForClass($element->getClass())->getDirectElement($element->getRelatedBy());
                    if ($related) {
                        // B
                        // this.tree => bad
                        // this.tree.lemon => good
                        if (substr_count($this->path, ".")<1) {
                            $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);

                        } else {
                            // find the related base path and take its face
                            $relatedBasePath = StringUtils::subStringBefore($this->path, ".");
                            $parentFace = $faceList[$relatedBasePath];

                            // C
                            // Same class ?
                            if ($parentFace->getFace()->getClass() != $element->getClass()) {
                                $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);

                            } else {
                                /* @var $parentFace \Face\Core\EntityFace */
                                // D
                                // Look if parent and child refer to the same one
                                if ($parentFace->getFace()->getDirectElement($element->getRelatedBy())->getRelatedBy() == $element->getName()) {
                                    $relatedBasePath=substr($this->path, 0, strrpos($this->path, '.'));

                                    // if $related is not in $facelist, then it means that $related is not a part of the query, ignore it..
                                    if (isset($faceList[$relatedBasePath.".".$related->getName()])) {
                                        $operation = new Operation(self::OPERATION_FORWARD_JOIN);
                                        $operation->setOptions("element",$element);

                                        $softPFace = new SoftPreparedFace($relatedBasePath, $related->getParentFace(), $this->preparedOperation);
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
                        $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);

                    }

                }

            } elseif ($doValues) {

                $operation = new Operation(self::OPERATION_DO_VALUES);
                $operation->setOptions("columnName", $this->columnNames[$element->getName()]);
                $operation->setOptions("element", $element);

                $this->operationsList[$element->getName()] = $operation;
            }
        }
    }

}