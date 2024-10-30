<?php if ( ! defined( 'ABSPATH' ) ) exit;
/* Save post meta on the 'save_post' hook. */
add_action('save_post', 'hdl_save_holiday_logos_meta', 1, 2);
add_action( 'admin_notices', 'hdl_save_post_error_notice' );

// displays error notice
function hdl_save_post_error_notice() {
    if (isset($_SESSION['hdl_save_error' ])) {
        $msg = 'Error on saving date: ' . $_SESSION['hdl_save_error' ];
        unset($_SESSION['hdl_save_error' ]);
        ?>
        <div class="error">
            <p><?php _e($msg); ?></p>
        </div>
        <?php
    }
}

// stores error notice for next page load
function hdl_retracement($err, $post) {
    $_SESSION['hdl_save_error'] = $err;
    if ($_POST['original_post_status'] == 'auto-draft') wp_delete_post( $post->ID, true );
    wp_redirect(admin_url('/post-new.php?post_type=holiday_logos'));
    exit;
}

// triggered on 'Publish' or 'Update'
function hdl_save_holiday_logos_meta($post_id, $post) {
    if (isset($_POST['action_hdlsave']) && $_POST['action_hdlsave'] == 'save-logo') {
        if (!current_user_can('edit_post', $post->ID))
            return $post->ID;

        if ($post->post_type == 'revision')
            return;

        // get post and calculate each start/end date
        // to compare with that we have in form
        $hdlGetPosts = get_posts(array('post_type' => 'holiday_logos', 'posts_per_page' => 200));
        $date_formats = array_unique(apply_filters('date_formats', array(__('F j, Y'), 'Y-m-d', 'm/d/Y', 'd/m/Y')));
        foreach ($date_formats as $format) {
            if (get_option('date_format') === $format) {
                $currentDate = date_i18n($format);
                $startYear = date('Y', strtotime($currentDate));
                break;
            }
        }
        foreach ($hdlGetPosts as $hdlpost) {
            if ($hdlpost->ID == $post->ID)
                continue;

            $hdlstartdate = get_post_meta($hdlpost->ID, 'hdlstartdate', true);
            if ($hdlstartdate == '')
                continue;
            $hdlenddate = get_post_meta($hdlpost->ID, 'hdlenddate', true);
            if ($hdlenddate == '') {
                $hdlenddate = $hdlstartdate;
            }

            $hdlrepeatlogo = get_post_meta($hdlpost->ID, 'hdlrepeatlogo', true);
            if ($hdlrepeatlogo == 1) {
                $curYear = date('Y', strtotime($currentDate));
                if ($curYear > $startYear) {
                    $hdlstartdate = date('m/d/Y', $hdlstartdate + (24 * 3600 * 365)) . '<br/>';
                    $hdlenddate = date('m/d/Y', $hdlenddate + (24 * 3600 * 365));
                }
            }
            /*echo 'Current date = ' . date('m/d/Y', strtotime($currentDate)) . "\n";
            echo 'Start date = ' . $hdlstartdate . "<br />";
            echo 'End date = ' . $hdlenddate . "<br />";
            echo 'post date = ' . $_POST['hdlstartdate'] . "<br />";
            wp_delete_post( $post->ID, true );
            exit;*/
             if (strtotime($_POST['hdlstartdate']) >= $hdlstartdate && strtotime($_POST['hdlstartdate']) <= $hdlenddate) {   
                $_SESSION['hdl_save_error'] = ' Date interfere with "<b>' . $hdlpost->post_title . '</b>" holiday dates';
                wp_delete_post( $post->ID, true );
                wp_redirect(admin_url('/post-new.php?post_type=holiday_logos'));
                exit;
            } else if (preg_match("~^[0-9]{2}/[0-9]{2}/[0-9]{4}$~", $_POST['hdlenddate']) && strtotime($_POST['hdlenddate']) <= strtotime($hdlenddate) && strtotime($_POST['hdlenddate']) >= strtotime($hdlstartdate)) {
                $_SESSION['hdl_save_error'] = ' Date interfere with "<b>' . $hdlpost->post_title. '</b>" holiday dates';
                wp_delete_post( $post->ID, true );
                wp_redirect(admin_url('/post-new.php?post_type=holiday_logos'));
                exit;
            }
        }

        if (!$_POST['hdlrepeatlogo']) {
            $_POST['hdlrepeatlogo'] = 0;
        }

        if (!$_POST['hdlemailme']) {
            $_POST['hdlemailme'] = 0;
        }

 	$_POST['hdlstartdate'] = hdl_get_time_stamp($_POST['hdlstartdate']);
        $_POST['hdlenddate'] = hdl_get_time_stamp($_POST['hdlenddate']);

        foreach ($_POST as $key => $value) {
            if (get_post_meta($post->ID, $key, FALSE)) {
                update_post_meta( $post->ID, $key, sanitize_text_field( $value ) );
            } else {
                add_post_meta( $post->ID, $key, sanitize_text_field( $value ) );
            }
        }
    }
}

