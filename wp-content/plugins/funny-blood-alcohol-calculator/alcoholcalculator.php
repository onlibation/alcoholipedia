<?php
/*
Plugin Name: Alcohol Calculator
Plugin URI: http://www.farbundstil.de/games/1382-blood-alcohol-calculator-plugin.php
Description: Put an entertaining Blood Alcohol Calculator on your blog
Version: 1.0
Author: Marcel Hollerbach
Author URI: http://www.farbundstil.de

Instructions

Requires at least Wordpress: 2.1.3

1. Upload the alcoholcalculator folder to your wordpress plugins directory (./wp-content/plugins)
2. Login to the Wordpress admin panel and activate the plugin "Alcohol Calulator"
3. Create a new post or page and enter the tag [BAC]

That's it ... Have fun!


*/

define("BAC_REGEXP", "/\[BAC]/");

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

define('BAC_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );

add_action('wp_head', 'BAC_addcss', 1);

//Add stylesheet to site
function BAC_addcss(){

    echo "<link rel=\"stylesheet\" href=\"". BAC_URLPATH. "bac_style.css\"  type=\"text/css\" media=\"screen\" />";

}

function BAC_plugin_callback($match)
{

    //Customize the labels
    $LANG_DRINKAMOUNT   =   "Amount of dinks";
    $LANG_BEER          =   "Beer";
    $LANG_WINE          =   "Wine";
    $LANG_COCKTAILS     =   "Cocktails";
    $LANG_WEIGHT        =   "Weight";
    $LANG_GENDER        =   "Gender";
    $LANG_FEMALE        =   "Female";
    $LANG_MALE          =   "Male";
    $LANG_CALCULATE     =   "Calculate";
    $LANG_ALCBLOOD      =   "Alcohol in blood: ";
    
    
    // DO NOT EDIT BELOW THIS LINE /////////////////////////////////////////////////////////////////////////////
    
	$output .= "
               <script language=\"JavaScript\">
                    	<!--
                            function calculate(){
                                    var result = document.getElementById('result');
                                    var weight = document.getElementById('weight');
                                    var male = document.getElementById('male');
                                    var beer = document.getElementById('beer');
                                    var wine = document.getElementById('wine');
                                    var cocktail = document.getElementById('cocktail');
                                    var result_image = document.getElementById('result_image');
                            
                                    var C = 0;
                                    var A = 0;
                                    var r = 0.6;
                                    if(male.checked)
                                            r = 0.7;
                                    if(beer.value.length > 0)
                                            A += beer.value  * 33 * 4.6 * 0.08;
                                    if(wine.value.length > 0)
                                            A += wine.value  * 20 * 12 * 0.08;
                                    if(cocktail.value.length > 0)
                                            A += cocktail.value  * 60 * 8 * 0.08;
                                    if(!isNaN(A))
                                            C = A / weight[weight.selectedIndex].value * r;
                                    if(C.toFixed(2))
                                            C = C.toFixed(2);
                                    if (C >= 4)
                                            result_image.src = \"" . BAC_URLPATH . "images/image5.jpg\";
                                    else if (C >=3 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image5.jpg\";
                                    else if (C >=2.5 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image4.jpg\";
                                    else if (C >=2 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image4.jpg\";
                                    else if (C >=1.5 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image4.jpg\";
                                    else if (C >=1 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image3.jpg\";
                                    else if (C >=0.8 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image3.jpg\";
                                    else if (C >=0.5 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image1.jpg\";
                                    else if (C >=0.3 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image1.jpg\";
                                    else if (C >=0 )
                                            result_image.src = \"" . BAC_URLPATH . "images/image0.jpg\";
                                    result.innerHTML = C;
                            
                                    return null;      
                            }
                               //-->
                    	</script>
	
	               <table>
                    <tr>
                    <td style=\"padding-left:65px\">
                        
                    	<table id=\"calculator\" width=\"250\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\" class=\"bac_box\">
                    	  <tr>
                    		<td style=\"padding:5px\"> 
                    		  <div align=\"center\" id=\"result\" class=\"bac_calc_text\"></div>
                    		  <center>
                    				<img src=\"" . BAC_URLPATH . "/images/logo.jpg\" id=\"result_image\" border=\"1\" alt=\"\">
                    		  </center>
                    		  <table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                    			<tr>
                    			  <td class=\"bac_calc_header\"><br /><b>" . $LANG_DRINKAMOUNT . "</b></td>
                    			</tr>
                    		  </table>
                    		  <table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                    			<tr> 
                    			  <td width=\"119\"> 
                    				<input type=\"text\" id=\"beer\" name=\"beer\" size=\"10\">
                    			  </td>
                    			  <td width=\"131\" class=\"bac_calc_text\"> " .$LANG_BEER." <i>0,33</i> (4.6%)</td>
                    			</tr>
                    			<tr> 
                    			  <td width=\"119\"> 
                    				<input type=\"text\" id=\"wine\" name=\"wine\" size=\"10\">
                    			  </td>
                    			  <td width=\"131\" class=\"bac_calc_text\"> ".$LANG_WINE." <i>0,2</i> (12%)</td>
                    			</tr>
                    			<tr> 
                    			  <td width=\"119\"> 
                    				<input type=\"text\" id=\"cocktail\" name=\"cocktail\" size=\"10\">
                    			  </td>
                    			  <td width=\"131\" class=\"bac_calc_text\"> ".$LANG_COCKTAILS." <i>0,5</i> (8%)</td>
                    			</tr>
                    		  </table>
                    		  <table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                    			<tr>
                    			  <td width=\"119\"> 
                    				<select style=\"width:90px\" name=\"select\" id=\"weight\"></select>
                    			  </td>
                    			  <td width=\"131\" class=\"bac_calc_text\">" .$LANG_WEIGHT. "</td>
                    			</tr>
                    		  </table>
                    		  <div class=\"bac_calc_header\"><br><b>" .$LANG_GENDER. "</b></div>
                    		  <table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                    			<tr>
                    			  <td class=\"bac_calc_text\">
                    				<input type=\"radio\" name=\"gender\" id=\"female\" checked=\"true\">
                    				" .$LANG_FEMALE. " </td>
                    			  <td class=\"bac_calc_text\">
                    				<input type=\"radio\" name=\"gender\" id=\"male\">
                    				" .$LANG_MALE." </td>
                    			</tr>
                    		  </table>
                    		  <p>
                    			<input type=\"submit\" id=\"submit_button\" onclick=\"calculate()\" class=\"bac_button\" name=\"Abschicken\" value=\"" .$LANG_CALCULATE."\">
                    		  </p>
                    		</td>
                    	  </tr>
                    	</table>
                    	<a href=\"http://www.farbundstil.de\" title=\"Copyright, Frisuren, Bilder, Kleider\" >
                    	<script language=\"JavaScript\">
                    	<!--
                    	var weight = document.getElementById('weight');
                    	for(i=0; i<111; i++)
                    		weight.options[i] = new Option(i+40+' kg',i+40);
                    	//-->
                    	</script>
                    	</a>
                    </td>
                  </tr>
               </table>";

	return ($output);
}

function BAC_plugin($content)
{
	return (preg_replace_callback(BAC_REGEXP, 'BAC_plugin_callback', $content));
}

add_filter('the_content', 'BAC_plugin');
add_filter('comment_text', 'BAC_plugin');



?>
