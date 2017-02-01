<?php

/**
 * @author Evoke. Salvoxia
 * @copyright 2015
 * @version 1.0
 */

define('INFO_LINKS_MOD_VERSION', '1.0');
$modInfo['info_links']['name'] = "Info Links v".INFO_LINKS_MOD_VERSION;
$modInfo['info_links']['abstract'] = "adds Dotlan, Eve Gate and Eve Who links to detail views";
$modInfo['info_links']['about'] = "by <a href=\"http://gate.eveonline.com/Profile/Salvoxia\">Salvoxia</a>";



// initialize config
if(!config::get('infoLinksSettings'))
{
	// initialize config
	$infoLinksConfig = array(
		'allianceDetail' => array(
			'dotlan' => TRUE,
			'evewho' => TRUE,
			'evegate' => TRUE
		),
		'corporationDetail' => array(
			'dotlan' => TRUE,
			'evewho' => TRUE,
			'evegate' => TRUE
		),
		'pilotDetail' => array(
			'evegate' => TRUE,
			'evewho' => TRUE
		),
		'killDetail' => array(
			'dotlan' => TRUE,
			'osmium' => TRUE
		),
		'systemDetail' => array(
			'dotlan' => TRUE
		)
	);
	
	config::set('infoLinksSettings', $infoLinksConfig);
}

// register for context assembling on alliance detail view
event::register("allianceDetail_context_assembling", "InfoLinks::hookAllianceDetail");

// register for context assembling on corporation detail view
event::register("corpDetail_context_assembling", "InfoLinks::hookCorporationDetail");

// register for context assembling on pilot detail view
event::register("pilotDetail_context_assembling", "InfoLinks::hookPilotDetail");

// register for context assembling on kill detail view
event::register("killDetail_context_assembling", "InfoLinks::hookKillDetail");

// register for context assembling on system detail view
event::register("systemdetail_context_assembling", "InfoLinks::hookSystemDetail");


class InfoLinks {

    public static function hookAllianceDetail(&$allianceDetail)
	{	
		$allianceDetail->addBehind('menuSetup', 'InfoLinks::allianceDetail');
	}
	 
	public static function allianceDetail(&$allianceDetail)
	{
		// load config
		$allianceConfig = array();
		$config = config::get('infoLinksSettings');
		if(is_array($config) && isset($config['allianceDetail']) && is_array($config['allianceDetail'])) $allianceConfig = $config['allianceDetail'];
		
		$allianceDetail->addMenuItem("caption","Info Links");
		
		$allianceName = $allianceDetail->alliance->getName();
		// only for real alliances
    $allianceExternalID = $allianceDetail->alliance->getExternalID();
		if(isset($allianceExternalID))
		{
			// DOTLAN
			if(isset($allianceConfig['dotlan']) && $allianceConfig['dotlan'])
			{
        $allianceDetail->addMenuItem("link", "zKillboard", 'https://zkillboard.com/alliance/' . $allianceExternalID);
        
				$allianceNameEscaped = preg_replace('/\\s/', '_', $allianceName);
				$allianceDetail->addMenuItem("link", "Dotlan", 'http://evemaps.dotlan.net/alliance/' . $allianceNameEscaped);
			}
			
			// EVE GATE
			if(isset($allianceConfig['evegate']) && $allianceConfig['evegate'])
			{
				$allianceDetail->addMenuItem("link", "Eve Gate", 'https://gate.eveonline.com/Alliance/' . $allianceName); 
			}
			
			// EVE WHO
			if(isset($allianceConfig['evewho']) && $allianceConfig['evewho'])
			{
				$allianceNameEscaped = preg_replace('/\\s/', '+', $allianceName);
				$allianceDetail->addMenuItem("link", "Eve Who", 'http://evewho.com/alli/' . $allianceNameEscaped);  
			}
		}
	}
	
	
		
		
		
		
	public static function hookCorporationDetail(&$corpDetail)
	{	
		$corpDetail->addBehind('menuSetup', 'InfoLinks::corporationDetail');
	}
	 
	public static function corporationDetail(&$corpDetail)
	{
		// load config
		$corporationConfig = array();
		$config = config::get('infoLinksSettings');
		if(is_array($config) && isset($config['corporationDetail']) && is_array($config['corporationDetail'])) $corporationConfig = $config['corporationDetail'];
		
		$corpDetail->addMenuItem("caption","Info Links");
		
		$corpName = $corpDetail->corp->getName();
		// only for real alliances
    $corpExternalID = $corpDetail->corp->getExternalID();
		if(isset($corpExternalID))
		{
			// DOTLAN
			if(isset($corporationConfig['dotlan']) && $corporationConfig['dotlan'])
			{
        $corpDetail->addMenuItem("link", "zKillboard", 'https://zkillboard.com/corporation/' . $corpExternalID);
				$corpNameEscaped = preg_replace('/\\s/', '_', $corpName);
				$corpDetail->addMenuItem("link", "Dotlan", 'http://evemaps.dotlan.net/corp/' . $corpNameEscaped);
			}
			
			// EVE GATE
			if(isset($corporationConfig['evegate']) && $corporationConfig['evegate'])
			{
				$corpDetail->addMenuItem("link", "Eve Gate", 'https://gate.eveonline.com/Corporation/' . $corpName); 
			}
			
			// EVE WHO
			if(isset($corporationConfig['evewho']) && $corporationConfig['evewho'])
			{
				$corpNameEscaped = preg_replace('/\\s/', '+', $corpName);
				$corpDetail->addMenuItem("link", "Eve Who", 'http://evewho.com/corp/' . $corpNameEscaped);  
			}
		}
	}
	
	
	public static function hookPilotDetail(&$pilotDetail)
	{	
		$pilotDetail->addBehind('menuSetup', 'InfoLinks::pilotDetail');
	}
	 
