
<?php
class ContenuController extends Controller
{

    public static $nom_a_afficher = "Contenu";

    protected $_colName = array(
        "id" => "",
        "titre" => "Titre (NE PAS CHANGER)",
        "contenu" => "Contenu",
        "Language_id" => "Langue"
    );


    protected $_colNotSelect = array(
	    "id"
	);



    protected $_colFilter = array(

		"titre",
        "Language_id"

	);

    
}
