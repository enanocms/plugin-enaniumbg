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
$plugins->attachHook('acl_rule_init', '$this->addAdminNode("adm_cat_appearance", "enaniumbg_acppage", "EnaniumConfig", scriptPath . "/plugins/enaniumbg/icons/garden.png");');

$ebg_images = array('default', 'aqua', 'blinds', 'dune', 'freshflower', 'garden', 'greenmeadow', 'ladybird', 'raindrops', 'storm', 'twowings', 'wood', 'yellowflower');

$ebg_outsiders = array();
if ( $dr = @opendir(ENANO_ROOT . '/plugins/enaniumbg') )
{
  while ( $dh = @readdir($dr) )
  {
    if ( $dh == '.' || $dh == '..' || is_dir(ENANO_ROOT . "/plugins/enaniumbg/$dh") )
      continue;
    
    if ( !preg_match('/\.jpg$/', $dh) )
      continue;
    
    $dh = preg_replace('/\.jpg$/', '', $dh);
    
    if ( in_array($dh, $ebg_images) )
      continue;
    
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
  
  if ( !getConfig('enanium_show_switcher', 1) )
    return;
  
  ?>
  <div id="enanium_bg_list">
    <?php
    foreach ( $ebg_images as $i => $image )
    {
      $sel = ( $image == getConfig('enanium_bg', 'default') ) ? ' class="selected"' : '';
      echo '<a' . $sel . ' href="#bg:' . $image . '" id="ebg_' . $image . '" onclick="enanium_change_bg(\'' . $image . '\', this); return false;" title="' . $lang->get('enaniumbg_' . $image) . '">';
      echo enanium_generate_sprite(scriptPath . '/plugins/enaniumbg/icons/sprite.png', 16, 16, 0, $i * 16);
      echo '</a>';
    }
    foreach ( $ebg_outsiders as $image )
    {
      $sel = ( $image == getConfig('enanium_bg', 'default') ) ? ' class="selected"' : '';
      echo '<a' . $sel . ' href="#bg:' . $image . '" id="ebg_' . $image . '" onclick="enanium_change_bg(\'' . $image . '\', this); return false;" title="' . htmlspecialchars(ucwords(str_replace('_', ' ', $image))) . '">';
      echo '<img alt=" " src="' . scriptPath . '/plugins/enaniumbg/icons/' . $image . '.png" />';
      echo '</a>';
    }
    ?>
  </div>
  <?php
}

function enanium_add_headers()
{
  global $db, $session, $paths, $template, $plugins; // Common objects
  global $ebg_images, $ebg_outsiders;
  global $lang;
  
  if ( $template->theme != 'enanium' )
    return;
  
  $repeat = getConfig('enanium_background_repeat', 'no-repeat');
  $attachment = getConfig('enanium_background_attachment', 'fixed');
  $position = getConfig('enanium_background_position', 'center top');
  
  if ( getConfig('enanium_show_switcher', 1) )
  {
    $template->add_header('<link rel="stylesheet" type="text/css" href="' . scriptPath . '/plugins/enaniumbg/enaniumbg.css" />');
    
    $addheader = <<<EOF
    <script type="text/javascript">
      var enanium_bg_repeat = '$repeat';
      var enanium_bg_attachment = '$attachment';
      var enanium_bg_position = '$position';
    </script>
EOF;
    $template->add_header($addheader);
    $template->add_header('<script type="text/javascript" src="' . scriptPath . '/plugins/enaniumbg/enaniumbg.js"></script>');
  }
  
  if ( ($img = getConfig('enanium_bg', 'default')) !== 'default' )
  {
    if ( !file_exists(ENANO_ROOT . "/plugins/enaniumbg/$img.jpg") )
      return;
    
    $scriptpath = scriptPath;
    $addheader = <<<EOF
    <style type="text/css">
      body {
        background-image: url({$scriptpath}/plugins/enaniumbg/{$img}.jpg);
        background-color: #000000;
        background-repeat: $repeat;
        background-attachment: $attachment;
        background-position: $position;
      }
      td#cell-content {
        background-color: transparent;
        background-image: url({$scriptpath}/plugins/enaniumbg/transw70.png);
      }
    </style>
EOF;
    $template->add_header($addheader);
  }
}

