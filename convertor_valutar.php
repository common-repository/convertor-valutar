<?php

/************************************************************************************
** Plugin Name: Convertor valutar
** Description: Convertor valutar bazat pe cursul oficial al BNR pentru ziua curenta.
                A widget that provides currency convertion calculator, based on
                current BNR(National Bank of Romania) exchange rates.
               
** Author: Ioana Orhei
** Author URI:
** Plugin URI:
** Version: 1.3
*************************************************************************************/


define(CONV_TEXTDOMAIN, 'convertor_valutar');

define('XML_FILENAME_FROM_BNR', 'http://bnr.ro/nbrfxrates.xml');
define('UPDATE_PERIOD',24*60*60); // 24h * 60min * 60sec - set one day into seconds


//--------------------------------------------------------------------
function currency_conv_init() {
  load_plugin_textdomain( CONV_TEXTDOMAIN, false, 'convertor-valutar/languages' );
}
add_action('plugins_loaded', 'currency_conv_init');

function my_plugin_activate() {
  //reads for the first time the xml file from BNR
  update_option('last_xml_timestamp',0);
  file_put_contents(WP_PLUGIN_DIR ."/convertor-valutar/xml/0.xml",file_get_contents(XML_FILENAME_FROM_BNR));
}
register_activation_hook( __FILE__, 'my_plugin_activate' );

//--------------------------------------------------------------------
function my_plugin_deactivate() {
  delete_option('last_xml_timestamp');
}
register_deactivation_hook( __FILE__, 'my_plugin_deactivate' );

function My_WidgetOutput($args)
{
    // extract the parameters
    extract($args);
    // get our options
    $options=get_option('my_converter');
    $title=$options['converter_title'];
    // print the theme compatibility code
    echo $before_widget;
    echo $before_title . $title. $after_title;
    // include our widget
 
      My_Currency_Converter();

    echo $after_widget;
}

function My_WidgetInit()
{      
      
        wp_register_sidebar_widget('my_curr_converter',__('Convertor valutar',CONV_TEXTDOMAIN),'My_WidgetOutput', array('description' =>__('Convertor valutar bazat pe cursul oficial al BNR',CONV_TEXTDOMAIN)) );
        wp_register_widget_control('my_curr_converter',__('Convertor valutar',CONV_TEXTDOMAIN),'My_WidgetControl', array('description' =>__('Convertor valutar bazat pe cursul oficial al BNR',CONV_TEXTDOMAIN)) );
}
 
function My_WidgetControl()
{
    $options = get_option('my_converter');
    // handle user input
    if ( $_POST["converter_submit"] )
    {
        $options['converter_title'] = $_POST["converter_title"] ;
        update_option('my_converter', $options);
    }
    $title = $options['converter_title'];
    echo '
    <p>
    <label for="converter_title">'.__('Titlu:',CONV_TEXTDOMAIN).' <input name="converter_title"
    type="text" value="'.$title.'" /></label>
    <input type="hidden" id="converter_submit" name="converter_submit"
    value="1" />
    </p> ';
   
}

add_action('init', 'My_WidgetInit');