/* Save post meta on the 'save_post' hook. */

/* Create Custom meta */

function holiday_logo_meta() {
    add_meta_box('holiday_logo_meta_box', 'Holiday Logo Details', 'display_holiday_logo_meta_box', 'holiday_logos', 'normal', 'high'
    );
}

function display_holiday_logo_meta_box() {
    global $post;
    $hdl_description = get_post_meta($post->ID, 'hdl_description', true);
    $hdlimg1 = get_post_meta($post->ID, 'hdlimg1', true);
    $hdlwidth1 = get_post_meta($post->ID, 'hdlwidth1', true);
    $hdlwidthsize1 = get_post_meta($post->ID, 'hdlwidthsize1', true);
    $hdlheight1 = get_post_meta($post->ID, 'hdlheight1', true);
    $hdlheightsize1 = get_post_meta($post->ID, 'hdlheightsize1', true);
    $hdlupload_image = get_post_meta($post->ID, 'hdlupload_image', true);
    $hdlimg2 = get_post_meta($post->ID, 'hdlimg2', true);
    $hdlwidth2 = get_post_meta($post->ID, 'hdlwidth2', true);
    $hdlwidthsize2 = get_post_meta($post->ID, 'hdlwidthsize2', true);
    $hdlheight2 = get_post_meta($post->ID, 'hdlheight2', true);
    $hdlheightsize2 = get_post_meta($post->ID, 'hdlheightsize2', true);
    $hdlupload_image2 = get_post_meta($post->ID, 'hdlupload_image2', true);
    $hdlvideourl = get_post_meta($post->ID, 'hdlvideourl', true);
    $hdlvideotype = get_post_meta($post->ID, 'hdlvideotype', true);
    $hdlwidth3 = get_post_meta($post->ID, 'hdlwidth3', true);
    //$hdlwidthsize3  =  get_post_meta($post->ID, 'hdlwidthsize3', true);
    $hdlheight3 = get_post_meta($post->ID, 'hdlheight3', true);
    // $hdlheightsize3  =  get_post_meta($post->ID, 'hdlheightsize3', true);
    $hdldatetype = get_post_meta($post->ID, 'hdldatetype', true);
    $hdlstartdate = hdl_get_formatted_date(get_post_meta($post->ID, 'hdlstartdate', true));
    $hdlenddate = hdl_get_formatted_date(get_post_meta($post->ID, 'hdlenddate', true));
    $hdlextendedbefore = get_post_meta($post->ID, 'hdlextendedbefore', true);
    $hdlextendedafter = get_post_meta($post->ID, 'hdlextendedafter', true);
    $hdlemailme = get_post_meta($post->ID, 'hdlemailme', true);
    $hdlrepeatlogo = get_post_meta($post->ID, 'hdlrepeatlogo', true);
    include_once('floating-holiday.php');
    $floating_holidays = hdl_list_floating_holidays();
    ?>
    <table width="100%" class="hdlogo">
        <input type="hidden" name="action_hdlsave" value="save-logo" />
        <tr class="hide-fla">
            <th scope="row"><?php _e('Description'); ?>
            </th>
            <td>
                <textarea name="hdl_description" placeholder="Description"><?php echo $hdl_description; ?></textarea>
            </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr class="hide-fla">
            <th scope="row"><?php _e('Logo'); ?>
            </th>
            <td>
                <input type="text" name="hdlwidth1" size = "4" placeholder="Width" value="<?php echo $hdlwidth1; ?>" />
                <select name="hdlwidthsize1">
                    <option value="px" <?php _e($hdlwidthsize1 == "px" ? "selected" : ""); ?>>px (pixels)</option>
                    <option value="em" <?php _e($hdlwidthsize1 == "em" ? "selected" : ""); ?>>em (em-quad)</option>
                </select>
                <input type="text" name="hdlheight1" size = "4" placeholder="Height" value="<?php echo $hdlheight1; ?>" />
                <select name="hdlheightsize1">
                    <option value="px" <?php _e($hdlheightsize1 == "px" ? "selected" : ""); ?>>px (pixels)</option>
                    <option value="em" <?php _e($hdlheightsize1 == "em" ? "selected" : ""); ?>>em (em-quad)</option>
                </select>
				<input id="hdlupload_image_button" class="browser button button-hero hdl-btn" type="button" value="Upload Logo" />
                <input id="hdlupload_image" class="media-group" type="text" size="36" name="hdlupload_image" value="<?php echo $hdlupload_image; ?>" />
            </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr class="hide-fla">
            <th scope="row"><?php _e('Image'); ?>
            </th>
            <td>
                <input type="text" name="hdlwidth2" size = "4" placeholder="Width" value="<?php echo $hdlwidth2; ?>" />
                <select name="hdlwidthsize2">
                    <option value="px" <?php _e($hdlwidthsize2 == "px" ? "selected" : ""); ?>>px (pixels)</option>
                    <option value="em" <?php _e($hdlwidthsize2 == "em" ? "selected" : ""); ?>>em (em-quad)</option>
                </select>
                <input type="text" name="hdlheight2" size = "4" placeholder="Height" value="<?php echo $hdlheight2; ?>" />
                <select name="hdlheightsize2">
                    <option value="px" <?php _e($hdlheightsize2 == "px" ? "selected" : ""); ?>>px (pixels)</option>
                    <option value="em" <?php _e($hdlheightsize2 == "em" ? "selected" : ""); ?>>em (em-quad)</option>
                </select>
				<input id="hdlupload_image_button2" class="browser button button-hero hdl-btn" type="button" value="Upload Image" />
                <input id="hdlupload_image2" class="media-group" type="text" size="36" name="hdlupload_image2" value="<?php echo $hdlupload_image2; ?>" />
            </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
        <tr class="hide-fla">
            <th scope="row"><?php _e('Video'); ?>
            </th>
            <td>
                <input class="media-group" type="text" name="hdlvideourl" size="40" placeholder="<?php _e('Add Video Url'); ?>" value="<?php echo $hdlvideourl; ?>" />&nbsp;
                <select name="hdlvideotype">
                    <option value="vimeo" <?php _e($hdlvideotype == "vimeo" ? "selected" : ""); ?>>Vimeo</option>
                    <option value="youtube" <?php _e($hdlvideotype == "youtube" ? "selected" : ""); ?>>Youtube</option>
                </select>
                <input type="text" name="hdlwidth3" size = "10" placeholder="Width" value="<?php echo $hdlwidth3; ?>" />
               <!-- <select name="hdlwidthsize3">
                    <option value="px" <?php _e($wid2 == "px" ? "selected" : ""); ?>>px (pixels)</option>
                </select>-->
                <input type="text" name="hdlheight3" size = "10" placeholder="Height" value="<?php echo $hdlheight3; ?>" />
               <!-- <select name="hdlheightsize3">
                    <option value="px" <?php _e($wid2 == "px" ? "selected" : ""); ?>>px (pixels)</option>
                </select>-->
            </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr class="hide-fla">
            <th scope="row"><?php _e('Date'); ?>
            </th>
            <td>
                <select name="hdldatetype" id="hdldatetype" onchange="changeRange()" style="float:left">
                    <option value="fixed" <?php _e($hdldatetype == "fixed" ? "selected" : ""); ?>>Fixed</option>
                    <option value="range" <?php _e($hdldatetype == "range" ? "selected" : ""); ?>>Range</option>
<?php 
                      foreach ($floating_holidays as $key => $value) {
                          ?>
                          <option value="<?php echo $value;?>"  <?php _e($hdldatetype == $value ? "selected" : ""); ?>><?php echo $key;?></option>
                          <?php
                      }
                    ?>
                </select>
		
                <input type="text" name="hdlstartdate" style="float:left" required="" id="hdlstartdate" size="10" placeholder="Date" value="<?php echo $hdlstartdate; ?>" />
                <input type="text" name="hdlenddate" style="float:left;display: <?php _e($hdldatetype == "range" ? "block" : "none"); ?>" id="hdlenddate" size ="10" placeholder="End Date" value="<?php echo $hdlenddate; ?>" />
            </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr class="hide-fla">
            <th scope="row"><?php _e('Time Offset'); ?>
            </th>
            <td>
                <?php
                $date_formats = array_unique(apply_filters('date_formats', array(__('F j, Y'), 'Y-m-d', 'm/d/Y', 'd/m/Y')));
                $custom = true;
                foreach ($date_formats as $format) {
                    if (get_option('date_format') === $format) {
                        echo 'Today is ' . date_i18n($format);
                    }
                }
                ?>

            </td></tr>
        
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><th>&nbsp;</th><th><input type="checkbox" name="hdlrepeatlogo" value="1" <?php _e($hdlrepeatlogo == 1 ? "checked" : ""); ?>/>Repeat every year.</th></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
    </table>
    <?php
}

/* End Create Custom meta */?>