function enanium_generate_sprite($sprite, $width, $height, $start_x, $start_y)
{
  $start_x = 0 - $start_x;
  $start_y = 0 - $start_y;
  return '<img alt=" " src="' . cdnPath . '/images/spacer.gif" width="' . $width . '" height="' . $height . '" style="background-image: url(\'' . $sprite . '\'); background-repeat: no-repeat; background-position: ' . $start_x . 'px ' . $start_y . 'px;" />';
}

function page_Admin_EnaniumConfig()
{
  global $db, $session, $paths, $template, $plugins; // Common objects
  global $lang;
  
  if ( isset($_POST['enanium_bg']) )
  {
    $bg = $_POST['enanium_bg'];
    if ( file_exists(ENANO_ROOT . "/plugins/enaniumbg/$bg.jpg") )
      setConfig('enanium_bg', $bg);
    
    $val = isset($_POST['show_switcher']) ? '1' : '0';
    setConfig('enanium_show_switcher', $val);
    
    setConfig('enanium_background_repeat', $_POST['background_repeat']);
    setConfig('enanium_background_attachment', $_POST['background_attachment']);
    setConfig('enanium_background_position', $_POST['background_position']);
    
    echo '<div class="info-box">' . $lang->get('enaniumbg_acp_msg_changes_saved') . '</div>';
  }
  
  acp_start_form();
  ?>
  <div class="tblholder">
    <table border="0" cellspacing="1" cellpadding="4">
      <tr>
        <th colspan="2"><?php echo $lang->get('enaniumbg_acp_th'); ?></th>
      </tr>
      <tr>
        <td class="row2" style="width: 50%;">
          <?php echo $lang->get('enaniumbg_acp_field_default_bg'); ?><br />
          <small><?php echo $lang->get('enaniumbg_acp_field_default_bg_hint'); ?></small>
        </td>
        <td class="row1" style="width: 50%;">
          <select name="enanium_bg">
            <?php
            global $ebg_images, $ebg_outsiders;
            if ( !empty($ebg_outsiders) )
            {
              foreach ( $ebg_outsiders as $image )
              {
                $sel = $image == getConfig('enanium_bg', 'default') ? 'selected="selected" ' : '';
                echo '<option ' . $sel . 'value="' . $image . '">' . ucwords(str_replace('_', ' ', $image)) . '</option>';
              }
              echo '<option disabled="disabled" value="">--------------------</option>';
            }
            foreach ( $ebg_images as $image )
            {
              $sel = $image == getConfig('enanium_bg', 'default') ? 'selected="selected" ' : '';
              echo '<option ' . $sel . 'value="' . $image . '">' . $lang->get("enaniumbg_$image") . '</option>';
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="row2">
          <label for="ebg_chk_show_switcher"><?php echo $lang->get('enaniumbg_acp_field_show_switcher'); ?></label><br />
          <small><?php echo $lang->get('enaniumbg_acp_field_show_switcher_hint'); ?></small>
        </td>
        <td class="row1">
          <input id="ebg_chk_show_switcher" type="checkbox" name="show_switcher" <?php echo getConfig('enanium_show_switcher', 1) == '1' ? 'checked="checked" ' : ''; ?>/>
        </td>
      </tr>
      <tr>
        <td class="row2">
          <?php echo $lang->get('enaniumbg_acp_field_tile'); ?><br />
          <small><?php echo $lang->get('enaniumbg_acp_field_tile_hint'); ?></small>
        </td>
        <td class="row1">
          <label>
            <input type="radio" name="background_repeat" value="repeat" <?php echo getConfig('enanium_background_repeat', 'no-repeat') == 'repeat' ? 'checked="checked" ' : ''; ?>/>
            <?php echo $lang->get('enaniumbg_acp_field_tile_tile'); ?>
          </label><br />
          <label>
            <input type="radio" name="background_repeat" value="no-repeat" <?php echo getConfig('enanium_background_repeat', 'no-repeat') == 'no-repeat' ? 'checked="checked" ' : ''; ?>/>
            <?php echo $lang->get('enaniumbg_acp_field_tile_norepeat'); ?>
          </label>
        </td>
      </tr>
      <tr>
        <td class="row2">
          <?php echo $lang->get('enaniumbg_acp_field_scroll'); ?><br />
          <small><?php echo $lang->get('enaniumbg_acp_field_scroll_hint'); ?></small>
        </td>
        <td class="row1">
          <label>
            <input type="radio" name="background_attachment" value="fixed" <?php echo getConfig('enanium_background_attachment', 'fixed') == 'fixed' ? 'checked="checked" ' : ''; ?>/>
            <?php echo $lang->get('enaniumbg_acp_field_scroll_fixed'); ?>
          </label><br />
          <label>
            <input type="radio" name="background_attachment" value="scroll" <?php echo getConfig('enanium_background_attachment', 'fixed') == 'scroll' ? 'checked="checked" ' : ''; ?>/>
            <?php echo $lang->get('enaniumbg_acp_field_scroll_scroll'); ?>
          </label>
        </td>
      </tr>
      <tr>
        <td class="row2">
          <?php echo $lang->get('enaniumbg_acp_field_anchor'); ?>
        </td>
        <td class="row1">
        <table border="0" style="background-color: transparent; width: 120px; padding: 5px; border: 1px solid #404040;">
            <?php
            foreach ( array('top', 'center', 'bottom') as $ypos )
            {
              echo '<tr>';
              foreach ( array('left', 'center', 'right') as $xpos )
              {
                ?><td style="width: 40px; line-height: 30px; text-align: center;"><input type="radio" name="background_position" value="<?php echo "$xpos $ypos"; ?>" <?php echo getConfig('enanium_background_position', 'center top') == "$xpos $ypos" ? 'checked="checked" ' : ''; ?>/><?php
              }
              echo '</tr>';
            }
            ?>
          </table>
        </td>
      </tr>
      <tr>
        <th class="subhead" colspan="2">
          <input type="submit" value="<?php echo $lang->get('etc_save_changes'); ?>" />
        </th>
      </tr>
    </table>
  </div>
  </form>
  <?php
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
        yellowflower: 'Yellow flower',
        
        acp_th: 'Enanium background configuration',
        acp_field_default_bg: 'Default background:',
        acp_field_default_bg_hint: '<b>Tip:</b> You can add more backgrounds to Enanium! Upload a JPEG image to plugins/enaniumbg/. Then, upload a 16x16 icon of the image, with the same filename, to plugins/enaniumbg/icons/. The plugin will automatically add the image to the list here and to the switcher, if enabled.',
        acp_field_show_switcher: 'Show background switcher:',
        acp_field_show_switcher_hint: 'If the switcher is turned off, the background selected above will always be used.',
        
        acp_field_tile: 'Allow background to be tiled:',
        acp_field_tile_hint: 'If this is off, the background will be letterboxed and/or pillarboxed if the screen is too small.',
        acp_field_tile_tile: 'Tile',
        acp_field_tile_norepeat: 'Don\'t tile',
        
        acp_field_scroll: 'Scroll background image with page:',
        acp_field_scroll_hint: 'This may produce undesired results if "%this.enaniumbg_acp_field_tile_norepeat%" is selected.',
        acp_field_scroll_fixed: 'Background fixed and always in view',
        acp_field_scroll_scroll: 'Scroll background',
        
        acp_field_anchor: 'Background position anchor:',
        
        acp_msg_changes_saved: 'Your changes have been saved.',
        acppage: 'Enanium configuration',
      }
    }
  }
}
</code>
**!*/

