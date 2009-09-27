<?PHP
/*
  Plugin Name: WikiLink
  Plugin URI: http://www.coders.me/wordpress/wikilink-wordpress-plugin
  Description: An easier way to link to wikipedia.
  Version: 0.1
  Author: Eduardo Daniel Sada
  Author URI: http://www.coders.me/
  Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eduardosada%40gmail%2ecom&item_name=WikiLink%20Plugin%20Development&no_shipping=0&no_note=1&tax=0&currency_code=USD
*/

add_option('wiki_cache' , true);
add_option('wiki_images', true);
add_option('wiki_lang'  , "en");
add_option('wiki_event' , "click");
add_option('wiki_width' , 240);
add_option('wiki_css'   , "vista");
add_option('wiki_framework', 1);


// Returns the script path
function wikiPath()
{
  return plugins_url('/wikilink/');
}

// Add CSS to header
function wikiHeader()
{
  if (!is_single())
  {
    return false;
  }

  $wikiPath = wikiPath();
  echo '<link rel="stylesheet" href="'.$wikiPath.'js/sexy-tooltips/'.get_option('wiki_css').'.css" type="text/css" media="all"/>';
  echo '<link rel="stylesheet" href="'.$wikiPath.'css/style.css" type="text/css" media="all"/>';
}

// Add Scripts to footer
function wikiFooter()
{
  if (!is_single())
  {
    return false;
  }

  $wikiPath = wikiPath();
  echo "\n\n<!-- wikiLink Start -->\n\t";
  
  echo '<script type="text/javascript">
    var wiki = new Array();
    wiki["dir"]     = "'.$wikiPath.'";
    wiki["width"]   = '.get_option('wiki_width').';
    wiki["event"]   = "'.get_option('wiki_event').'";
    wiki["lang"]    = "'.get_option('wiki_lang').'";
  </script>';
  
  if (get_option('wiki_framework'))
  {
    echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools-yui-compressed.js"></script>';
  }
  echo '<script type="text/javascript" src="'.$wikiPath.'js/sexy-tooltips.js"></script>';
  echo '<script type="text/javascript" src="'.$wikiPath.'js/wikicore.js"></script>';
  echo "\n\n<!-- wikiLink End -->\n\t";
}


function wikitag_func($atts, $content=null)
{
    if (!is_single())
    {
        return $content;
    }

    $span = '<span class="wikispan">'.$content.'</span>';
    return $span;
}




