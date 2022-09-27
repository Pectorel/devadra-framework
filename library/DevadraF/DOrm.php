<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 25/09/2018
 * Time: 16:02
 */

class DOrm
{

    protected $_instance = null;
    protected $_sql = null;
    protected $_params = array();
    protected $_table = null;
    protected $_referenceMap = array();
    protected $_careDepent = array();


    protected function _connect()
    {
        //PDO
        $this->_instance = PDO_DB::getInstance();
    }

    public function select($cols = array())
    {
        $this->_sql = "SELECT";

        if(!empty($cols))
        {
            foreach ($cols as $col)
            {
                $this->_sql.= " " . $col;
            }
        }

        $this->_sql.= " FROM " . $this->_table . " ";

    }


    public function where($cond, $param)
    {

        if(!empty($conds))
        {
           $this->_sql.= "AND ";
        }
        else
        {
            $this->_sql.= "WHERE ";
        }

        $this->_sql.= $cond . " ";

        array_push($this->_params, $param);

    }


    public function orWhere($cond, $param)
    {

        $this->_sql.= "OR " . $cond;
        array_push($this->_params, $param);

    }



    public function toString()
    {
        return $this->_sql;
    }


    public function fetchAll($fetch_type = PDO::FETCH_ASSOC)
    {

        $res = null;

        if(empty($this->_sql))
        {
            if (!empty($this->_table))
            {
                $this->_sql = "SELECT * FROM {$this->_table}";
            }

        }

        $stmt = $this->_instance->prepare($this->_sql);
        $stmt->execute($this->_params);
        $res = $stmt->fetchAll($fetch_type);

        return $res;

    }








}