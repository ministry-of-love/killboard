<?php
$zeitanzeige = config::get('most_exp_mod_time');
$anzahlanzeige = config::get('most_exp_mod_count');
$versionanzeige = config::get('most_exp_mod_ver');
$whatanzeige = config::get('most_exp_mod_what');
$showmoney = config::get('most_exp_mod_money');
$allianzid = config::get('most_exp_mod_alliance');
$corpid = config::get('most_exp_mod_corp');
$imgdir = config::get('cfg_kbhost');
$pods = config::get('most_exp_mod_pods');
$podanzahl = config::get('most_exp_mod_podc');


$until = time() - (86400 * $zeitanzeige);
$until = date("Y-m-d H:i:s",$until);

if(empty($allianzid) && empty($corpid)) { $warning = "<small>(Warning: <font color=\"red\">Enter corp or alliance ID in admin panel!</font>)</small>"; }

if(empty($allianzid) || $allianzid == '0') { 
  $use = 'corp';
}
else { 
  $use = 'alli';
}

$qry = new DBQuery();
if($pods == 'yes')
{
  $pqry = new DBQuery();
}

$query = "SELECT * FROM kb3_kills WHERE kb3_kills.kll_timestamp > '$until' ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
$pquery = "SELECT * FROM kb3_kills WHERE kb3_kills.kll_timestamp > '$until' AND kb3_kills.kll_ship_id = '670' ORDER BY kll_isk_loss DESC LIMIT $podanzahl";

// if($whatanzeige == 'both')
// {
//   if($use == 'corp')
//   {
//     $query = "SELECT * FROM kb3_kills,kb3_inv_crp WHERE kb3_kills.kll_timestamp > '$until' AND ((kb3_kills.kll_crp_id = '$corpid') OR (kb3_kills.kll_id = kb3_inv_crp.inc_kll_id AND kb3_inv_crp.inc_crp_id = '$corpid'))GROUP BY kb3_kills.kll_id ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
//
//     if($pods == 'yes')
//     {
//       $pquery = "SELECT * FROM kb3_kills,kb3_inv_crp WHERE kb3_kills.kll_timestamp > '$until' AND ((kb3_kills.kll_crp_id = '$corpid') OR (kb3_kills.kll_id = kb3_inv_crp.inc_kll_id AND kb3_inv_crp.inc_crp_id = '$corpid')) AND kb3_kills.kll_ship_id = '670' GROUP BY kb3_kills.kll_id ORDER BY kll_isk_loss DESC LIMIT $podanzahl";
//     }
//
//   }
//   else
//   {
//     $query = "SELECT * FROM kb3_kills,kb3_inv_all WHERE kb3_kills.kll_timestamp > '$until' AND ((kb3_kills.kll_all_id = '$allianzid') OR (kb3_kills.kll_id = kb3_inv_all.ina_kll_id AND kb3_inv_all.ina_all_id = '$allianzid'))GROUP BY kb3_kills.kll_id ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
//
//     if($pods == 'yes')
//     {
//       $pquery = "SELECT * FROM kb3_kills,kb3_inv_all WHERE kb3_kills.kll_timestamp > '$until' AND ((kb3_kills.kll_all_id = '$allianzid') OR (kb3_kills.kll_id = kb3_inv_all.ina_kll_id AND kb3_inv_all.ina_all_id = '$allianzid')) AND kb3_kills.kll_ship_id = '670' GROUP BY kb3_kills.kll_id ORDER BY kll_isk_loss DESC LIMIT $podanzahl";
//     }
//
//   }
// }
//
// if($whatanzeige == 'kills')
// {
//   if($use == 'corp')
//   {
//     $query = "SELECT * FROM kb3_kills,kb3_inv_crp WHERE kb3_kills.kll_timestamp > '$until' AND kb3_kills.kll_id = kb3_inv_crp.inc_kll_id AND kb3_inv_crp.inc_crp_id = '$corpid' ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
//
//     if($pods == 'yes')
//     {
//       $pquery = "SELECT * FROM kb3_kills,kb3_inv_crp WHERE kb3_kills.kll_timestamp > '$until' AND kb3_kills.kll_id = kb3_inv_crp.inc_kll_id AND kb3_inv_crp.inc_crp_id = '$corpid' AND kb3_kills.kll_ship_id = '670' ORDER BY kll_isk_loss DESC LIMIT $podanzahl";
//     }
//
//   }
//   else
//   {
//     $query = "SELECT * FROM kb3_kills,kb3_inv_all WHERE kb3_kills.kll_timestamp > '$until' AND kb3_kills.kll_id = kb3_inv_all.ina_kll_id AND kb3_inv_all.ina_all_id = '$allianzid' ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
//
//     if($pods == 'yes')
//     {
//       $pquery = "SELECT * FROM kb3_kills,kb3_inv_all WHERE kb3_kills.kll_timestamp > '$until' AND kb3_kills.kll_id = kb3_inv_all.ina_kll_id AND kb3_inv_all.ina_all_id = '$allianzid' AND kb3_kills.kll_ship_id = '670' ORDER BY kll_isk_loss DESC LIMIT $podanzahl";
//     }
//
//   }
// }
//
// if($whatanzeige == 'losses')
// {
//   if($use == 'corp')
//   {
//     $query = "SELECT * FROM kb3_kills WHERE kll_timestamp > '$until' AND kll_crp_id = '$corpid' ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
//
//     if($pods == 'yes')
//     {
//       $pquery = "SELECT * FROM kb3_kills WHERE kll_timestamp > '$until' AND kll_crp_id = '$corpid' AND kb3_kills.kll_ship_id = '670' ORDER BY kll_isk_loss DESC LIMIT $podanzahl";
//     }
//
//   }
//   else
//   {
//     $query = "SELECT * FROM kb3_kills WHERE kll_timestamp > '$until' AND kll_all_id = '$allianzid' ORDER BY kll_isk_loss DESC LIMIT $anzahlanzeige";
//
//     if($pods == 'yes')
//     {
//       $pquery = "SELECT * FROM kb3_kills WHERE kll_timestamp > '$until' AND kll_all_id = '$allianzid' AND kb3_kills.kll_ship_id = '670' ORDER BY kll_isk_loss DESC LIMIT $podanzahl";
//     }
//
//   }
// }

