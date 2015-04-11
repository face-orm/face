<?php


namespace Face\Sql\Query\Clause;


use Face\Core\EntityFace;
use Face\Sql\Query\Clause\OrderBy\Field;
use Face\Sql\Query\FQuery;

class OrderBy implements SqlClauseInterface {

    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * @param Field $items
     */
    public function addItem(Field $field)
    {
        $this->fields[] = $field;
    }



    public function getSqlString(FQuery $q)
    {

        $str = "";

        if (count($this->fields) > 0) {
            $str = "ORDER BY";
            $i = 0;
            foreach($this->fields as $orderBy){
                if($i>0){
                    $str.=",";
                }
                $str .= " " . $orderBy->getSqlString($q);
                $i++;
            }
        }

        return $str;

    }


}
