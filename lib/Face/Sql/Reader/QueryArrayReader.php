<?php

namespace Face\Sql\Reader;

use Face\Config;
use \Face\Sql\Query\FQuery;
use Face\Core\InstancesKeeper;
use Face\Sql\Reader\QueryArrayReader\PreparedFace;
use Face\Sql\Reader\QueryArrayReader\PreparedOperations;
use Face\Sql\Reader\QueryArrayReader\PreparedReader;
use Face\Util\Operation;
use Face\Util\StringUtils;

/**
 * Description of QueryArrayReader
 *
 * @author bobito
 */
class QueryArrayReader implements QueryReaderInterface
{

    public static $devtimer=0;

    /**
     *
     * @var \Face\Sql\Query\FQuery
     */
    protected $FQuery;
    /**
     *
     * @var \Face\Core\InstancesKeeper
     */
    protected $instancesKeeper;

    /**
     *
     * @var \Face\Sql\Result\ResultSet
     */
    protected $resultSet;



    protected $unfoundPrecedence;

    function __construct(\Face\Sql\Query\FQuery $FQuery, InstancesKeeper $instancesKeeper = null)
    {

        $this->FQuery = $FQuery;

        if (!$instancesKeeper) {
            $this->instancesKeeper=new InstancesKeeper();
        } else {
            $this->instancesKeeper=$instancesKeeper;
        }

        $this->resultSet=new \Face\Sql\Result\ResultSet($FQuery->getBaseFace(), $this->instancesKeeper);
        
    }


    public function read(\PDOStatement $stmt)
    {

        $this->unfoundPrecedence=array();

        $preparedReader = new PreparedOperations($this->FQuery);


        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // loop over joined faces
            foreach ($preparedReader as $basePath => $preparedFace) {

                $identity = $preparedFace->rowIdentity($row, $basePath);

                if ($identity) {
                    // if already instantiated then get it from ikeeper and try the forwards
                    if ($this->instancesKeeper->hasInstance($preparedFace->getFace()->getClass(), $identity)) {
                        $instance = $this->instancesKeeper->getInstance($preparedFace->getFace()->getClass(), $identity);

                        $preparedFace->runOperations($instance,$row,$this->instancesKeeper, $this->unfoundPrecedence);

                        if (!$this->resultSet->pathHasIdentity($basePath, $identity)) {
                            $this->resultSet->addInstanceByPath($basePath, $instance, $identity);
                        }

                        // else create the instance and hydrate it
                    } else {
                        $instance = $this->createInstance($preparedFace->getFace());
                        $this->instancesKeeper->addInstance($instance, $identity);
                        $this->resultSet->addInstanceByPath($basePath, $instance, $identity);

                        $preparedFace->runOperations($instance,$row,$this->instancesKeeper, $this->unfoundPrecedence);

                    }
                }

            }


        }

        // set unset instances. To be improved ?
        foreach ($this->unfoundPrecedence as $unfound) {

            if(!$this->instancesKeeper->hasInstance($unfound['elementToSet']->getClass(), $unfound['identityOfElement'])){

                var_dump($unfound['elementToSet']->getClass());
                var_dump($unfound['identityOfElement']);
                var_dump(get_class($instance));

            }

            $unfoundInstance = $this->instancesKeeper->getInstance($unfound['elementToSet']->getClass(), $unfound['identityOfElement']);
            $unfound['instance']->faceSetter($unfound['elementToSet'], $unfoundInstance);
        }


        return $this->resultSet;

    }


    /**
     * Create an instance from an assoc array  returned by sql
     * @param \Face\Core\EntityFace $face the face that describes the entity
     * @param array $array the array of data
     * @param string $basePath
     * @param array $faceList
     * @return \Face\Sql\Reader\className
     */
    protected function createInstance(\Face\Core\EntityFace $face)
    {
        $className = $face->getClass();
        $instance  = new $className();

        return $instance;
    }






}
