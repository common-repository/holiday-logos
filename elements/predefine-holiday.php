<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once 'floating-holiday.php';
ob_start();
$current_year = date('Y');
$previous_year= $current_year-1;
/*$hdleventdate = array(
                        "New Year's Day"=>'01/01/2015',
                        "Groundhog's Day"=>'02/02/2015',
                        "Valentine's Day"=>'02/14/2015',
                        "Lincoln's Birthday"=>'02/12/2015',
                        "Washington's Birthday"=>'02/22/2015',
                        "President's Day"=>'02/15/2015-02/23/2015-Monday',
                        "Easter"=>'03/01/2015-04/30/2015',
                        "St. Patick's Day"=>'03/17/2015',
                        "April Fool's Day"=>'04/01/2015',
                        "Earth Day"=>'04/22/2015',
                        "Cinco De Mayo"=>'05/05/2015',
                        "Mother's Day"=>'05/07/2015-05/16/2015-Sunday',
                        "Armed Forces Day"=>'05/15/2015-05/21/2015-Saturday',
                        "Memorial Day"=>'05/24/2015-05/31/2015-Monday',
                        "Flag Day"=>'06/14/2015',
                        "Father's Day"=>'06/15/2015-06/22/2015-Sunday',
                        "Independence Day (USA)"=>'07/04/2015',
                        "Parent's Day"=>'07/22/2015-07/30/2015-Sunday',
                        "Labor Day"=>'09/01/2015-09/07/2015-Monday',
                        "Grandparent's Day"=>'10/09/2015-10/15/2015-Monday',
                        "Columbus Day"=>'02/02/2015',
                        "Halloween"=>'10/31/2015',
                        "Election Day"=>'11/02/2015-11/10/2015-Tuesday',
                        "Veteran's Day"=>'11/11/2015',
                        "Thanksgiving"=>'11/22/2015-11/31/2015-Thursday',
                        "Christmas Eve"=>'12/24/2015',
                        "Christmas Day"=>'12/25/2015',
                        "New Year's Eve"=>'12/31/2015'
                    );*/

$hdleventdate = array(
                        "New Year's Day"=>'01/01/'.$current_year,
                        "Groundhog's Day"=>'02/02/'.$current_year,
                        "Valentine's Day"=>'02/14/'.$current_year,
                        "Lincoln's Birthday"=>'02/12/'.$current_year,
                        "Washington's Birthday"=>'02/22/'.$current_year,
                        "President's Day"=>hdl_presidents_holiday($current_year),
                        "Easter"=>hdl_easter_hoiday($current_year),
                        "St. Patick's Day"=>'03/17/'.$current_year,
                        "April Fool's Day"=>'04/01/'.$current_year,
                        "Earth Day"=>'04/22/'.$current_year,
                        "Cinco De Mayo"=>'05/05/'.$current_year,
                        "Mother's Day"=>'05/07/'.$current_year.'-05/16/'.$current_year.'-Sunday',
                        "Armed Forces Day"=>'05/15/'.$current_year.'-05/21/'.$current_year.'-Saturday',
                        "Memorial Day"=>hdl_memorial_holiday($current_year),
                        "Flag Day"=>'06/14/'.$current_year,
                        "Father's Day"=>'06/15/'.$current_year.'-06/22/'.$current_year.'-Sunday',
                        "Independence Day (USA)"=>'07/04/'.$current_year,
                        "Parent's Day"=>'07/22/'.$current_year.'-07/30/'.$current_year.'-Sunday',
                        "Labor Day"=>hdl_labor_holiday($current_year),
                        "Grandparent's Day"=>'10/09/'.$current_year.'-10/15/'.$current_year.'-Monday',
                        "Columbus Day"=>hdl_columbus_holiday($current_year),
                        "Halloween"=>'10/31/'.$current_year,
                        "Election Day"=>'11/02/'.$current_year.'-11/10/'.$current_year.'-Tuesday',
                        "Veteran's Day"=>'11/11/'.$current_year,
                        "Thanksgiving"=>hdl_thanksgiving_holiday($current_year),
                        "Christmas Eve"=>'12/24/'.$current_year,
                        "Christmas Day"=>'12/25/'.$current_year,
                        "New Year's Eve"=>'12/31/'.$previous_year
                    );

 ?>
