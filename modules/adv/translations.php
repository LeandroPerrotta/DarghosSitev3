<?php

if(!$_GET["lang"]){
    
    $select = "<select name='lang' style='width: 120px'>";
    
    $select .= "<option value='en'>Inglês</option>";
    
    $select .= "</select>";
    
    $module .= "
    
    <form action='{$_SERVER['REQUEST_URI']}' method='GET'>
    
        <fieldset>
            <input type='hidden' name='ref' value='adv.translations'>
        
            <p>
                <label for='lang'>Idioma</label>
                {$select}
            </p>
            
			<div id='line1'></div>
			
			<p>
				<input class='button' type='submit' value='Enviar' />
			</p>	            
        </fieldset>
    </form>
    
    ";    
}
else{
    
    $file = "language/" . $_GET["lang"] . ".json";
    $temp = file_get_contents($file);
    
    if($temp){
        
        $data = json_decode($temp);
        
        if($_POST){
            
            $k = 0;
            
            foreach($data as $key => $value){
                
                $data->{$key} = stripslashes($_POST["key_{$k}"]);
                $k++;
            }
            
            $json = json_encode($data);
            file_put_contents($file, $json);
        }    
        
        $inputs = "";
        
        $k = 0;
        foreach($data as $key => $value){
            
            //$value = htmlspecialchars($value);
            //$key = htmlspecialchars($key);
            
            if(strlen($value) <= 128){
                $inputs .= "
                    <p style='margin-bottom: 25px;'>
                        <label for='{$key}'>{$key}</label>
                        <input type='text' name='key_{$k}' style='width: 300px;' value='{$value}'/>
                    </p>
                ";
            }
            else{
                $inputs .= "
                <p style='margin-bottom: 25px;'>
                    <label for='{$key}'>{$key}</label>
                    <textarea rows='16' cols='96' name='key_{$k}'>{$value}</textarea>
                </p>
                ";
            }
            
            $k++;
        }
        
        $module .= "
        
        <form action='{$_SERVER['REQUEST_URI']}' method='POST'>
        
            <fieldset>                
                {$inputs}
                
    			<div id='line1'></div>
    			
    			<p>
    				<input class='button' type='submit' value='Enviar' />
    			</p>                 
            </fieldset>           
        </form>
        
        ";        
    }
    else{
        $module .= "<h2>Arquivo de dados '{$file}' não encontrado.</h2>";
    }
}
?>