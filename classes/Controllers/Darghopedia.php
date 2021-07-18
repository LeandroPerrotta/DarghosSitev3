<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;

class Darghopedia
{
    function World(){
        
        \Core\Main::includeStylecheestSource("map");
        
        $data = array();
        new \Views\Darghopedia\World($data);
        
        return true;
    }
}
?>