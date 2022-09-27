<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 19/08/2017
 * Time: 20:31
 */

class PDO_DB
{

    private static $instance = null;

    private function __construct() {}

    private function __clone() {}

    /**
     * @return PDO|null
     */
    public static function getInstance()
    {

        $ini = parse_ini_file("./config.ini", true);

        $dbarray = $ini["db"];

        if(Generale::isLocal())
        {

            $dbarray = $ini["dblocal"];

        }

        $host = $dbarray["host"];
        $login = $dbarray["login"];
        $pass = base64_decode($dbarray["pass"]);
        $dbname = $dbarray["dbname"];




        if(!isset(self::$instance))
        {

            try
            {
                $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
                $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
                self::$instance = new PDO("mysql:host=" . $host . ":3306;dbname=" . $dbname,$login,$pass, $pdo_options);
            }
            catch(PDOException $e)
            {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }

        }
        return self::$instance;
    }

}