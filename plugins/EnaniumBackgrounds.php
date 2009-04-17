<?php
/**!info**
{
  "Plugin Name"  : "Enanium backgrounds",
  "Plugin URI"   : "http://enanocms.org/plugin/enaniumbg",
  "Description"  : "Provides several additional background images for Enanium.",
  "Author"       : "Dan Fuhry",
  "Version"      : "1.1.6",
  "Author URI"   : "http://enanocms.org/"
}
**!*/

// $plugins->attachHook('enanium_search_form', 'enanium_paint_bg_controls();');
$plugins->attachHook('enanium_main_header', 'enanium_paint_bg_controls();');
$plugins->attachHook('compile_template', 'enanium_add_headers();');

$ebg_images = array('default', 'aqua', 'blinds', 'dune', 'freshflower', 'garden', 'greenmeadow', 'ladybird', 'raindrops', 'storm', 'twowings', 'wood', 'yellowflower');

$ebg_outsiders = array();
if ( $dr = @opendir(ENANO_ROOT . '/plugins/enaniumbg') )
{
  while ( $dh = @readdir($dr) )
  {
    if ( $dh == '.' || $dh == '..' || is_dir(ENANO_ROOT . "/plugins/enaniumbg/$dr") )
      continue;
    
    if ( in_array($dh, $ebg_images) || !preg_match('/\.jpg$/', $dh) )
      continue;
    
    $dh = preg_replace('/\.jpg$/', '', $dh);
    
    if ( !file_exists(ENANO_ROOT . "/plugins/enaniumbg/icons/$dh.png") )
      continue;
    
    $ebg_outsiders[] = $dh;
  }
  closedir($dr);
}
unset($dh, $dr);

function enanium_paint_bg_controls()
{
  global $ebg_images, $ebg_outsiders;
  global $lang;
  
  ?>
  <div id="enanium_bg_list">
    <?php
    foreach ( $ebg_images as $i => $image )
    {
      $sel = ( $image == 'default' ) ? ' class="selected"' : '';
      echo '<a' . $sel . ' href="#bg:' . $image . '" id="ebg_' . $image . '" onclick="enanium_change_bg(\'' . $image . '\', this); return false;" title="' . $lang->get('enaniumbg_' . $image) . '">';
      echo enanium_generate_sprite(scriptPath . '/plugins/enaniumbg/icons/sprite.png', 16, 16, 0, $i * 16);
      echo '</a>';
    }
    ?>
  </div>
  <?php
}

function enanium_add_headers()
{
  global $db, $session, $paths, $template, $plugins; // Common objects
  global $lang;
  
  if ( $template->theme != 'enanium' )
    return;
  
  $template->add_header('<link rel="stylesheet" type="text/css" href="' . scriptPath . '/plugins/enaniumbg/enaniumbg.css" />');
  $template->add_header('<script type="text/javascript" src="' . scriptPath . '/plugins/enaniumbg/enaniumbg.js"></script>');
}

function enanium_generate_sprite($sprite, $width, $height, $start_x, $start_y)
{
  $start_x = 0 - $start_x;
  $start_y = 0 - $start_y;
  return '<img alt=" " src="' . cdnPath . '/images/spacer.gif" width="' . $width . '" height="' . $height . '" style="background-image: url(\'' . $sprite . '\'); background-repeat: no-repeat; background-position: ' . $start_x . 'px ' . $start_y . 'px;" />';
}

/**!language**
<code>
{
  eng: {
    categories: ['meta', 'enaniumbg'],
    strings: {
      meta: {
        enaniumbg: 'Enanium backgrounds',
      },
      enaniumbg: {
        default: 'Default',
        aqua: 'Aqua',
        blinds: 'Blinds',
        dune: 'Dune',
        freshflower: 'Fresh flower',
        garden: 'Garden',
        greenmeadow: 'Greenmeadow',
        ladybird: 'Ladybird',
        raindrops: 'Raindrops',
        storm: 'Storm',
        twowings: 'Two Wings',
        wood: 'Wood',
        yellowflower: 'Yellow flower'
      }
    }
  }
}
</code>
**!*/

