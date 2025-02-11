<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
  Plugin Name: Holiday Logos
  Plugin URI: http://holidaylogos.com
  Description: Automatically changes your logo, image, background, or video based on the date.
  Version: 1.1.6
  Author: CONCEPTiNET
  Author URI: https://conceptinet.com
 */

$HDL_path = trailingslashit(dirname(__FILE__));
register_activation_hook(__FILE__, 'hdl_insert_predefine_event');

// admin edit screen columns customization
function hdl_custom_columns($columns) {
    unset($columns['date']);
    $new_columns = array(
        'hdlstartdate' => __('Start Date', 'conceptinet'),
        'date' => __('Date', 'conceptinet'),
        'author' => __('Author', 'conceptinet')
    );
    return array_merge($columns, $new_columns);
}
add_filter('manage_holiday_logos_posts_columns' , 'hdl_custom_columns');

function hdl_custom_page_column_content( $column_name, $post_id ) {
    if ( $column_name == 'hdlstartdate' ) {
        echo hdl_get_formatted_date(get_post_meta($post_id, 'hdlstartdate', true));
    }
}
add_action( 'manage_posts_custom_column', 'hdl_custom_page_column_content', 10, 2 );

function hdl_startdate_column_register_sortable( $columns ) {
    $columns['hdlstartdate'] = 'hdlstartdate';
    return $columns;
}
add_filter( 'manage_edit-holiday_logos_sortable_columns', 'hdl_startdate_column_register_sortable' );

function hdl_startdate_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'hdlstartdate' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'hdlstartdate',
            'orderby' => 'meta_value_num'
        ) );
    }
    return $vars;
}
add_filter( 'request', 'hdl_startdate_column_orderby' );

function hdl_insert_predefine_event() {
	$existing = get_posts(array('post_type' => 'holiday_logos', 'posts_per_page' => 40));
	// only install sample data if no holiday_logo post types exist
	if( count( $existing ) == 0 ) {
		require_once 'elements/predefine-holiday.php';
		foreach ($hdleventdate as $key => $value) {
			$post = array(
				'post_status' => 'publish',
				'post_title' => $key,
				'post_type' => 'holiday_logos',
			);
			$post_ID = wp_insert_post($post);
			if (strpos($value, '-') !== false) {
				$value = explode('-', $value);
				$value[0] = hdl_get_time_stamp($value[0]);
				add_post_meta($post_ID, 'hdlstartdate', $value[0]);
 				if($value[1]!=='') $value[1] = hdl_get_time_stamp($value[1]);
				add_post_meta($post_ID, 'hdlenddate', $value[1]);
				if ($value[2] != '') {
					add_post_meta($post_ID, 'hdlday', $value[2]);
				}
			} else {
 				$value = hdl_get_time_stamp($value);
				add_post_meta($post_ID, 'hdlstartdate', $value);
			}
		}
		//wp_schedule_event(time(), 'daily', 'hdl_send_email_every_day');
	}
}


function hdl_hook_noindex() {
    if (get_post_type() == 'holiday_logos') {
        print '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
    }
}
add_action('wp_head', 'hdl_hook_noindex');

add_action('init', 'create_holiday_post_type');   //Create post type
add_action('admin_print_scripts', 'hlogo_my_admin_scripts');   // Upload image
add_action('admin_print_styles', 'hdl_admin_styles');   // Upload image
add_action('admin_init', 'load_hdl_style');            //Global css
add_action('in_admin_footer', 'hdl_datepicketscript'); // Datepicker
add_action('admin_init', 'holiday_logo_meta');  //Custom meta
require_once 'elements/holiday-custom.php';

/* Sub menu under holiday logo */
add_action('admin_menu', 'hdl_enable_pages');

function hdl_enable_pages() {
    add_submenu_page('edit.php?post_type=holiday_logos', 'Holiday Logos Settings', 'Settings', 'edit_posts', basename(__FILE__), 'hdl_settings');
    add_submenu_page('edit.php?post_type=holiday_logos', 'Holiday Logos Report', 'Report', 'read', 'report.php', 'hdl_report');
}