$qry->execute($query);
if($pods == 'yes')
{
  $pqry->execute($pquery);
}


function check_tv_pilot($id,$corpid,$allianzid,$what)
{
    return "<font color=\"#00AA00\">&bull;</font>";
    //
    // $qry = new DBQuery();
    // if($allianzid == '0')
    // {
    //   $qry->execute("SELECT plt_name,plt_crp_id FROM kb3_pilots WHERE plt_id = '$id'");
    // }
    // else
    // {
    //   $qry->execute("SELECT plt_name,plt_crp_id,crp_all_id FROM kb3_pilots,kb3_corps WHERE kb3_pilots.plt_id = '$id' AND kb3_pilots.plt_crp_id = kb3_corps.crp_id");
    // }
    // $row = $qry->getRow();
    //
    // if($what == 'n')
    // {
    //   return $row[plt_name];
    // }
    //
    // if($what == 's')
    // {
    //   if($allianzid == '0')
    //   {
    //     if($row[plt_crp_id] == $corpid)
    //     {
    //       return "<font color=\"#DD0000\">&bull;</font>";
    //     }
    //     else
    //     {
    //       return "<font color=\"#00AA00\">&bull;</font>";
    //     }
    //   }
    //   else
    //   {
    //     if($row[crp_all_id] == $allianzid)
    //     {
    //       return "<font color=\"#DD0000\">&bull;</font>";
    //     }
    //     else
    //     {
    //       return "<font color=\"#00AA00\">&bull;</font>";
    //     }
    //   }
    // }     
}


