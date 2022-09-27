
<?php
class DevadraConfig extends Model
{

    protected $_instance;
    protected $_table = "DevadraConfig";
    
    protected $_referenceMap = array(
		
	);
    
    protected $_careDepent = array();

    public static function isMaintenance()
    {

        $configM = new DevadraConfig();

        $maintenance = $configM->getVal("Maintenance");

       return ($maintenance["value"] == 0) ? false : true;

    }

    public function getVal($index)
    {

        $res = null;

        $sql = "SELECT value FROM DevadraConfig WHERE nom = ?";
        $params = array($index);


        $res = $this->select($sql, $params, PDO::FETCH_ASSOC, "fetch");

        return $res;

    }

    public function getVersion()
    {

        $res = $this->getVal("Version");

        if(empty($res["value"]) && $res["value"] !== "0")
        {

            $this->insert(array("nom" => "Version", "value" => 1));
            $res = 0;

        }else{
            $res = $res["value"];
        }

        return $res;
    }

    /*public function filter($data, $no_check = array("id", "_ssid", "nom"))
    {
        return parent::filter($data, $no_check);
    }*/

}
