<?php if ( ! defined( 'ABSPATH' ) ) exit;

// get all saved holidays
$args_r = array(
    'post_type' => 'holiday_logos',
    'posts_per_page' => 200
);

if (isset($_GET['order_by']) && $_GET['order_by'])
{
    $orderby = $_GET['order_by'];
    $args_r['order'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';

    if ($orderby == 'title')
    {
        $args_r['orderby'] = 'post_title';
        $title_order_class = ' ' .strtolower($args_r['order']);
        $title_sort = ($title_order_class == ' asc') ? 'desc' : 'asc';
        $start_order_class = ' desc';
        $start_sort = 'asc';
    }
    else if ($orderby == 'startdate')
    {
        $args_r['orderby'] = 'meta_value';
        $args_r['meta_key'] = 'hdlstartdate';
        $start_order_class = ' ' .strtolower($args_r['order']);
        $start_sort = ($start_order_class == ' asc') ? 'desc' : 'asc';
        $title_order_class = ' desc';
        $title_sort = 'asc';
    }
}
else
{
    $title_order_class = $start_order_class = ' desc';
    $title_sort = $start_sort = 'asc';
}

$hdlGetPosts = get_posts($args_r);

if (isset($_GET['import']) && $_GET['import'] == 'csv')
{

}
?>

<div class="wrap">
        <div class="postbox-container wp-core-ui">
            <div class="metabox-holder inside">
                <div class="meta-box-sortables">
                        <table class="form-table hdl_table">
                            <tbody>
                                <tr valign="top" class="form-field  wpform_module_title_tr class hide-fla hdl-report-title">
                                    <td colspan="5">
                                        <h1>
                                            Holiday Logos Report
                                            <span class="export-btn"><a href="<?php echo $complete_url = wp_nonce_url( admin_url('edit.php?post_type=holiday_logos&amp;page=report.php&amp;import=csv'), 'export-csv_' . date('Y-m-d'), 'holiday-logos_report'); ?>" target="_blank">Export .csv</a></span>
                                            <span class="dashicons-before dashicons-businessman support-btn"><a href="http://holidaylogos.com" target="_blank">Support</a></span> &nbsp; </h1>
                                    </td>
                                </tr>
                                <tr valign="top" class="form-field  wpform_module_title_tr class hide-fla hdl-report-th">
                                    <th scope="col" id="title" class="manage-column column-title column-primary sortable<?php echo  $title_order_class ?>">
                                        <a href="./edit.php?post_type=holiday_logos&amp;page=report.php&amp;order_by=title&amp;order=<?php echo $title_sort ?>">
                                            <span>Holiday's Logo</span><span class="sorting-indicator"></span>
                                        </a>
                                    </th>
                                    <th scope="col" id="startday" class="manage-column column-title column-primary sortable<?php echo  $start_order_class ?>">
                                        <a href="./edit.php?post_type=holiday_logos&amp;page=report.php&amp;order_by=startdate&amp;order=<?php echo $start_sort ?>">
                                            <span>Start Date</span><span class="sorting-indicator"></span>
                                        </a>
                                    </th>
                                    <th scope="col" id="endday"> <span>End Date</span></th>
                                </tr>

<?php
// loop through and output table rows
foreach($hdlGetPosts as $hdlpost)
{
    $title = $hdlpost->post_title;
    $start = hdl_get_formatted_date(get_post_meta($hdlpost->ID, 'hdlstartdate', true));
    $end = hdl_get_formatted_date(get_post_meta($hdlpost->ID, 'hdlenddate', true));
    $end = (strlen($end) >= 8) ? $end : $start;
    //$before = get_post_meta($hdlpost->ID, 'hdlextendedbefore', true);
    //$before = (is_numeric($before)) ? $before : 0;
    //$after = get_post_meta($hdlpost->ID, 'hdlextendedafter', true);
    //$after = (is_numeric($after)) ? $after : 0;
    ?>

    <tr class="iedit author-other level-0 type-holiday_logos status-publish hentry hgl-report-tr">
        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
            <strong><a class="row-title"
                       href="<?php echo admin_url('post.php?post=' . $hdlpost->ID) ?>&amp;action=edit"><?= $title ?></a></strong>
        </td>
        <td class="hdlstartdate column-hdlstartdate date column-date" data-colname="StartDate">
            <div class="hdl-report_hidden"><b>Start Day: </b></div>
            <abbr title="<?= $start ?>"><?= $start ?></abbr>
        </td>
        <td class="hdlstartdate column-hdlstartdate date column-date" data-colname="EndDate">
            <div class="hdl-report_hidden"><b>End Day: </b></div>
            <abbr title="<?= $end ?>"><?= $end ?></abbr>
        </td>
    </tr>
    <?php }  ?>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
</div>