function My_Currency_Converter(){
 
   $last_updated_xml_name = get_the_xml_filename();
 
   echo '
   <script type="text/javascript">
    function convert()
    {
        var xmlhttp,val,from,to;
        var curr_codes = new Array("AED","AUD","BGN","BRL","CAD","CHF","CNY","CZK","DKK","EGP","EUR","GBP","HUF","INR","JPY","KRW","MDL","MXN","NOK",
                                "NZD","PLN","RON","RSD","RUB","SEK","TRY","UAH","USD","ZAR");
        val = document.getElementById("val").value.toString();
        from = curr_codes[document.getElementById("from").selectedIndex];
        to = curr_codes[document.getElementById("to").selectedIndex];
        last_updated_xml_name= document.getElementById("last_updated_xml_name").value;
        if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
 
               xmlhttp=new XMLHttpRequest(); 
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                document.getElementById("conv_value").value=xmlhttp.responseText;
            }
         }

    xmlhttp.open("GET","wp-content/plugins/convertor-valutar/convertor.php?value="+val+"&from_currency="+from+"&to_currency="+to+"&xml_name="+last_updated_xml_name,true);
    xmlhttp.send();
    }
   </script>';

  echo "
   <div style='width:155px; height:280px; border:1px solid #CCC;padding:5px;background:#DDD;'>
     <form name='myoption' action='' >
       ".__('Suma:',CONV_TEXTDOMAIN)."
       <br/>
      <input name ='value' type ='text'  id = 'val' value ='' size='15';/>
      <br/><br/>
      ".__('Din:',CONV_TEXTDOMAIN)."
      <br/>
   <select name ='from_curr' id='from' style='width: 145px'>
    <option   value='AED'>(AED)".__('Dirhamul Emiratelor Arabe',CONV_TEXTDOMAIN)." </option>
    <option   value='AUD'>(AUD)".__('Dolarul australian',CONV_TEXTDOMAIN)."</option>
    <option   value='BGN'>(BGN)".__('Leva bulgareasca',CONV_TEXTDOMAIN)."</option>
    <option   value='BRL'>(BRL)".__('Realul brazilian',CONV_TEXTDOMAIN)."</option>
    <option   value='CAD'>(CAD)".__('Dolarul canadian',CONV_TEXTDOMAIN)."</option>
    <option   value='CHF'>(CHF)".__('Francul elvetian',CONV_TEXTDOMAIN)."</option>
    <option   value='CNY'>(CNY)".__('Yuan-ul chinezesc',CONV_TEXTDOMAIN)."</option>
    <option   value='CZK'>(CZK)".__('Coroana ceha',CONV_TEXTDOMAIN)." </option>
    <option   value='DKK'>(DKK)".__('Coroana daneza',CONV_TEXTDOMAIN)."</option>
    <option   value='EGP'>(EGP)".__('Lira egipteana',CONV_TEXTDOMAIN)."</option>
    <option   value='EUR'>(EUR)Euro</option>
    <option   value='GBP'>(GBP)".__('Lira sterlina',CONV_TEXTDOMAIN)."</option>
    <option   value='HUF'>(HUF)".__('Forintul maghiar',CONV_TEXTDOMAIN)."</option>
    <option   value='INR'>(INR)".__('Rupia indiana',CONV_TEXTDOMAIN)."</option>
    <option   value='JPY'>(JPY)".__('Yen-ul japonez',CONV_TEXTDOMAIN)."</option>
    <option   value='KRW'>(KRW)".__('Won-ul sud-corean',CONV_TEXTDOMAIN)."</option>
    <option   value='MDL'>(MDL)".__('Leul moldovenesc',CONV_TEXTDOMAIN)."</option>
    <option   value='MXN'>(MXN)".__('Peso-ul mexican',CONV_TEXTDOMAIN)."</option>
    <option   value='NOK'>(NOK)".__('Coroana norvegiana',CONV_TEXTDOMAIN)."</option>
    <option   value='NZD'>(NZD)".__('Dolarul neo-zeelandez',CONV_TEXTDOMAIN)."</option>
    <option   value='PLN'>(PLN)".__('Zlotul polonez',CONV_TEXTDOMAIN)."</option>
    <option   value='RON'>(RON)".__('Leul romanesc',CONV_TEXTDOMAIN)."</option>
    <option   value='RSD'>(RSD)".__('Dinarul sarbesc',CONV_TEXTDOMAIN)."</option>
    <option   value='RUB'>(RUB)".__('Rubla ruseasca',CONV_TEXTDOMAIN)."</option>
    <option   value='SEK'>(SEK)".__('Coroana suedeza',CONV_TEXTDOMAIN)."</option>
    <option   value='TRY'>(TRY)".__('Noua lira turceasca',CONV_TEXTDOMAIN)."</option>
    <option   value='UAH'>(UAH)".__('Hryvna ucraineana',CONV_TEXTDOMAIN)."</option>
    <option   value='USD'>(USD)".__('Dolarul american',CONV_TEXTDOMAIN)."</option>
    <option   value='ZAR'>(ZAR)".__('Rand-ul sud-african',CONV_TEXTDOMAIN)."</option>

  </select>
  <br/><br/>
  ".__('In:',CONV_TEXTDOMAIN)."
  <br/>
  <select name ='to_currency' id ='to' style='width: 145px'>
    <option   value='AED'>(AED)".__('Dirhamul Emiratelor Arabe',CONV_TEXTDOMAIN)." </option>
    <option   value='AUD'>(AUD)".__('Dolarul australian',CONV_TEXTDOMAIN)."</option>
    <option   value='BGN'>(BGN)".__('Leva bulgareasca',CONV_TEXTDOMAIN)."</option>
    <option   value='BRL'>(BRL)".__('Realul brazilian',CONV_TEXTDOMAIN)."</option>
    <option   value='CAD'>(CAD)".__('Dolarul canadian',CONV_TEXTDOMAIN)."</option>
    <option   value='CHF'>(CHF)".__('Francul elvetian',CONV_TEXTDOMAIN)."</option>
    <option   value='CNY'>(CNY)".__('Yuan-ul chinezesc',CONV_TEXTDOMAIN)."</option>
    <option   value='CZK'>(CZK)".__('Coroana ceha',CONV_TEXTDOMAIN)." </option>
    <option   value='DKK'>(DKK)".__('Coroana daneza',CONV_TEXTDOMAIN)."</option>
    <option   value='EGP'>(EGP)".__('Lira egipteana',CONV_TEXTDOMAIN)."</option>
    <option   value='EUR'>(EUR)Euro</option>
    <option   value='GBP'>(GBP)".__('Lira sterlina',CONV_TEXTDOMAIN)."</option>
    <option   value='HUF'>(HUF)".__('Forintul maghiar',CONV_TEXTDOMAIN)."</option>
    <option   value='INR'>(INR)".__('Rupia indiana',CONV_TEXTDOMAIN)."</option>
    <option   value='JPY'>(JPY)".__('Yen-ul japonez',CONV_TEXTDOMAIN)."</option>
    <option   value='KRW'>(KRW)".__('Won-ul sud-corean',CONV_TEXTDOMAIN)."</option>
    <option   value='MDL'>(MDL)".__('Leul moldovenesc',CONV_TEXTDOMAIN)."</option>
    <option   value='MXN'>(MXN)".__('Peso-ul mexican',CONV_TEXTDOMAIN)."</option>
    <option   value='NOK'>(NOK)".__('Coroana norvegiana',CONV_TEXTDOMAIN)."</option>
    <option   value='NZD'>(NZD)".__('Dolarul neo-zeelandez',CONV_TEXTDOMAIN)."</option>
    <option   value='PLN'>(PLN)".__('Zlotul polonez',CONV_TEXTDOMAIN)."</option>
    <option   value='RON'>(RON)".__('Leul romanesc',CONV_TEXTDOMAIN)."</option>
    <option   value='RSD'>(RSD)".__('Dinarul sarbesc',CONV_TEXTDOMAIN)."</option>
    <option   value='RUB'>(RUB)".__('Rubla ruseasca',CONV_TEXTDOMAIN)."</option>
    <option   value='SEK'>(SEK)".__('Coroana suedeza',CONV_TEXTDOMAIN)."</option>
    <option   value='TRY'>(TRY)".__('Noua lira turceasca',CONV_TEXTDOMAIN)."</option>
    <option   value='UAH'>(UAH)".__('Hryvna ucraineana',CONV_TEXTDOMAIN)."</option>
    <option   value='USD'>(USD)".__('Dolarul american',CONV_TEXTDOMAIN)."</option>
    <option   value='ZAR'>(ZAR)".__('Rand-ul sud-african',CONV_TEXTDOMAIN)."</option>
  </select>
  <br/>
  <br/>
   ".__('Rezultat:',CONV_TEXTDOMAIN)."
 <br/>