	public static function pilotDetail(&$pilotDetail)
	{
		// load config
		$pilotConfig = array();
		$config = config::get('infoLinksSettings');
		if(is_array($config) && isset($config['pilotDetail']) && is_array($config['pilotDetail'])) $pilotConfig = $config['pilotDetail'];
		
		$pilotDetail->addMenuItem("caption","Info Links");
		
		// try to get the pilot...
		// no valid ID found...
		if (!$pilotDetail->plt_id && !$pilotDetail->plt_external_id) 
		{
			return;
		}
	
		$Pilot = NULL;
		if ($pilotDetail->plt_id) 
		{
			$Pilot = Cacheable::factory('Pilot', $pilotDetail->plt_id);
		} 
		
		else 
		{
			$Pilot = new Pilot(0, $pilotDetail->plt_external_id);
		}
		
		$pilotName = $Pilot->getName();
		// only for real alliances
		if($Pilot->getExternalID())
		{
			
			// EVE GATE
			if(isset($pilotConfig['evegate']) && $pilotConfig['evegate'])
			{
        $pilotDetail->addMenuItem("link", "zKillboard", 'https://zkillboard.com/character/' . $pilotDetail->plt_external_id);
				$pilotDetail->addMenuItem("link", "Eve Gate", 'https://gate.eveonline.com/Profile/' . $pilotName);
			}
			
			// EVE WHO
			if(isset($pilotConfig['evewho']) && $pilotConfig['evewho'])
			{
				$pilotNameEscaped = preg_replace('/\\s/', '+', $pilotName);
				$pilotDetail->addMenuItem("link", "Eve Who", 'http://evewho.com/pilot/' . $pilotNameEscaped);  
			}
		}
	}
	
	
	public static function hookKillDetail(&$killDetail)
	{	
		$killDetail->addBehind('menuSetup', 'InfoLinks::killDetail');
	}
	 
	public static function killDetail(&$killDetail)
	{
		// load config
		$killConfig = array();
		$config = config::get('infoLinksSettings');
		if(is_array($config) && isset($config['killDetail']) && is_array($config['killDetail'])) $killConfig = $config['killDetail'];
		
		
		$killDetail->addMenuItem("caption","Info Links");
		
		// try to get the kill...
		// no valid ID found...
		if (!$killDetail->kll_id && !$killDetail->kll_external_id) 
		{
			return;
		}
	
		$Kill = NULL;
		if ($killDetail->kll_id) 
		{
			$Kill = Cacheable::factory('Kill', $killDetail->kll_id);
		} 
		
		else 
		{
			$Kill = new Kill($killDetail->kll_external_id, true);
		}
		
		// DOTLAN
		if(isset($killConfig['dotlan']) && $killConfig['dotlan'])
		{
      $killExternalID = $killDetail->getKill()->getExternalID();
      if(isset($killExternalID))
      {
        $killDetail->addMenuItem("link", "zKillboard", 'https://zkillboard.com/kill/' . $killDetail->getKill()->getExternalID());
      }
			$systemNameEscaped = preg_replace('/\\s/', '_', $Kill->getSolarSystemName());
			$killDetail->addMenuItem("link", "Dotlan", 'http://evemaps.dotlan.net/system/' . $systemNameEscaped);
		}
		
		// O.SMIUM
		if(isset($killConfig['osmium']) && $killConfig['osmium'])
		{
			$killDetail->addMenuItem("link", "o.smium.org", "https://o.smium.org/loadout/dna/" . $killDetail->generateShipDNA());
		}
		
		
	}
	
	
	
	public static function hookSystemDetail(&$systemDetail)
	{	
		$systemDetail->addBehind('menuSetup', 'InfoLinks::systemDetail');
	}
	 
	public static function systemDetail(&$systemDetail)
	{
		// load config
		$systemConfig = array();
		$config = config::get('infoLinksSettings');
		if(is_array($config) && isset($config['systemDetail']) && is_array($config['systemDetail'])) $systemConfig = $config['systemDetail'];
		
		$systemDetail->addMenuItem("caption","Info Links");
		
		$System = new SolarSystem($systemDetail->sys_id);
		$systemName = $System->getName();
		
		// DOTLAN
		if(isset($systemConfig['dotlan']) && $systemConfig['dotlan'])
		{
			$systemNameEscaped = preg_replace('/\\s/', '_', $systemName);
			$systemDetail->addMenuItem("link", "Dotlan", 'http://evemaps.dotlan.net/system/' . $systemNameEscaped);
		}
	}
	
}