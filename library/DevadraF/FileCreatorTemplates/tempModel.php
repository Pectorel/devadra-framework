<?php
/**
 * Created by PhpStorm.
 * User: Aurélien
 * Date: 27/05/2018
 * Time: 18:04
 */

$modelString = "
<?php
class tempModel extends Model
{
    
    protected \$_instance;
    protected \$_table = null;
    
    protected \$_referenceMap = array();
    
    protected \$_careDepent = array();
    
}
";