<?php

  if(isset($_GET['from_currency']) && isset($_GET['to_currency']) && isset($_GET['value']) && isset($_GET['xml_name']))
  {
    xmlParse($_GET['xml_name'],'Cube');	
  	$converted_value = calculate_value($_GET['value'], $_GET['from_currency'],$_GET['to_currency']); 
  }
  else
  {	
	$converted_value = '';
  }
  echo $converted_value;
  
function xmlParse($file,$wrapperName){
    global $rates;
    $xml = new XMLReader();
    if(!$xml->open($file)){
        die("Failed to open input file.");
    }
    $n=0;
    $x=0;
    while($xml->read()){
   
        if($xml->nodeType==XMLReader::ELEMENT && $xml->name == $wrapperName){
            while($xml->read() && $xml->name != $wrapperName){
                 
                if($xml->nodeType==XMLReader::ELEMENT){
                    
                    $currency = $xml->getAttribute('currency');
                    $multiplier = $xml->getAttribute('multiplier');
                    if($multiplier == NULL) $multiplier = 1;

                    $xml->read();
                    $value = $xml->value; 
                    
                    $rates[$currency] = $value/$multiplier ;
                
                }
            }
            
        }
    }
    $xml->close();
}

function calculate_value($value, $from, $to)
{
 global $rates;
 
 $rates['RON'] = 1;
 $result = $value * $rates[$from]/$rates[$to];
 return $result;
}



?>