
<?php
/**
 * Created by PhpStorm.
 * User: Aurelien
 * Date: 21/01/2019
 * Time: 19:01
 */

class UpdateController extends Controller
{

    public function updateAction()
    {

        ini_set('max_execution_time', 6000);

        $devConfM = new DevadraConfig();

        $version = $devConfM->getVersion();
        $version = intval($version);

        while(method_exists($this, "_v".($version+1)))
        {

            $this->{"_v".($version+1)}();
            $version += 1;
        }

        $devConfM->update(array("nom" => "Version", "value" => $version), "WHERE nom = ?", array("version"));

        echo "<br/>Update terminée";
        exit();

    }

    public function __call($name, $arguments)
    {

        echo "<br />Update terminée";
        exit();

    }


}