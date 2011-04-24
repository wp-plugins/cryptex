<?php
/**
	Plugin Name: Cryptex - EMail Obfuscator+Protector
	Plugin URI: http://www.a3non.org/go/cryptex
	Description: Advanced Graphical EMail Obfuscator which provides image based email address protection using wordpress shortcode and integrated encryption/decryption of addresses for hyperlinks
	Version: 1.3.2
	Author: Andi Dittrich
	Author URI: http://www.a3non.org
	License: MIT X11-License
	
	Copyright (c) 2010-2011, Andi Dittrich

	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

define('CRYPTEX_INIT', true);
define('CRYPTEX_VERSION', '1.3.1');
define('CRYPTEX_PLUGIN_PATH', dirname(__FILE__));
define('CRYPTEX_DEFAULT_FONT_PATH', CRYPTEX_PLUGIN_PATH.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR);

// generate js 
function Cryptex_generate_js(){
	// only include js if hyperlinks are enabled
	if(get_option('cryptex-enable-hyperlink', true)){ 
		// random key
		$key = '';
		for ($i=0;$i<32;$i++){
			$key .= chr(rand(48, 90));
		}	
		define('CRYPTEX_KEY', $key);
	
		echo '<script type="text/javascript" src="'.plugins_url('/cryptex/cryptex_compressed.js').'"></script>';
		echo '<script type="text/javascript">var CRYPTEX_KEY = \''.CRYPTEX_KEY.'\';</script>';
	}
}
add_action('wp_print_scripts', 'Cryptex_generate_js', 100, 0);

// generate css
function Cryptex_generate_css(){
	// only include css if enabled
	if(get_option('cryptex-embed-css', true)){ 
		// include static css file
		wp_register_style('cryptex-static', plugins_url('/cryptex/cryptex.css'));
		wp_enqueue_style('cryptex-static');
		
		// include dynamic css file
		wp_register_style('cryptex-dynamic', plugins_url('/cryptex/cache/dynamic.css'));
		wp_enqueue_style('cryptex-dynamic');
	}
}
add_action('wp_print_styles', 'Cryptex_generate_css', 100, 0);


// generate image components
function Cryptex_display($atts=NULL, $content="", $code=""){
	// get 2 parts
	$parts = explode('@', $content);
	
	if (count($parts)==2){	
		// get image url
		$imgURL0 = Cryptex_getImageURL($parts[0]);
		$imgURL1 = Cryptex_getImageURL($parts[1]);

		// generate html
		if(get_option('cryptex-enable-hyperlink', true)){ 
			$html .= '<span class="cryptex" rel="'.Cryptex_encrypt_email($content).'">';
		}else{
			$html .= '<span class="cryptex">';	
		}
		
		$html .= '<img src="'. $imgURL0 .'" alt="hidden" /><span class="divider">';
		$html .= get_option('cryptex-email-divider', '(at)');
		$html .= '</span><img src="'. $imgURL1 .'" alt="hidden" />';
		$html .= '</span>';
		return $html;
	}else{
		return '*invalid email*';	
	}
}
// add the actions
add_shortcode('cryptex', 'Cryptex_display');

// mail encryption - shift keying encryption
function Cryptex_encrypt_email($txt){
	// expand key on same length
	$key = str_repeat(CRYPTEX_KEY, strlen($txt)/strlen(CRYPTEX_KEY) + 1);
	
	// split strings
	$data = str_split($txt);
	$shifts = str_split($key);
	
	// output;
	$output = array();
	
	// shift
	for ($i=0;$i<count($data);$i++){
		$a = ord($data[$i]);
		$b = ord($shifts[$i]);
		$c = 49;
		
		// odd-even switch
		if ($i%2==0){
			$a += $b;
			
			// prevent overflow
			if ($a>255){
				$a -= 255;
				$c = 48;	
			}
		}else{
			$a -= $b;
			
			// prevent underflow
			if ($a<0){
				$a = -$a;
				$c = 48;	
			}
		}
		
		$output[] = $a;
		$output[] = $c;
	}
	
	// convert array to hex values
	$hex = '';	
	foreach ($output as $el){
		$hex .= str_pad (dechex($el), 2  ,'0', STR_PAD_LEFT);	
	}	
	return $hex;
}

// generate image
function Cryptex_getImageURL($txt){
	// url
	$file = sha1($txt).'.png';
	
	// if file not exists create it
	if (!file_exists(CRYPTEX_PLUGIN_PATH.'/cache/'.$file)){
		$fontsize = intval(get_option('cryptex-font-size', '12'));
		$fontfile = get_option('cryptex-font-path', '').get_option('cryptex-font', '');
		$fontcolor = hexdec(get_option('cryptex-font-color', '0x000000'));
		
		// check for valid font file
		if (is_file($fontfile)){
			// boundarys
			$boundaries = imagettfbbox($fontsize, 0, $fontfile, $txt);
			
			// calculate boundaries
			$min_x = min( array($boundaries[0], $boundaries[2], $boundaries[4], $boundaries[6]) );
			$max_x = max( array($boundaries[0], $boundaries[2], $boundaries[4], $boundaries[6]) );
			$min_y = min( array($boundaries[1], $boundaries[3], $boundaries[5], $boundaries[7]) );
			$max_y = max( array($boundaries[1], $boundaries[3], $boundaries[5], $boundaries[7]) );
			$width  = ( $max_x - $min_x );
			//$height = ( $max_y - $min_y ); 
			
			// pt to px lookup table
			// @source http://reeddesign.co.uk/test/points-pixels.html
			$PTtoPX = array(
				'6' => 8,
				'7' => 9,
				'8' => 11,
				'9' => 12,
				'10' => 13,
				'11' => 15,
				'12' => 16,
				'13' => 17,
				'14' => 19,
				'15' => 21,
				'16' => 22,
				'17' => 23,
				'18' => 24,
				'19' => 25,
				'20' => 26,
				'21' => 28,
				'22' => 29
			);
			
			// height based on font size ! - this can cause problems using big fonts -> pt<>px drift, but solves problems with font base lines..
			$height = $fontsize;
			
			// if using GD2 -> pt settings, convert pt height in px using lookup table
			if (Cryptex_get_font_size_format()=='pt'){
				if (array_key_exists($fontsize, $PTtoPX)){
					$height = $PTtoPX[$fontsize];	
				}
			}
			
			// create new image
			$im = imagecreatetruecolor($width+2, $height+2);
			
			// transparent background 
			$color = imagecolorallocatealpha($im, 0, 0, 0, 127);
			imagefill($im, 0, 0, $color);
			imagesavealpha($im, true); 
			
			// enable AA
			imageantialias($im, true);
			
			// create text		
			imagettftext($im, $fontsize, 0, 0, $fontsize+1, $fontcolor, $fontfile, $txt);
			
			// store image
			imagepng($im, CRYPTEX_PLUGIN_PATH.'/cache/'.$file);
			
			// destroy
			imagedestroy($im);
		}else{
			// FALLBACK
			$width = imagefontwidth(3)*strlen($txt);
			$height = imagefontheight(3);
			
			// create new image
			$im = imagecreatetruecolor($width+2, $height+7);
			
			// transparent background 
			$color = imagecolorallocatealpha($im, 0, 0, 0, 127);
			imagefill($im, 0, 0, $color);
			imagesavealpha($im, true); 
			
			// enable AA
			imageantialias($im, true);
			
			// create text
			imagestring($im, 3, 0, 0, $txt, $fontcolor);
			
			// store image
			imagepng($im, CRYPTEX_PLUGIN_PATH.'/cache/'.$file);
			
			// destroy
			imagedestroy($im);
		}
	}
	
	// return cache file url
	return plugins_url('/cryptex/cache/').$file;
}





// clear cache on update settings
function Cryptex_update_cache(){
	// cache dir
	$dir = CRYPTEX_PLUGIN_PATH.'/cache/';
	// remove cache files
	if (is_dir($dir)){
		$files = scandir($dir);
		foreach ($files as $file){
			if ($file!='.' && $file!='..'){
				unlink($dir.$file);	
			}
		}
	}
	
	// create dynamic css style	
	
	// get font parameters		
	$fontfamily = basename(get_option('cryptex-font', 'Arial'));
	$fontcolor = dechex(hexdec(get_option('cryptex-font-color', '0x000000')));
	$fontsize = intval(get_option('cryptex-font-size', '12')).Cryptex_get_font_size_format();
	
	// generate style	
	$style = '
		.cryptex, .cryptex .divider{
			font-family: '.$fontfamily.', sans-serif;
			font-size: '.$fontsize.';
			color: #'.$fontcolor.';	
		}
	';
	
	// store css file
	file_put_contents(CRYPTEX_PLUGIN_PATH.'/cache/dynamic.css', $style);
}
// well...is there no action hook for updating settings in wp ?
if (isset($_POST) && isset($_POST['option_page']) && $_POST['option_page']=='cryptex-settings-group'){
	Cryptex_update_cache();
}

// ADMIN PAGE
// add menu
function CryptexPlugin_backend() {
	add_options_page('Cryptex - Advanced EMail Obfuscator+Protector', 'Cryptex Obfuscator', 'administrator', __FILE__, 'Cryptex_settings_page');
	
	//call register settings function
	add_action('admin_init', 'Cryptex_register_settings'); 
}


function Cryptex_register_settings() {
	// register settings
	register_setting('cryptex-settings-group', 'cryptex-font-path');
	register_setting('cryptex-settings-group', 'cryptex-enable-hyperlink');
	register_setting('cryptex-settings-group', 'cryptex-email-divider');
	register_setting('cryptex-settings-group', 'cryptex-embed-css');
	register_setting('cryptex-settings-group', 'cryptex-font');
	register_setting('cryptex-settings-group', 'cryptex-font-size');
	register_setting('cryptex-settings-group', 'cryptex-font-color');
}

// depending on GD version, px or pt are user for font size
function Cryptex_get_font_size_format(){
	if (function_exists('gd_info')) {
        $gdinfo = gd_info();
        preg_match('/\d/', $gdinfo['GD Version'], $match);

		// Depending on your version of GD, this should be specified as the pixel size (GD1) or point size (GD2).
		if (version_compare($match[0], '2', '>=')){
			return 'pt';
		}else{
			return 'px';	
		}
    }else{
		return 'px';	
	}
}

// options page
function Cryptex_settings_page() {
	// load language files

	//load_plugin_textdomain('cryptex', null, basename(dirname(__FILE__)).'/lang');
	// font list
	$fonts = array();
	
	// get font list by path
	if (is_dir(get_option('cryptex-font-path', ''))){
		$files = scandir(get_option('cryptex-font-path', ''));	
		foreach ($files as $file){
			if (strtolower(end(explode(".", $file))) == 'ttf'){
				$fonts[] = $file;
			}	
		}
	}

	// include admin page
	include('SettingsPage.php');
}

// WordPress Plugin Hooks
add_action('admin_menu', 'CryptexPlugin_backend');

/**
	BACKUP/RESTORE font files on upgrade
*/

// backup files -> move player package files to parent folder
function cryptex_update_backup(){
	rename(CRYPTEX_PLUGIN_PATH.'/fonts', dirname(CRYPTEX_PLUGIN_PATH).'/_cryptex_font_backup');
}

// restore files -> move player package back to plugin dir
function cryptex_update_restore(){
	rename(dirname(CRYPTEX_PLUGIN_PATH).'/_cryptex_font_backup', CRYPTEX_PLUGIN_PATH.'/fonts');
	
	// update cache
	Cryptex_update_cache();
}
// update/install events
add_filter('upgrader_pre_install', 'cryptex_update_backup', 10, 0);
add_filter('upgrader_post_install', 'cryptex_update_restore', 10, 0);
?>