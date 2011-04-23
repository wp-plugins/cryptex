<?php
/**
	Descriptionn: CRYPTEX Admin Settings Page
	Plugin URI: http://www.a3non.org/go/cryptex
	Version: 1.3
	Author: Andi Dittrich
	Author URI: http://www.a3non.org
	License: MIT X11-License
	
	Copyright (c) 2010-2011, Andi Dittrich

	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
?>
<?php if (!defined('CRYPTEX_INIT')) die('DIRECT ACCESS PROHIBITED'); ?>

<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2>Cryptex - Advanced EMail Obfuscator+Protector</h2>

<!-- usage !-->
<p>Just insert a cryptex shortcode within the email-address into your posts/pages. Example: <strong>[cryptex]emailaddress@example.com[/cryptex]</strong></p>

<!-- info !-->
<h2>System Info</h2>
<p>
<?php
	if (function_exists('gd_info')){
		$info = gd_info();
		echo 'GD lib installed: <strong style="color:#090">true</strong><br />';	
		echo 'GD version: <strong style="color:#090">',  $info["GD Version"],'</strong><br />';	
		
		// png support
		if ($info['PNG Support']){
			echo 'PNG support: <strong style="color:#090">true</strong><br />';
		}else{
			echo 'PNG support: <strong style="color:#900">false</strong><br />';
		}
		
		// font path avaible ?
		if (is_dir(get_option('cryptex-font-path', ''))){
			echo 'System font path: <strong style="color:#090">valid</strong><br />';
		}else{
			echo 'System font path: <strong style="color:#900">invalid</strong><br />';
		}
		
		// fonts avaible ?
		if (count($fonts)>0){
			echo 'Fonts avaible: <strong style="color:#090">true</strong><br />';
		}else{
			echo 'Fonts avaible: <strong style="color:#900">false - fallback active</strong><br />';
		}

		// PHP version
		if (version_compare(phpversion(), '5.0', '>=')){
			echo 'PHP version: <strong style="color:#090">', phpversion() ,'</strong><br />';
		}else{
			echo 'PHP version: <strong style="color:#900">', phpversion() ,'</strong><br />';
		}

		// server os
		echo 'Server OS: <strong>', PHP_OS,'</strong><br />';
				
		// cryptex version
		echo 'Cryptex Plugin Version: <strong>', CRYPTEX_VERSION, '</strong><br />';
	}else{
		echo 'GD Lib installed: <strong style="color:#900">false</strong>';
	}
?>
</p>

<!-- settings !-->
<form method="post" action="options.php">
    <?php settings_fields( 'cryptex-settings-group' ); ?>
    
    <h2>Settings</h2>
    <h4>System font path</h4>
    <p>You have to choose a directory with TrueTypeFonts (.ttf) as source for image creation. The default values for linux systems are <strong>/usr/share/fonts/</strong>, <strong>/usr/share/fonts/truetype/</strong> - for windows systems <strong>C:\Windows\Fonts\</strong>. If there are no TrueTypeFonts avaible, the plugin will use the embedded fonts of GD libraray as fallback. If you don't know the path, please ask your hosting provider or upload the fonts manually into the cryptex-plugin-directory <strong><?php echo CRYPTEX_PLUGIN_PATH.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR ?></strong> and use this as path.   
    </p>
    <p><strong>Note: </strong>During security restrictions this pathes, depending on your hosting environment, could be unaccessable. <br />
    	In this case you have to upload the font files from your system into the cryptex-plugin-directory <strong><?php echo CRYPTEX_PLUGIN_PATH.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR ?></strong>
    </p>

	<!-- font path !-->
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Font path</th>
        <td>
        <input name="cryptex-font-path" type="text" value="<?php echo get_option('cryptex-font-path', '')?>" class="regular-text" />
		<label for="cryptex-font-path">with trailing slash!</label>
        </td>
        </tr>
    </table>

	<!-- hyperlinks !-->    
    <h4>Hyperlinks</h4>
    <p>Should EMail-Addresses linkable ? (mailto:youremail@example.com)
    <br /><strong>Note:</strong> Cryptex provides an integrated key-shifting based encryption/decryption of hyperlinks to ensure maximum protection.</p>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enabled</th>
        <td>
		<?php
			if(get_option('cryptex-enable-hyperlink', true)){ 
				$checked = ' checked="checked" '; 
			}
			echo "<input ".$checked." name='cryptex-enable-hyperlink' type='checkbox' />";
		?>        
        </td>
        </tr>
    </table>
    
    <!-- @sign replacement !-->
    <h4>@ Sign</h4>
    <p>Instead of using the @ sign for emails, you can choose between some options - this could increase the protection level against spiders/bots using OCR.				
    <br /><strong>Note:</strong> Use ALWAYS the @ sign in the cryptex shortcode!</p>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Replacements</th>
        <td>
        <select name="cryptex-email-divider">
		<?php
			$replacements = array('@', '(at)', '[at]', '{at}', '/AT/');
			
			foreach ($replacements as $rpl){
				$selected = ($rpl == get_option('cryptex-email-divider', '@')) ? 'selected="selected"' : '';
				echo '<option value="'.$rpl.'" '.$selected.'>'.$rpl.'</option>';
			}
			?> 
        </select>       
        </td>
        </tr>
    </table>

    <!-- appearance !-->
    <h3>Appearance</h3>
    <p>Change these settings to your current theme styles</p>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Font Family</th>
        <td>
        <select name="cryptex-font">
		<?php			
			foreach ($fonts as $font){
				$selected = ($font == get_option('cryptex-font', '')) ? 'selected="selected"' : '';
				echo '<option value="'.$font.'" '.$selected.'>'.$font.'</option>';
			}
			?> 
        </select>       
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Font Color</th>
        <td>
        <input name="cryptex-font-color" type="text" value="<?php echo get_option('cryptex-font-color', '0x000000')?>" class="text" />
        <label for="cryptex-font-color">hex e.g. 0xff0102</label>
        </td>
        </tr>
        
         <tr valign="top">
        <th scope="row">Font Size</th>
        <td>
        <input name="cryptex-font-size" type="text" value="<?php echo get_option('cryptex-font-size', '12')?>" class="text" />
        <label for="cryptex-font-size">in <?php echo Cryptex_get_font_size_format(); ?></label>
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Embed CSS</th>
        <td>
		<?php
			if(get_option('cryptex-embed-css', true)){ 
				$checked = ' checked="checked" '; 
			}
			echo "<input ".$checked." name='cryptex-embed-css' type='checkbox' />";
		?>        
        </td>
        </tr>
        
        <label for="cryptex-font-path"></label>
    </table>

    
	<!-- submit !-->
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>

<h2>Credits</h2>
<p><a href="http://www.a3non.org/go/cryptex">Cryptex</a> is developed by <a href="http://www.a3non.org">Andi Dittrich</a>. It's release under the MIT X11 License.</p>
</div>