function hdl_settings() {
    if ($_POST['submit']) {
        if ( ! isset( $_POST['holiday-logos_settings'] )
            || ! wp_verify_nonce( $_POST['holiday-logos_settings'], 'update' . date('Y-m-d') )
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {

            foreach ($_POST as $key => $value) {
                if (get_option($key) !== false) {
                    update_option( $key, sanitize_text_field( $value ) );
                } else {
                    $deprecated = null;
                    $autoload = 'no';
                    add_option( $key, sanitize_text_field( $value ), $deprecated, $autoload);
                }
            }
            $msg = "Form Submitted Successfully";
        }
    }
    ?>
    <div class="wrap">
        <div class="postbox-container wp-core-ui">
            <div class="metabox-holder inside">
                <div class="meta-box-sortables">
                    <form action="<?php echo admin_url(); ?>/edit.php?post_type=holiday_logos&page=holiday-logos.php" method="POST" enctype='multipart/form-data'>
                        <?php wp_nonce_field( 'update' . date('Y-m-d'), 'holiday-logos_settings' ); ?>
						<table class="form-table hdl_table">
                            <tbody>
                                <?php if ($msg != '') { ?>
                                    <tr valign="top" class="form-field  wpform_module_title_tr class hide-fla">
                                        <td colspan="2">
                                            <span style='color:#008000'><strong><?php echo $msg; ?></strong></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr valign="top" class="form-field  wpform_module_title_tr class hide-fla">
                                    <td colspan="2">
                                        <h1>Settings<span class="dashicons-before dashicons-businessman support-btn"><a href="http://holidaylogos.com/?utm_source=plugin&utm_medium=holiday-logos&utm_content=support-button&utm_campaign=holiday-logos-support" target="_blank">Support</a></span></h1>
                                    </td>
                                </tr>
                                <tr valign="top" class="form-field  wpform_module_title_tr class hide-fla">
                                    <th> &nbsp;<?php _e('Upload Logo'); ?></th>
                                    <td>
                                        <input id="hdlupload_default_image" style="width:300px;" type="text" name="hdlupload_default_image" value="<?php echo get_option('hdlupload_default_image'); ?>" />
                                        <input id="hdlupload_default_image_button" class="browser button button-hero hdl-btn" type="button" value="Upload Logo" />
                                    </td>
                                </tr>
                                <tr class="hide-fla">
                                    <th scope="row">&nbsp;<?php _e('Upload Default Image'); ?>
                                    </th>
                                    <td>
                                        <input id="hdlupload_default_image2" type="text" size="36" name="hdlupload_default_image2" value="<?php echo get_option('hdlupload_default_image2'); ?>" />
                                        <input id="hdlupload_default_image_button2" class="browser button button-hero hdl-btn" type="button" value="Upload Image" />
                                    </td></tr>
                                <tr class="hide-fla">
                                    <th scope="row">&nbsp;<?php _e('Video'); ?>
                                    </th>
                                    <td>
                                        <input type="text" name="hdldefaultvideourl" size="40" placeholder="<?php _e('Add Video Url'); ?>" value="<?php echo get_option('hdldefaultvideourl'); ?>" />&nbsp;
                                        <select name="hdldefaultvideotype">
                                            <option value="vimeo" <?php _e(get_option('hdldefaultvideotype') == "vimeo" ? "selected" : ""); ?>>Vimeo</option>
                                            <option value="youtube" <?php _e(get_option('hdldefaultvideotype') == "youtube" ? "selected" : ""); ?>>Youtube</option>
                                        </select>
                                        <input type="text" name="hdldefaultwidth3" size = "10" placeholder="Width" value="<?php echo get_option('hdldefaultwidth3'); ?>" />
                                        <input type="text" name="hdldefaultheight3" size = "10" placeholder="Height" value="<?php echo get_option('hdldefaultheight3'); ?>" />
                                    </td></tr>
                                <tr class="hide-fla">
                                    <th scope="row">&nbsp;
                                    </th>
                                    <td>
                                        <input type="submit" name="submit" class="button-hero hdl-btn" value="submit"/>
                                    </td></tr>
                        </table>
                        </form>
                    <table class="form-table hdl_table">
                        <tr valign="top" class="form-field  wpform_module_title_tr class hide-fla">
                            <td colspan="2">
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2">
                                1. Place <strong>[holidayevent types="Logo"]</strong>  in your widget where you want the show logo.
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2">
                                2. If you want to add Logo shortcode  directly into file then please use it in php tag.
                                <strong>do_shortcode('[holidayevent types="Logo"]')</strong>.
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2">
                                3. Place <strong>'[holidayevent types="Image"]'</strong>  in your widget where you want the show Image.
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2">
                                4. If you want to add Image shortcode  directly into file then please uuse it in php tag. 
                                <strong>do_shortcode('[holidayevent types="Image"]')</strong>.
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2">
                                5. Place <strong>[holidayevent types="Video"]</strong>  in your widget where you want the show Video.
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2">
                                6. If you want to add Video shortcode  directly into file then please use it in php tag. 
                                <strong>do_shortcode('[holidayevent types="Video"]')</strong>.
                            </td>
                        </tr>
                        <tr valign="top" class="form-field  wpform_module_title_tr">
                            <td colspan="2"> <strong>Please help us recover some the cost for development and maintenance. Any amount is appreciated. Cheers! </strong><br/>
                                <form action="https://www.paypal.com/cgi-bin/webscr" target="_blank" method="post">
                                    <input type="hidden" name="business" value="paypal@conceptinet.com">
                                    <input type="hidden" name="cmd" value="_donations">
                                    <input type="hidden" name="item_name" value="Holiday Logos">
                                    <input type="hidden" name="currency_code" value="USD">
                                    <input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"
                                           alt="PayPal - The safer, easier way to pay online">
                                    <img alt="" border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >

                                </form>

                            </td>
                        </tr>
                        </tbody></table>

                </div>
            </div>
        </div>
    </div>
    <?php
}

function hdl_report() {
    require_once('report.php');
}

function hdl_setup() {
    
}

/* End Sub menu holiday logo */


function hlogo_my_admin_scripts() {
	wp_enqueue_media();
	wp_enqueue_script( 'hdl-validate', plugins_url( 'js/jquery.validate.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'hdl-validate-extra', plugins_url( 'js/additional-methods.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'hdl-jquery-ui', plugins_url( 'js/jquery-ui.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'hdl-admin', plugins_url( 'js/hdl-admin.js', __FILE__  ) );
}

function hdl_admin_styles() {
    wp_enqueue_style('thickbox');
}

function load_hdl_style() {
    wp_enqueue_style('hdlstyle', plugins_url('css/hdlstyle.css', __FILE__));
    wp_enqueue_style('hdldatepicker', plugins_url('css/datepicker.css', __FILE__));
    wp_enqueue_style('hdltheme', plugins_url('css/theme.css', __FILE__));
}

/* Datepicker */

function hdl_datepicketscript() {
    ?>
    <script type='text/javascript'>
        
		// use modern media uploader  - Wordpress 3.5+
        jQuery(document).ready(function ($) {
		 
			$('#hdlupload_default_image_button').click(function(e) {
				e.preventDefault();
				//If the uploader object has already been created, reopen the dialog
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
		 
				//Extend the wp.media object
				var custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image'
					},
					multiple: false
				});
		 
				//When a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$('#hdlupload_default_image').val(attachment.url);
				});
		 
				//Open the uploader dialog
				custom_uploader.open();
			});
			
			$('#hdlupload_default_image_button2').click(function(e) {
				e.preventDefault();
				//If the uploader object has already been created, reopen the dialog
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
		 
				//Extend the wp.media object
				var custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image'
					},
					multiple: false
				});
		 
				//When a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$('#hdlupload_default_image2').val(attachment.url);
				});
		 
				//Open the uploader dialog
				custom_uploader.open();
			});

			$('#hdlupload_image_button').click(function(e) {
				e.preventDefault();
				//If the uploader object has already been created, reopen the dialog
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
		 
				//Extend the wp.media object
				var custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image'
					},
					multiple: false
				});
		 
				//When a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$('#hdlupload_image').val(attachment.url);
				});
		 
				//Open the uploader dialog
				custom_uploader.open();
			});

			$('#hdlupload_image_button2').click(function(e) {
				e.preventDefault();
				//If the uploader object has already been created, reopen the dialog
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
		 
				//Extend the wp.media object
				var custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image'
					},
					multiple: false
				});
		 
				//When a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$('#hdlupload_image2').val(attachment.url);
				});
		 
				//Open the uploader dialog
				custom_uploader.open();
			});


		});	

        var dateFormat = "mm/dd/yy",
                from = jQuery("#hdlstartdate")
                .datepicker({
                    defaultDate: "+1w",
                    //changeMonth: true,
                })
                .on("change", function () {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = jQuery("#hdlenddate")
                .datepicker({
                    defaultDate: "+1w",
                    //changeMonth: true,
                })
                .on("change", function () {
                    from.datepicker("option", "maxDate", getDate(this));
                });

        function getDate(element) {
            var date;
            try {
                date = jQuery.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }

            return date;
        }

        function changeRange()
        {
            if (jQuery("#hdldatetype").val() == 'range') {
                jQuery("#hdlenddate").show();
                jQuery("#hdlstartdate").attr("placeholder", "Start Date");
            }
            else if( jQuery("#hdldatetype").val() == 'fixed' ){
                jQuery("#hdlenddate").hide();
                jQuery("#hdlstartdate").attr("placeholder", "Date");
            }else{
                var optvalue = jQuery("#hdldatetype").val();
                jQuery("#hdlenddate").hide();
                jQuery("#hdlstartdate").attr("value", optvalue);
            }
        }
    </script>
    <?php
}

/* Datepicker End */


/* Create Post type */

function create_holiday_post_type() {
    if( !session_id() ) session_start();

    register_post_type('holiday_logos', array(
        'labels' => array(
            'singular_name' => 'Add Logo',
            'plural_name' => 'Add Logo',
            'all_items' => 'View Logo',
            'add_new' => 'Add Logo',
            'menu_name' => 'Holiday Logos',
            'add_new_item' => 'Add New Logo',
            'name' => 'View Logo',
            'edit_item' => 'Edit Logo',
            'new_item' => 'New Logo',
            'view' => 'View',
            'view_item' => 'View Logo',
            'search_items' => 'Search Logo',
            'not_found' => 'No Logo found',
            'not_found_in_trash' => 'No Logo found in Trash',
            'parent' => 'Parent Logo'
        ),
        'public' => true,
        'menu_position' => 15,
        'supports' => array('title'),
        'taxonomies' => array('support'),
        'has_archive' => true
            )
    );
}

/* End Create Post type */

/* Create Short Code */

function holiday_logo($atts) {
    extract(shortcode_atts(array(
        'types' => 'Logo',
	'attr' => array(),
                    ), $atts));

	if( count($attr)> 0 )
        $attr = explode( ',', $attr );

    $hdlGetPosts = get_posts(array('post_type' => 'holiday_logos', 'posts_per_page' => 200));
    $date_formats = array_unique(apply_filters('date_formats', array(__('F j, Y'), 'Y-m-d', 'm/d/Y', 'd/m/Y')));
    //$custom = false;
    $default = true;
    foreach ($date_formats as $format) {
        if (get_option('date_format') === $format) {
            $currentDate = date_i18n($format);
            $curYear = date('Y', strtotime($currentDate));
            break;
        }
    }

    foreach ($hdlGetPosts as $post) {
        $hdlstartdate = get_post_meta($post->ID, 'hdlstartdate', true);
	    $hdlstartdate = hdl_get_formatted_date($hdlstartdate);	
        $startYear = date('Y', strtotime($hdlstartdate));
        $hdlenddate = get_post_meta($post->ID, 'hdlenddate', true);
 	    $hdlenddate = hdl_get_formatted_date($hdlenddate);
        
	if ($hdlenddate == '') {
            $hdlenddate = $hdlstartdate;
        }

        $hdlrepeatlogo = get_post_meta($post->ID, 'hdlrepeatlogo', true);
        if ($hdlrepeatlogo == 1) {
            if ($curYear > $startYear) {
                $hdlstartdate = date('m/d/Y', strtotime($hdlstartdate) + (24 * 3600 * 365)) . '<br/>';
                $hdlenddate = date('m/d/Y', strtotime($hdlenddate) + (24 * 3600 * 365));
            }
        }	

        if (strtotime($hdlstartdate) <= strtotime($currentDate) && strtotime($hdlenddate) >= strtotime($currentDate)) {
            $hdl_description = get_post_meta($post->ID, 'hdl_description', true);
            $hdlwidth1 = get_post_meta($post->ID, 'hdlwidth1', true);
            $hdlwidthsize1 = get_post_meta($post->ID, 'hdlwidthsize1', true);
            $hdlheight1 = get_post_meta($post->ID, 'hdlheight1', true);
            $hdlheightsize1 = get_post_meta($post->ID, 'hdlheightsize1', true);
            $hdlupload_image = get_post_meta($post->ID, 'hdlupload_image', true);
            $hdlwidth2 = get_post_meta($post->ID, 'hdlwidth2', true);
            $hdlwidthsize2 = get_post_meta($post->ID, 'hdlwidthsize2', true);
            $hdlheight2 = get_post_meta($post->ID, 'hdlheight2', true);
            $hdlheightsize2 = get_post_meta($post->ID, 'hdlheightsize2', true);
            $hdlupload_image2 = get_post_meta($post->ID, 'hdlupload_image2', true);
            $hdlvideourl = get_post_meta($post->ID, 'hdlvideourl', true);
            $hdlvideotype = get_post_meta($post->ID, 'hdlvideotype', true);
            $hdlwidth3 = get_post_meta($post->ID, 'hdlwidth3', true);
            $hdlheight3 = get_post_meta($post->ID, 'hdlheight3', true);

            if ($types == 'Logo') {
                if ($hdlupload_image == '') {
                    $hdlupload_image = get_option('hdlupload_default_image');
                }
                if ($hdlupload_image == '') {
                    return null;
                }
                ($hdlwidth1 != '' ) ? $style = 'width:' . $hdlwidth1 . ($hdlwidthsize1 != '') ? $hdlwidthsize1 : 'px'  : '';
                ($hdlheight1 != '' ) ? $style .= 'height:' . $hdlheight1 . ($hdlheightsize1 != '') ? $hdlheightsize1 : 'px' : '';
                return '<img src="' . $hdlupload_image . '" style="' .$style. '" alt="' .$hdl_description. '" />';
            }
            if ($types == 'Image') {
                if ($hdlupload_image2 == '') {
                    $hdlupload_image2 = get_option('hdlupload_default_image2');
                }
                if ($hdlupload_image2 == '') {
                    return null;
                }
                ($hdlwidth2 != '' ) ? $style2 = 'width:' . $hdlwidth2 . ($hdlwidthsize1 != '') ? $hdlwidthsize2 : 'px'  : '';
                ($hdlheight2 != '' ) ? $style2 .= 'height:' . $hdlheight2 . ($hdlheightsize1 != '') ? $hdlheightsize2 : 'px' : '';
                return '<img src="' . $hdlupload_image2 . '" style="' .$style2. '" alt="' .$hdl_description. '" />';
            }
            if ($types == 'Video') {
                if ($hdlvideourl == '') {
                    $hdlvideourl = get_option('hdldefaultvideourl');
					$hdlvideotype = get_option( 'hdldefaultvideotype' );
					$hdlwidth3 = get_option( 'hdldefaultwidth3' );
					$hdlheight3 = get_option( 'hdldefaultheight3' );
                }
                if ($hdlvideourl == '') {
                    return null;
                } else {
                    $iframeUrl = getVideoEmbedUrl($hdlvideotype, $hdlvideourl);
                }

		$iframeUrl = apply_filters('hlgurlchange',$iframeUrl);

                $iframe =  '<iframe ';

                if(count($attr)> 0) {
                    foreach ($attr as $a) {
                        $pattern = '/[=]/';
                        preg_match_all($pattern,$a,$matches,PREG_SET_ORDER);
                        if( count($matches)==1){
                            if ( preg_match( '#[^\']?(.+)=[^\']?(.+)$#', $a, $match ) ) {
                                list( $key, $value ) = explode( '=', $a );
                                $iframe .= "$key=\"".$value."\"";
                            }
                        }
                    }
                }

                $iframe .= ' width="' . $hdlwidth3 . '" height="' . $hdlheight3 . '" src="' . $iframeUrl . '"  frameborder="0" allowfullscreen></iframe>'; 
                return $iframe;

                //return '<iframe width="' . $hdlwidth3 . '" height="' . $hdlheight3 . '" src="' . $iframeUrl . '"  frameborder="0" allowfullscreen></iframe>';
            }
        }
    }

    if ($default === true) { // this seems to fire if there are no active holidays for today()
        if ($types == 'Logo') {
            $src = get_option('hdlupload_default_image');
            if (strlen($src) > 3) {
                return '<img src="' . $src . '" alt="' .get_bloginfo('name'). '"/>';
            }
        }

        if ($types == 'Image') {
            $src = get_option('hdlupload_default_image2');
            if (strlen($src) > 3) {
                return '<img src="' . $src . '" />';
            }
        }

        if ($types == 'Video') {
            $videoWidth  = get_option('hdldefaultwidth3');
            $videoHeight = get_option('hdldefaultheight3');
            $videoUrl    = get_option('hdldefaultvideourl');
            $videoType   = get_option('hdldefaultvideotype');

            $iframeUrl   = getVideoEmbedUrl($videoType, $videoUrl);

            if (!is_null($iframeUrl)) {
                $html = '<iframe src="' . getVideoEmbedUrl($videoType, $videoUrl) .'"';

                if (is_numeric($videoWidth) && $videoWidth > 0) {
                    $html .= ' width="' . $videoWidth . '"';
                }
                if (is_numeric($videoHeight) && $videoHeight > 0) {
                    $html .= ' height="' . $videoHeight . '"';
                }

                $html .= ' frameborder="0" allowfullscreen></iframe>';

                return $html;
            } else {
                return '';
            }

        }
    }
}

add_shortcode('holidayevent', 'holiday_logo');


function getVideoEmbedUrl($videoType, $videoUrl) {

    $id = null;

    switch ($videoType) {
        case 'youtube':
            // Check possible formats
            $matches = array();
            preg_match('/.*watch\?v=(.*)/', $videoUrl, $matches);

            if (count($matches) > 0) {
                $id = $matches[1];
            } else {
                preg_match('/.*embed\/(.*)/', $videoUrl, $matches);

                if (count($matches) > 0) {
                    $id = $matches[1];
                } else {
                    return null;
                }
            }

            return 'https://www.youtube.com/embed/' . $id;
            break;

        case 'vimeo':
            // Check possible formats
            $matches = array();
            preg_match('/.*\/(\d*)/', $videoUrl, $matches);

            if (count($matches) > 0) {
                return 'https://player.vimeo.com/video/' . $matches[1];
            } else {
                return null;
            }
            break;
    }
}




function add_days($date, $days) {
    $timeStamp = strtotime(date('Y-m-d', $date));
    $timeStamp+= 24 * 60 * 60 * $days;

    // ...clock change....
    if (date("I", $timeStamp) != date("I", $date)) {
        if (date("I", $date) == "1") {
            // summer to winter, add an hour
            $timeStamp+= 60 * 60;
        } else {
            // summer to winter, deduct an hour
            $timeStamp-= 60 * 60;
        } // if
    } // if
    $cur_dat = mktime(0, 0, 0, date("n", $timeStamp), date("j", $timeStamp), date("Y", $timeStamp)
    );
    return $cur_dat;
}

add_filter("wp_mail_content_type", "hdl_mail_content_type");

function hdl_mail_content_type() {
    return "text/html";
}

add_filter("wp_mail_from", "hdl_mail_from");

function hdl_mail_from() {
    return get_option('admin_email');
}

add_filter("wp_mail_from_name", "hdl_mail_from_name");

function hdl_mail_from_name() {
    return get_option('blogname');
}

function hdl_notification() {
    $message = '<html><body>';
    $message .= '<table>';
    $message .= '<tr><td>Hi,<td/></tr>';
    $message .= '<tr><td>&nbsp;</td></tr>';
    $message .= '<tr><td>This is just a reminder from holiday logos plugin at ' . get_option('blogname') . ' about a schedule changed to your site.<td/></tr>';
    $message .= '<tr><td>Please visit <a href="' . admin_url() . '/edit.php?post_type=holiday_logos&page=holiday-logos.php">Settings</a> to make any adjustments or changes, if needed.<td/></tr>';
    $message .= '<tr><td>&nbsp;</td></tr>';
    $message .= '<tr><td>Thanks</td></tr>';
    $message .= '<tr><td><a href="http://holidayLogos.com">HolidayLogos.com</a></td></tr>';
    $message .= '</table>';
    $message .= '</body></html>';
    $toEmail = get_option('admin_email');
    wp_mail($toEmail, 'Holiday Logo Reminder', $message);
}

//add_action('hdl_send_email_every_day', 'hdl_send_email_');

function hdl_send_email_() {
    $hdlGetPosts = get_posts(array('post_type' => 'holiday_logos', 'posts_per_page' => 40));
    $date_formats = array_unique(apply_filters('date_formats', array(__('F j, Y'), 'Y-m-d', 'm/d/Y', 'd/m/Y')));

    foreach ($date_formats as $format) {
        if (get_option('date_format') === $format) {
            $currentDate = date_i18n($format);
            $curYear = date('Y', strtotime($currentDate));
            break;
        }
    }
   
    foreach ($hdlGetPosts as $post) {
        $hdlstartdate = get_post_meta($post->ID, 'hdlstartdate', true);
 	    $hdlstartdate = hdl_get_formatted_date($hdlstartdate);
        $startYear = date('Y', strtotime($hdlstartdate));
        $hdlenddate = get_post_meta($post->ID, 'hdlenddate', true);
	    $hdlenddate = hdl_get_formatted_date($hdlenddate);

        if ($hdlenddate  == '') {
             $hdlenddate = $hdlstartdate;  
        }

        $hdlemailme = get_post_meta($post->ID, 'hdlemailme', true);
        $hdlrepeatlogo = get_post_meta($post->ID, 'hdlrepeatlogo', true);
        if ($hdlrepeatlogo == 1) {
            if ($curYear > $startYear) {
                $hdlstartdate = date('m/d/Y', strtotime($hdlstartdate) + (24 * 3600 * 365)) . '<br/>';
                $hdlenddate = date('m/d/Y', strtotime($hdlenddate) + (24 * 3600 * 365));
            }
        }

        if (strtotime($hdlstartdate) <= strtotime($currentDate) && strtotime($hdlenddate) >= strtotime($currentDate)) {
            if ($hdlemailme == 1) {
                if (strtotime($hdlstartdate) == strtotime($currentDate)) {
                    if (get_post_meta($post->ID, 'check_start_email', true) != 1) {
                        hdl_notification();
                        add_post_meta($post->ID, 'check_start_email', 1);
                        break;
                    }
                }
            }
          }
    }
}

function hdl_export_report(){
    if (array_key_exists('page', $_GET) && $_GET['page'] == 'report.php' &&
        array_key_exists('import', $_GET) && $_GET['import'] == 'csv'
    ){
        if ( ! isset( $_GET['holiday-logos_report'] )
            || ! wp_verify_nonce( $_GET['holiday-logos_report'], 'export-csv_' . date('Y-m-d') )
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        }

        $hdlGetPosts = get_posts(array('post_type' => 'holiday_logos', 'posts_per_page' => 200));

        ob_clean();
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=holiday_logos_report.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $csvstream = fopen("php://output", "w");
        fputcsv($csvstream, array('Holiday Name','Start Date','End Date'));

        foreach($hdlGetPosts as $hdlpost)
        {
            $csv_r = array();
            $title = $hdlpost->post_title;
            array_push($csv_r, $title);
            $start = hdl_get_formatted_date(get_post_meta($hdlpost->ID, 'hdlstartdate', true));
            $end = hdl_get_formatted_date(get_post_meta($hdlpost->ID, 'hdlenddate', true));
            $end = (strlen($end) >= 8) ? $end : $start;
            array_push($csv_r, $start);
            array_push($csv_r, $end);
            
            fputcsv($csvstream, $csv_r);
        }
        fclose($csvstream);

        exit;
    }	
}

function hdl_get_time_stamp($date, $seperator ='/' ){
    if($date){
        list($month, $day, $year) = explode($seperator, $date);
        return mktime(0, 0, 0, $month, $day, $year);
    }else
        return $date;

}

function hdl_get_formatted_date($timestamp, $format='m/d/Y'){
    if($timestamp)
        return date($format,$timestamp) ;
    else
        return $timestamp;
}

add_action('init', 'hdl_export_report');


?>
