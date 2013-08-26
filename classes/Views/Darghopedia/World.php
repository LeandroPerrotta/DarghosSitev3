<?php 
namespace Views\Darghopedia;
class World extends \Core\Views
{
    function __construct($data){
        parent::__construct($data);
        
        global $module;
        
        $url = "?ref=darghopedia.world";
        
        $module .= "
        <p>Bem vindo ao Darghos Map Explorer. Alguma vez você já precisou ir em um lugar e não sabia como chegar? A localização de um NPC? Uma quest? Ou mesmo uma area de hunt? Através desta pagina você poderá de maneira facil explorar todo o nosso mapa, tornando a sua vida no Darghos mais prática.</p> 
        <p style='font-weight: bold;'>Obs: Esta ferramenta ainda está em fase BETA portanto você pode encontrar erros.</p>
        <h3 style='margin-top: 20px;'>Cidades</h3>
        <ul>
            <li><a href='{$url}'>Quendor</a></li>
            <li><a href='{$url}&posx=2121&posy=1601&posz=7'>Thorn</a></li>   
            <li><a href='{$url}&posx=2691&posy=901&posz=7'>Aaragon</a></li>
            <li><a href='{$url}&posx=3041&posy=1751&posz=7'>Aracura</a></li>
            <li><a href='{$url}&posx=1901&posy=2451&posz=7'>Salazart</a></li>
            <li><a href='{$url}&posx=1691&posy=1031&posz=7'>Northrend</a></li>
            <li><a href='{$url}&posx=801&posy=891&posz=7'>Kashmir</a></li>
            <li><a href='{$url}&posx=971&posy=1971&posz=7'>Island of Peace</a></li>
        </li>
        <div id='map-viewer' >
            <img id='map-image' usemap='#mapmarks'></img> 
            <input type='hidden' name='base_x' id='base_x' value='{$_GET["posx"]}'/>
            <input type='hidden' name='base_y' id='base_y' value='{$_GET["posy"]}'/>
            <input type='hidden' name='base_z' id='base_z' value='{$_GET["posz"]}'/>
        </div>
        <map name='mapmarks'>
            <area shape='circle' coords='1971,1835,10' href='#' title='Quendor'>
        </map>        
        <div id='map-controls'>
            <div id='control-dir'>
                <div id='control-north' class='control-button'></div>
                <div id='control-east' class='control-button'></div>
                <div id='control-south' class='control-button'></div>
                <div id='control-west' class='control-button'></div>
            </div>
            
            <div id='control-floor'>
                <div id='control-floorup' class='control-button'></div>
                <div id='control-floordown' class='control-button'></div>
            </div>
            
            <span id='control-cords'></span>
        </div>
        
        <fieldset>
        <p>URL da posição atual <input type='text' size='80' value='' id='control-url'></p>
        </fieldset>
        ";
    }
}
?>