function wiki_ShowOptions()
{

	if (isset($_POST['info_update'])) : ?>

        <div id="message" class="updated fade">
        <p><strong>
        <?php
            if ($_POST['info_style'])
            {
              update_option('wiki_framework', (bool) $_POST["wiki_framework"]);
              update_option('wiki_images',    (bool) $_POST["wiki_images"]);
              update_option('wiki_lang',      $_POST["wiki_lang"]);
              update_option('wiki_width',     (int) $_POST["wiki_width"]);
              update_option('wiki_event',     $_POST["wiki_event"]);
              update_option('wiki_css',       $_POST["wiki_css"]);
              update_option('wiki_cache',     (bool) $_POST["wiki_cache"]);
              _e('Settings saved.');
            }
            if ($_POST['info_cache'])
            {
                if ($_POST['wiki_deletecache'])
                {
                    wiki_deletecache();
                    echo __('Delete').' '.__('Cache');
                }
            }
        ?>
        </strong></p>
        </div>
    <?php endif; ?>

	<div class="wrap">
    <div id="icon-plugins" class="icon32">
      <br/>
    </div>
    <h2>WikiLink</h2>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
      <input type="hidden" name="info_style" value="1" />

      <h3>WikiLink Style</h3>

      <table class="form-table">
        <tr valign="top">
          <th scope="row"><label for="wiki_framework">Add MooTools ?</label></th>
          <td>
          <select name="wiki_framework"/>
          <option value="0" <?php echo get_option('wiki_framework')==0?'selected="selected"':''; ?>><?php _e('No'); ?></option>
          <option value="1" <?php echo get_option('wiki_framework')==1?'selected="selected"':''; ?>><?php _e('Yes'); ?></option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wiki_images">Show Thumbnail ?</label></th>
          <td>
          <select name="wiki_images"/>
          <option value="0" <?php echo get_option('wiki_images')==false?'selected="selected"':''; ?>><?php _e('No'); ?></option>
          <option value="1" <?php echo get_option('wiki_images')==true ?'selected="selected"':''; ?>><?php _e('Yes'); ?></option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wiki_lang">Language</label></th>
          <td>
          <select name="wiki_lang"/>
          <option value="en" <?php echo get_option('wiki_lang')=="en"?'selected="selected"':''; ?>>English</option>
          <option value="es" <?php echo get_option('wiki_lang')=="es"?'selected="selected"':''; ?>>Español</option>
          <option value="de" <?php echo get_option('wiki_lang')=="de"?'selected="selected"':''; ?>>Deutsch</option>
          <option value="fr" <?php echo get_option('wiki_lang')=="fr"?'selected="selected"':''; ?>>Français</option>
          <option value="it" <?php echo get_option('wiki_lang')=="it"?'selected="selected"':''; ?>>Italiano</option>
          <option value="pt" <?php echo get_option('wiki_lang')=="pt"?'selected="selected"':''; ?>>Português</option>
          <option value="pl" <?php echo get_option('wiki_lang')=="pl"?'selected="selected"':''; ?>>Polski</option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wiki_width">Width</label></th>
          <td>
          <input type="text" name="wiki_width" value="<?php echo get_option('wiki_width')?>"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wiki_width">Mode</label></th>
          <td>
          <select name="wiki_event"/>
          <option value="click" <?php echo get_option('wiki_event')=="click"?'selected="selected"':''; ?>>Click</option>
          <option value="mouseenter" <?php echo get_option('wiki_event')=="mouseenter"?'selected="selected"':''; ?>>MouseEnter</option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wiki_css">CSS <?php echo get_option('wiki_css') ?></label></th>
          <td>
          <select name="wiki_css"/>
          <option value="vista"   <?php echo get_option('wiki_css')=="vista"  ?'selected="selected"':''; ?>>Vista</option>
          <option value="coda"    <?php echo get_option('wiki_css')=="coda"   ?'selected="selected"':''; ?>>Coda</option>
          <option value="blue"    <?php echo get_option('wiki_css')=="blue"   ?'selected="selected"':''; ?>>Blue</option>
          <option value="rosita"  <?php echo get_option('wiki_css')=="rosita" ?'selected="selected"':''; ?>>Rosita</option>
          <option value="hulk"    <?php echo get_option('wiki_css')=="hulk"   ?'selected="selected"':''; ?>>Hulk</option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="wiki_cache">Enable Cache ?</label></th>
          <td>
          <select name="wiki_cache"/>
          <option value="0" <?php echo get_option('wiki_cache')==false?'selected="selected"':''; ?>><?php _e('No'); ?></option>
          <option value="1" <?php echo get_option('wiki_cache')==true ?'selected="selected"':''; ?>><?php _e('Yes'); ?></option>
          </select>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" name="info_update" value="<?php _e('Update options &raquo;'); ?>" />
      </p>
    </form>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
      <input type="hidden" name="info_cache" value="1" />
      <h3>Cache Control</h3>

      <table class="form-table">
        <tr valign="top">
          <th scope="row"><label for="wiki_deletecache">Delete Cache ?</label></th>
          <td>
          <select name="wiki_deletecache"/>
          <option value="0" selected="selected"><?php _e('No'); ?></option>
          <option value="1"><?php _e('Yes'); ?></option>
          </select>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" name="info_update" value="<?php _e('Update options &raquo;'); ?>" />
      </p>

    </form>
    
    <h3>Donate</h3>
    <p>Support this plugin for future updates</p>
    <p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eduardosada%40gmail%2ecom&item_name=WikiLink%20Plugin%20Development&no_shipping=0&no_note=1&tax=0&currency_code=USD">Buy me a coffee</a></p>
    <h3>Credits</h3>
    <p><a href="http://www.coders.me/wordpress/wikilink">WikiLink</a>. Author: <a href="http://www.coders.me/about">Eduardo Sada</a></p>
  </div>
	
<?php
} //end function

function wiki_menu()
{
  if (function_exists('add_options_page'))
  {
    add_options_page('WikiLink', "WikiLink", 8, __FILE__, 'wiki_ShowOptions');
  }
}

function wiki_deletecache()
{
  $dirname = '../'.wikiPath().'cache/wikipedia/';
  if (is_dir($dirname))
  {
    $dir_handle = opendir($dirname);
    while ($file = readdir($dir_handle))
    {
      if ($file != "." && $file != ".." && pathinfo($file, PATHINFO_EXTENSION) == 'cache')
      {
        if (!is_dir($dirname."/".$file))
        {
          unlink($dirname."/".$file);
        }
      }
    }
  }
  else
  {
    return false;
  }
  closedir($dir_handle);
  return true;  
}


add_shortcode('wiki', 'wikitag_func');

add_action('wp_head', 'wikiHeader' );
add_action('wp_footer', 'wikiFooter' );

add_action('admin_menu', 'wiki_menu');



// Register editor button hooks
add_filter( 'mce_external_plugins'  , 'WIKI_mce_external_plugins' );
add_filter( 'mce_buttons'           , 'WIKI_mce_buttons' );

// Load the custom TinyMCE plugin
function WIKI_mce_external_plugins( $plugins )
{
    $plugins['WIKI_customMCEPlugin'] = plugins_url('/wikilink/js/editor_plugin.js');
    return $plugins;
}


// Add the custom TinyMCE buttons
function WIKI_mce_buttons( $buttons ) {
    array_push( $buttons, 'WIKI_MCECustomButton');
    return $buttons;
}


add_action('admin_print_scripts', 'WIKI_my_custom_quicktags');

function WIKI_my_custom_quicktags()
{
    wp_enqueue_script('my_custom_quicktags', plugins_url('/wikilink/js/my-custom-quicktags.js'), array('quicktags') );
}


?>