<?php
require_once( 'common/admin/admin_menu.php' );
$page = new Page('Most Expensive Mod - Settings');

$version = "1.8"; //Version Update for me, do not change!

$versiondb = config::get('most_exp_mod_ver');
if($version != $versiondb) 
{ 
  config::set('most_exp_mod_ver', $version); 
  $html .= "<br /><b>This Mod got updated, have fun with it! New version set!</b><br /><br />";
}

$html .= "<br /><b>I HACKED THE SHIT OUT OF THIS MOD SO IT DOEN'T CARE ABOUT CORP/ALLIANCE ID. IGNORE THAT PART.</b><br /><br />";


switch($_GET["step"]){
 default:

$zeitanzeige = config::get('most_exp_mod_time'); if(!$zeitanzeige) { $zeitanzeige = 7; config::set('most_exp_mod_time', $zeitanzeige); }
$anzahlanzeige = config::get('most_exp_mod_count'); if(!$anzahlanzeige) {  $anzahlanzeige = 5; config::set('most_exp_mod_count', $anzahlanzeige); }
$versionanzeige = config::get('most_exp_mod_ver');
$whatanzeige = config::get('most_exp_mod_what'); if(!$whatanzeige) { $whatanzeige = 'kills'; config::set('most_exp_mod_what', $whatanzeige); }
$showmoney = config::get('most_exp_mod_money'); if(!$showmoney) { $showmoney = 2; config::set('most_exp_mod_money', $showmoney); }
$corpid = config::get('most_exp_mod_corp'); if(!$corpid) { $corpid = 0; config::set('most_exp_mod_corp', $corpid); }
$allianceid = config::get('most_exp_mod_alliance'); if(!$allianceid) { $allianceid = 0; config::set('most_exp_mod_alliance', $allianceid); }
$pods = config::get('most_exp_mod_pods'); if(!$pods) { $pods = 'yes'; config::set('most_exp_mod_pods', $pods); }
$podanzahl = config::get('most_exp_mod_podc'); if(!$podanzahl) { $podanzahl = 10; config::set('most_exp_mod_podc', $podanzahl); }
$html .= "
   <form name=\"add\" action=\"?a=settings_most_expensive_mod&amp;step=add\" method=\"post\">
   <table width=\"100%\">
    <tr><td width=\"30%\">Enter your CorpID: &raquo;</td><td width=\"30%\"><input type=\"text\" name=\"add_corpid\" value=\"".$corpid."\" /></td><td width=\"40%\"><small>Hint: <font color=\"red\">Enter 0, if this is an alliance board</font></small></td></tr>
    <tr><td valign=\"top\">Enter your AllianceID: &raquo;</td><td valign=\"top\"><input type=\"text\" name=\"add_allianceid\" value=\"".$allianceid."\" /></td><td valign=\"top\"><small>Hint: <font color=\"red\">Enter 0, if this is a corp board!<br />You can find your ID this way:<br />1. Click on search<br />2. Enter your corp or alliance name and select the result<br />3. Look at your browsers address bar, you are looking for the numbers after <b>&amp;crp_id=</b> or <b>&amp;all_id=</b> !</font></small></td></tr>

    <tr><td><br /><br /></td><td><br /><br /></td></tr>
    
    <tr><td>How many days to count with? &raquo;</td><td><input type=\"text\" name=\"add_zeit\" value=\"".$zeitanzeige."\" /></td><td><small>recommended: <font color=\"red\">7</font></small></td></tr>
    <tr><td>How many kills to show? &raquo;</td><td><input type=\"text\" name=\"add_anzahl\" value=\"".$anzahlanzeige."\" /></td><td><small>recommended: <font color=\"red\">5</font></small></td></tr>
    <tr><td>How many PODs to show? &raquo;</td><td><input type=\"text\" name=\"podcount\" value=\"".$podanzahl."\" /></td><td><small>recommended: <font color=\"red\">10</font></small></td></tr>

    <tr><td>What to show? &raquo;</td><td><input type=\"radio\" name=\"add_what\" value=\"kills\" ";
    if($whatanzeige == 'kills') { $html .= "checked"; }
$html .="/>Kills only</td><td>&nbsp;</td></tr>

    <tr><td>&nbsp;</td><td><input type=\"radio\" name=\"add_what\" value=\"losses\" ";
    if($whatanzeige == 'losses') { $html .= "checked"; }    
$html .="/>Losses only</td><td>&nbsp;</td></tr>

    <tr><td>&nbsp;</td><td><input type=\"radio\" name=\"add_what\" value=\"both\" ";
    if($whatanzeige == 'both') { $html .= "checked"; }   
$html .="/>Kills and losses</td><td valign=\"bottom\"><small>(Warning: <font color=\"red\">Front page will load much longer!</font>)</small></td></tr>

    <tr><td><br /><br /></td><td><br /><br /></td></tr>

    <tr><td>How to show prices? &raquo;</td><td><input type=\"radio\" name=\"add_money\" value=\"1\" ";
    if($showmoney == '1') { $html .= "checked"; }
$html .="/>Standard</td><td><small>For example: <font color=\"red\"><b>1.072.400.000</b> ISK</font></small></td></tr>

    <tr><td>&nbsp;</td><td><input type=\"radio\" name=\"add_money\" value=\"2\" ";
    if($showmoney == '2') { $html .= "checked"; }    
$html .="/>Long Tags</td><td><small>For example: <font color=\"red\"><b>1.07 Billion</b></font></small></td></tr>

    <tr><td>&nbsp;</td><td><input type=\"radio\" name=\"add_money\" value=\"3\" ";
    if($showmoney == '3') { $html .= "checked"; }    
$html .="/>Short Tags</td><td><small>For example: <font color=\"red\"><b>1.07b</b> ISK</font></small></td></tr>

    <tr><td><br /><br /></td><td><br /><br /></td></tr>

    <tr><td>Show extra POD Kills &raquo;</td><td><input type=\"radio\" name=\"podshow\" value=\"yes\" ";
    if($pods == 'yes') { $html .= "checked"; }
$html .="/>Yes</td><td></td></tr>

    <tr><td>&nbsp;</td><td><input type=\"radio\" name=\"podshow\" value=\"no\" ";
    if($pods == 'no') { $html .= "checked"; }    
$html .="/>No</td><td></td></tr>

    <tr><td></td><td><br /><input type=\"submit\" value=\"save\" /></td><td>&nbsp;</td></tr>
   </table>
   </form>
";

$html .= "<br /><br /><hr size=\"1\" /><div align=\"right\"><i><small>Most Expensive Mod (Version $versionanzeige) by <a href=\"http://www.back-to-yarrr.de\" target=\"_blank\">Sir Quentin</a></small></i></div>";

break;

case "add": 
if ($_POST) {
  $exp_time = trim($_POST["add_zeit"]);
  $exp_count = trim($_POST["add_anzahl"]);
  $exp_what = $_POST["add_what"];
  $exp_money = $_POST["add_money"];
  $exp_corp = $_POST["add_corpid"];
  $exp_alliance = $_POST["add_allianceid"];
  $exp_pods = $_POST["podshow"];
  $exp_podc = $_POST["podcount"];
  
  if($exp_corp != '0') { $exp_alliance = 0; }
  
  config::set('most_exp_mod_time', $exp_time);
  config::set('most_exp_mod_count', $exp_count);
  config::set('most_exp_mod_what', $exp_what);
  config::set('most_exp_mod_money', $exp_money);
  config::set('most_exp_mod_corp', $exp_corp);
  config::set('most_exp_mod_alliance', $exp_alliance);
  config::set('most_exp_mod_pods', $exp_pods);
  config::set('most_exp_mod_podc', $exp_podc);
  
  $html .= "Settings updated!";
}
break;
}

$page->setContent($html);
$page->addContext($menubox->generate());
$page->generate();
?>
