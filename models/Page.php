
<?php
class Page extends Model
{
    
    protected $_instance;
    protected $_table = "Page";
    
    protected $_referenceMap = array(
		"Template" => array(
                "table" => "Template",
                "foreign" => "Template_id",
                "constraint" => "id",
                "showColumn" => "intitule"
		),
	);
    
    protected $_careDepent = array(
        "Paragraphe" => "Paragraphe"
    );
    
}
