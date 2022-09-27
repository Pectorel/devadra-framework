
<?php
class Paragraphe extends Model
{
    
    protected $_instance;
    protected $_table = "Paragraphe";
    
    protected $_referenceMap = array(
		"PositionPhoto" => array(
                "table" => "PositionPhoto",
                "foreign" => "PositionPhoto_id",
                "constraint" => "id",
                "showColumn" => "position"
		),"Page" => array(
                "table" => "Page",
                "foreign" => "Page_id",
                "constraint" => "id",
                "showColumn" => "titre"
		),
	);
    
    protected $_careDepent = array();
    
}
