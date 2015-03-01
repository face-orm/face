<?php

namespace Face\Sql\Query;


use Face\Config;
use Face\Traits\EntityFaceTrait;
use Face\Util\OOPUtils;

/**
 *
 * It represents a query :
 * - that is not a read query (only modification) : update/insert/delete
 * - that is linked to only one entity (that's why it's called simple).
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
    public function __construct($entity,Config $config = null)
    {
        if(!$config){
            $config = Config::getDefault();
        }
        $this->config = $config;

        if (!OOPUtils::UsesTrait($entity, 'Face\Traits\EntityFaceTrait')) {
            throw new \Exception("Class ".get_class($entity)." doesnt use the trait Face\Traits\EntityFaceTrait");
        }

        $this->entity = $entity;

        parent::__construct($entity->getEntityFace($config->getFaceLoader()));
    }
}