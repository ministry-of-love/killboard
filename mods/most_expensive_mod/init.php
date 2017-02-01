<?php
$modInfo['most_expensive_mod']['name'] = "Most Expensive Mod";
$modInfo['most_expensive_mod']['abstract'] = "Show the most expensive kills and losses";
$modInfo['most_expensive_mod']['about'] = "by <a href=\"http://www.back-to-yarrr.de\" target=\"_blank\">Sir Quentin</a>";

event::register('home_assembling', 'most_expensive::add');

class most_expensive{
  function add($home){
    $home->addBehind("contracts", "most_expensive::generate");
  }
  
  function generate(){
    include_once('mods/most_expensive_mod/most_expensive.php');
    return $html;
  }
}



?>