<input  type= 'text' id='conv_value' value='' size='15' style='border:1px solid #FFA347;color:#FFA347';/>
<input type= 'hidden' id='last_updated_xml_name' value = ".$last_updated_xml_name." />
  </form>
<div style='width:88px;margin:5px auto;'> <button type= 'button' onclick ='convert()' style='background:#BBB;'>".__('Converteste',CONV_TEXTDOMAIN)."</button></div>
 </div>
";
 
}




//--------------------------------------------------------------------------------

function get_first_sec_of_day() {
    $month = date("m", time());
    $day = date("d", time());
    $year = date("Y", time());
// Syntax: mktime(hour, minute, second, month, day, year)
    return mktime(0,0,0,$month,$day,$year);
}

//--------------------------------------------------------------------------------

function get_the_xml_filename() {
    /*if (get_option('last_xml_timestamp')== false)
    {
        update_option('last_xml_timestamp', 0 );
    }*/

  return WP_PLUGIN_DIR ."/convertor-valutar/xml/" . get_option('last_xml_timestamp'). ".xml";
}



//--------------------------------------------------------------------------------
//
// This function will automatically and periodically
// download (once a day) the XML file from
// http://bnr.ro/nbrfxrates.xml
//
// This function is made to avoid the unnecessary URL requests for BNR site.
// This function also make some history of the BNR course in XML format.
function load_xml_once_a_day() {
    $current_time = time(); // get the current timestamp
    $last_xml_timestamp = get_option('last_xml_timestamp'); // get the last XML timestamp
   
    // if the current timestamp > (current XML timestamp + UPDATE_PERIOD) then
    //   donwload the new XML file and set the new current XML timestamp
    //   if the old content is the same with the new content do nothing
    //   download the new content of XML only if the new one is different
    if ( $current_time > ($last_xml_timestamp + UPDATE_PERIOD ))
    {
        //echo 'if ( $current_time...'; // for debug
        $local_xml_content = file_get_contents(get_the_xml_filename());
        $remote_xml_content = file_get_contents(XML_FILENAME_FROM_BNR); // BNR site request
        if ( $local_xml_content === $remote_xml_content )
        {
            // the old content is the same with the new content
        }
        else
            if ( $remote_xml_content ) {
                $first_sec_of_day = get_first_sec_of_day();
                update_option('last_xml_timestamp',$first_sec_of_day); // update $last_xml_timestamp
                file_put_contents(WP_PLUGIN_DIR ."/convertor-valutar/xml/$first_sec_of_day.xml",$remote_xml_content);
            }
    }
}

//--------------------------------------------------------------------------------

add_action('plugins_loaded','load_xml_once_a_day');



?>