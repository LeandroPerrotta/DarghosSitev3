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
            <li><a href='{$url}&posx=2362&posy=1815&posz=7'>Thorn</a></li>   
            <li><a href='{$url}&posx=2904&posy=1144&posz=7'>Aaragon</a></li>
            <li><a href='{$url}&posx=3323&posy=1980&posz=7'>Aracura</a></li>
            <li><a href='{$url}&posx=2149&posy=2667&posz=7'>Salazart</a></li>
            <li><a href='{$url}&posx=1948&posy=1251&posz=7'>Northrend</a></li>
            <li><a href='{$url}&posx=1079&posy=1123&posz=7'>Kashmir</a></li>
            <li><a href='{$url}&posx=1218&posy=2201&posz=7'>Island of Peace</a></li>
        </li>
        <div id='map-viewer' >
            
            <div id='map-image'>
                
            </div>
                            
            </img> 
            <input type='hidden' name='base_x' id='base_x' value='{$_GET["posx"]}'/>
            <input type='hidden' name='base_y' id='base_y' value='{$_GET["posy"]}'/>
            <input type='hidden' name='base_z' id='base_z' value='{$_GET["posz"]}'/>
        </div>      
        <div id='map-mask'></div>        
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