<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 29/08/2018
 * Time: 17:43
 */

class Paginator
{

    private $_modelM;
    protected $_itemPerPage;
    protected $_nbPages;
    protected $_name;
    protected $_res;
    protected $_page;
    protected $_fullres;


    public function __construct($name, $modelM, $options)
    {

        $this->_name = $name;
        $this->_modelM = $modelM;



        if(empty($_GET["page"]))
        {
            $this->_page = 1;
        }
        else{
            $this->_page = $_GET["page"];
        }


        if(!empty($options["itemPerPage"]))
        {
            $this->_itemPerPage = $options["itemPerPage"];
        }
        elseif(!empty($options["nbPages"]))
        {
            $this->_nbPages = $options["nbPages"];
        }

        if(empty($options["noFullRequest"]))
        {
            $this->doFullRequest();

            $this->calculateAll();


        }

        //var_dump($this->_itemPerPage);

        if(empty($options["noRequest"]))
        {
            $this->doRequest();
        }


    }


    public function doFullRequest($admin = true, $sql = "", $params = array())
    {

        if($admin)
        {
            $this->_fullres = $this->_modelM->filterAdmin(PDO::FETCH_NUM);
        }
        else if(!empty($sql)){
            $this->_fullres = $this->_modelM->select($sql, $params);
        }

    }

    public function doRequest($admin = true, $sql = "", $params = array())
    {

        if($admin)
        {

            $options = array();
            $options["Limit"] = $this->_itemPerPage . " OFFSET " . ($this->_itemPerPage * ($this->_page - 1));
            $this->_res = $this->_modelM->filterAdmin(PDO::FETCH_NUM, $options);

        }
        else if(!empty($sql)){

            $sql.= " LIMIT " . $this->_itemPerPage . " OFFSET " . ($this->_itemPerPage * ($this->_page - 1));
            //var_dump($sql);
            $this->_res = $this->_modelM->select($sql, $params);
        }


    }

    public function getRes()
    {
        return $this->_res;
    }


    /**
     * @return mixed
     */
    public function getItemPerPage()
    {
        return $this->_itemPerPage;
    }

    /**
     * @return mixed
     */
    public function getNbPages()
    {
        return $this->_nbPages;
    }

    public function getCurrentPage()
    {
        return $this->_page;
    }


    protected function _calculate($divider)
    {
        $length = count($this->_fullres);
        return ceil($length / $divider);
    }

    public function calculateAll()
    {

        if(!empty($this->_itemPerPage))
        {
            $this->_nbPages = $this->_calculate($this->_itemPerPage);
        }
        elseif(!empty($this->_nbPages))
        {
            $this->_itemPerPage = $this->_calculate($this->_nbPages);
        }

    }

    public function savePaginator()
    {
        $_SESSION[$this->_name] = serialize($this);
    }

    public static function getPaginator($name)
    {
        return unserialize($_SESSION[$name]);
    }





}