function money($sum,$how)
{
  if(!$how) { return "<small>(Warning: <font color=\"red\">Update Settings!</font>)</small>"; }
  if($how == '1')
  {
    return "<b>".number_format($sum,0,'.','.')."</b> ISK";
  }
  
  if($sum > 1000000000000) { $sum = round(($sum/1000000000000),2); $ltag = 'Trillion'; $stag = 't'; }
  if($sum > 1000000000)    { $sum = round(($sum/1000000000),2); $ltag = 'Billion'; $stag = 'b'; }
  if($sum > 1000000)       { $sum = round(($sum/1000000),2); $ltag = 'Million'; $stag = 'm'; }
  if($sum > 1000)          { $sum = round(($sum/1000),2); $ltag = 'Thousand'; $stag = 'k'; }

  if($how == '2')
  {
    return "<b>".number_format($sum,2,'.','.')." ".$ltag."</b>";
  }

  if($how == '3')
  {
    return "<b>".number_format($sum,2,'.','.')."".$stag."</b> ISK";
  }
  
  if($how == '4')
  {
    return "<b>".number_format($sum,0,'.','.')." ".$stag."</b>";
  }
}

if($whatanzeige == 'kills') { $type = " kills "; }
if($whatanzeige == 'losses') { $type = " losses "; }
if($whatanzeige == 'both') { $type = " kills and losses "; }

$html .= "<br />
<table width=\"100%\">
<tr><td colspan=\"".$anzahlanzeige."\"><img src=\"$imgdir/mods/most_expensive_mod/icon.png\" alt=\"Most Expensive Mod by Sir Quentin\" /><b>Most expensive ".$type." for the last ".$zeitanzeige." Days</b><hr size=\"1\" /></td></tr>
<tr>";
if($warning) { $html .= "<td align=\"center\">$warning</td>"; }
while ($kills = $qry->getRow()) 
{
  $kid = $kills[kll_id];
  $vloss = $kills[kll_isk_loss];
  $kll = new Kill($kid);
  $vid = $kll->getVictimID();
  $vname = $kll->getVictimName();
  $vshipid = $kll->getVictimShipID();
  $vshipname = $kll->getVictimShipName();
  $vshipimg = $kll->getVictimShipImage(64);
  $html .= "
  <td align=\"center\">
  ".check_tv_pilot($vid,$corpid,$allianzid,s)."
  <a class=\"kb-shipclass\" href=\"?a=pilot_detail&amp;plt_id=".$vid."\">".$vname."</a><br /><br />
  <a class=\"kb-shipclass\" href=\"?a=kill_detail&amp;kll_id=".$kid."\"><img src=\"".$vshipimg."\" alt=\"".$vshipname."\" border=\"0\" /></a><br /><br />
  <a class=\"kb-shipclass\" href=\"?a=invtype&amp;id=".$vshipid."\">".$vshipname."</a><br />".money($vloss,$showmoney)."</td>";
}
$html .= "
</tr>
<tr>
  <td colspan=\"".$anzahlanzeige."\"><hr size=\"1\" />";
if($pods == 'no')
{
  $html .="
    <div align=\"right\"><i><small>Most Expensive Mod (Version $versionanzeige) by <a href=\"http://www.back-to-yarrr.de\" target=\"_blank\">Sir Quentin</a></small></i></div>
  ";
}
$html .="    
  </td>
</tr>
</table>";

if($pods == 'yes')
{
  $html .= "
  <table width=\"100%\">
    <tr><td colspan=\"".$podanzahl."\"><img src=\"$imgdir/mods/most_expensive_mod/podicon.png\" alt=\"Most Expensive Mod by Sir Quentin\" /><b>Top ".$podanzahl." most expensive pod ".$type." for the last ".$zeitanzeige." Days</tr>
    <tr><td colspan=\"".$podanzahl."\"><hr size=\"1\" /></tr>
    <tr>
    ";
    while ($pkills = $pqry->getRow()) 
    {
      $ppid = $pkills[kll_victim_id];
      $pkid = $pkills[kll_id];
      $ploss = $pkills[kll_isk_loss];
      $pplt = new Pilot($ppid);
      $pimg = $pplt->getPortraitURL(32);
      $pname = $pplt->getName();
      $html .= "
      <td align=\"center\">
        <a class=\"kb-shipclass\" href=\"?a=kill_detail&amp;kll_id=".$pkid."\"><img src=\"".$pimg."\" alt=\"".$pname."\" border=\"0\" /></a><br /><br />    
        ".money($ploss,4)."
      </td>";
    }
  $html .= "
    </tr>
    <tr>
      <td colspan=\"".$podanzahl."\"><hr size=\"1\" /><div align=\"right\"><i><small>&nbsp;</small></i></div>
      </td>
    </tr>
  </table>";
}
?>