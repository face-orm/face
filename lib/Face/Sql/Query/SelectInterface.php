<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 11/2/15
 * Time: 10:28 PM
 */

namespace Face\Sql\Query;


use Face\Sql\Query\Clause\OrderBy\Field;
use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\Clause\Where\WhereGroup;
use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\QueryFace;

interface SelectInterface extends QueryInterface
{

    /**
     * @return JoinQueryFace[]
     */
    public function getJoins();

    /**
     * @return JoinQueryFace
     */
    public function getSoftThroughJoin();

    /**
     * @param string $path
     * @return boolean
     */
    public function isJoined($path);

    /**
     * @return WhereGroup
     */
    public function getWhere();

    /**
     * @return Field[]
     */
    public function getOrderBy();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return int
     */
    public function getOffset();

    /**
     * @return Column[]
     */
    public function getSelectedColumns();

}
