<?php 
namespace Views\Adv;
use Core\Main;
class Statistics extends \Core\Views
{
    function __construct($data){
        parent::__construct($data);
        
        global $module;

        \Core\Main::includeJavaScriptSource("libs/Chart.js");
        
        $module .= "
        <h2>Jogadores ativos</h2>
        		
        <form action='#' type='POST'>
        	<fieldset>
	        	<p>
		       		<label>Dias atrás</label>
		        	<input type='text' id='chart_days_ago' value='30'/>
	        	</p>
	        		
	        	<p>
	        		<input class='button' type='button' id='chart_update' value='Enviar'>
	        	</p>
        	</fieldset>
        </form>
        		
        <canvas id='chart_activePlayers' style='margin-top: 25px;' width='600' height='400'></canvas>				
        <canvas id='chart_onlinePlayers' style='margin-top: 25px;' width='600' height='400'></canvas>				
        ";
    }
}
?>