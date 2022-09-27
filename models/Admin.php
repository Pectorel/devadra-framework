<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 27/08/2017
 * Time: 00:37
 */

class Admin extends Model
{

    protected $_instance;
    protected $_table = "Admin";

    public function getConnectedUser()
    {

        $adminsql = "SELECT * FROM Admin WHERE _ssid = ?";
        $adminparam = array($_SESSION["session_id"]);

        $user = $this->select($adminsql, $adminparam, PDO::FETCH_ASSOC, "fetch");

        return $user;

    }



}