<?php
if (!defined('CRYPTEX_INIT')) die('DIRECT ACCESS PROHIBITED');

// check
if (function_exists('gd_info')){
		echo '<p>';
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
		
		echo '</p>';
	}else{
		echo '<div class="updated error"><p>GD library not found on your system - you cannot use cryptex until you or your hosting provider install the GD library with enabled PNG support</p></div>';
	}
?>