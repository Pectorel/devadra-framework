
<?php
class AdminLanguage extends Model
{
    
    protected $_instance;
    protected $_table = "AdminLanguage";
    
    protected $_referenceMap = array(
        "Language" => array(
            "table" => "Language",
            "foreign" => "Language_id",
            "constraint" => "id",
            "showColumn" => "nom"
        ),
        "Admin" => array(
            "table" => "Admin",
            "foreign" => "Admin_id",
            "constraint" => "id",
            "showColumn" => "login"
        ),
    );
    
    protected $_careDepent = array();


    public function getDisplayName($obj)
    {

        $adminM = new Admin();

        $id = empty($obj["id"]) ? $obj[1] : $obj["id"];

        $sql = "SELECT * FROM Admin WHERE id = ?";
        $params = array($id);

        $admin = $adminM->select($sql, $params, PDO::FETCH_ASSOC, "fetch");

        return $admin["login"];

    }
    
}
