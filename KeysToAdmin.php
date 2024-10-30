<?php
/*
Plugin Name: Keys To Admin
Plugin URI: http://itszero.org/blog/?
Description: Provides you a shortcut from blog to admin interface without putting any disturbing link in your page. It enables you to type specified text when you're in your blog (and not any input field in page), then magically go to admin interface.
Version: 1.0
Author: Zero, Chien-An Cho
Author URI: http://itszero.org

    Copyright 2009 Zero, Chien-An Cho (email : itszero at gmail dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function ZRKeysToAdmin_init() {
    if (!get_option('ZRKeysToAdmin_keyToMatch'))
        update_option('ZRKeysToAdmin_keyToMatch', 'goadmin');

    add_action('admin_menu', 'ZRKeysToAdmin_config_page');
}
add_action('init', 'ZRKeysToAdmin_init');

function ZRKeysToAdmin_config_page() {
    if ( function_exists('add_submenu_page') )
        add_submenu_page('plugins.php', __('KeysToAdmin Configuration'), __('KeysToAdmin Configuration'), 'manage_options', 'keystoadmin-config', 'keystoadmin_config');
}

function keystoadmin_config() {
	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));
			
        if (empty($_POST['key']))
            update_option('ZRKeysToAdmin_keyToMatch', 'goadmin'); // Default value
        else
            update_option('ZRKeysToAdmin_keyToMatch', $_POST['key']);
    }
    ?>
    <?php if ( !empty($_POST ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('KeysToAdmin Configuration'); ?></h2>
<div class="narrow">
<p><?php echo __('This option enables you to customized what you type in blog to go to admin page, the default value is "goadmin".');?></p>
<form action="" method="post" id="keystoadmin-conf" style="margin: auto; width: 400px; ">
<h3><label for="key"><?php _e('Keys to go to admin'); ?></label></h3>
<p><input id="key" name="key" type="text" size="15" maxlength="12" value="<?php echo get_option('ZRKeysToAdmin_keyToMatch'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>

<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>
</form>
</div>
</div>
    <?php
}

function ZRKeysToAdmin_keypress_hook($content) {
?>
<script type="text/javascript" charset="utf-8">
var keybuffer = "";
document.onkeypress = function(e) {
        var target;
        if (window.event)
            target = window.event.srcElement;
        else
            target = e.target;
            
        if (target.tagName.toUpperCase() == "INPUT") return;
        
        var keyToMatch = "<?php echo get_option('ZRKeysToAdmin_keyToMatch');?>";
        if (window.event) // IE
            keybuffer += String.fromCharCode(window.event.keyCode);
        else
            keybuffer += String.fromCharCode(e.charCode);

        if (keyToMatch.indexOf(keybuffer) != 0)
                keybuffer = "";
        
        if (keyToMatch == keybuffer)
        {       
                location.href = "<?php echo bloginfo('url');?>/wp-admin";
                keybuffer = "";
        }       
       
        return true;
}
</script>
<?php
}

add_action('wp_head', 'ZRKeysToAdmin_keypress_hook');
?>