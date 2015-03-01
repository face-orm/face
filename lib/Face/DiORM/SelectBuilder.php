<?php

namespace Face\DiORM;

use Face\Config;
use Face\Core\EntityFace;
use Face\DiORM;
use \Face\Sql\Query\SelectBuilder as baseSelectBuilder;

class SelectBuilder extends baseSelectBuilder {

    /**
     * @var DiORM
     */
    protected $diOrm;

    function __construct(EntityFace $baseFace, DiORM $diOrm)
    {
        parent::__construct($baseFace);
        $this->diOrm = $diOrm;
    }

    /**
     * @return \Face\Sql\Result\ResultSet
     */
    public function results()
    {
        return $this->diOrm->select($this);
    }
}