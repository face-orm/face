<?php

namespace Face\Sql\Query;


use Face\Config;
use Face\Core\EntityInterface;
use Face\Traits\EntityFaceTrait;
use Face\Util\OOPUtils;

/**
 *
 * It represents a query that will modify the database (update/insert/delete) and that uses only one entity (not a collection, ignore children/parents)
 *
 * Class AbstractSimpleQuery
 * @package Face\Sql\Query
 */
abstract class AbstractModifierSimpleQuery extends FQuery {

    /**
     * @var EntityFaceTrait
     */
    protected $entity;

    /**
     * @var Config
     */
    protected $config;


    /**
     * @param EntityFaceTrait $entity
     * @param Config $config
     * @throws \Exception
     */
    public function __construct(EntityInterface $entity,Config $config = null)
    {
        if(!$config){
            $config = Config::getDefault();
        }
        $this->config = $config;

        $this->entity = $entity;

        parent::__construct($entity->getEntityFace($config->getFaceLoader()));
    }
}