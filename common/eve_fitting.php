<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */

// Original by TEKAI
// Ammo addition and little modifications by Wes Lave

$kll_id = (int)edkURI::getArg('kll_id', 1);
$kill = Cacheable::factory('Kill', $kll_id);
$ship = $kill->getVictimShip();
$pilotname = $kill->getVictimName();
$shipclass = $ship->getClass();
$shipname = $ship->getName();
$killtitle .= $pilotname."'s ".$shipname;

$fitting_array[InventoryFlag::$HIGH_SLOT_1] = array();    // high slots
$fitting_array[InventoryFlag::$MED_SLOT_1] = array();    // med slots
$fitting_array[InventoryFlag::$LOW_SLOT_1] = array();    // low slots
$fitting_array[InventoryFlag::$RIG_SLOT_1] = array();    // rig slots
$fitting_array[InventoryFlag::$DRONE_BAY] = array();    // drone bay
$fitting_array[InventoryFlag::$SUB_SYSTEM_SLOT_1] = array();    // subsystems
$ammo_array[InventoryFlag::$HIGH_SLOT_1] = array();	// high ammo
$ammo_array[InventoryFlag::$MED_SLOT_1] = array();	// mid ammo
$ammo_array[InventoryFlag::$LOW_SLOT_1] = array();	// low ammo


if (count($kill->destroyeditems_) > 0)
{
	foreach($kill->destroyeditems_ as $destroyed)
	{
		$item = $destroyed->getItem();
		$i_qty = $destroyed->getQuantity();
		$i_name = $item->getName();
		$i_location = InventoryFlag::collapse($destroyed->getLocationID());
		$i_id = $item->getID();
		$i_usedgroup = $item->get_used_launcher_group($i_name);
		
		// Nanite Repair Paste for ancillary armor repairers is a special snowflake
		// there are no type attributes indicating a used group
		// if item is nanite repair paste
		if($i_id == 28668) 
		{
			// ancillary armor repairers
			$i_usedgroup = 1199;
		}
		//Fitting, KE - add destroyed items to an array of all fitted items.
		if($i_location != InventoryFlag::$CARGO)
		{
			if(($i_usedgroup != 0))
			{
				if ($i_location == InventoryFlag::$HIGH_SLOT_1)
				{
					$i_ammo=$item->get_ammo_size($i_name);

				}
				else
				{
					$i_ammo = 0;
				}
				$ammo_array[$i_location][]=array('Name'=>$i_name, 'usedgroupID' => $i_usedgroup, 'size' => $i_ammo);
			} else
			{
				for ($count = 0; $count < $i_qty; $count++)
				{
					if ($i_location == InventoryFlag::$HIGH_SLOT_1)
					{
						$i_charge=$item->get_used_charge_size($i_name);
					}
					else
					{
						$i_charge = 0;
					}
					$fitting_array[$i_location][]=array('Name'=>$i_name, 'groupID' => $item->get_group_id($i_name), 'chargeSize' => $i_charge);
				}
			}
		}
	//fitting thing end
	}
}

if (count($kill->droppeditems_) > 0)
{
	foreach($kill->droppeditems_ as $dropped)
	{
		$item = $dropped->getItem();
		$i_qty = $dropped->getQuantity();
		$i_name = $item->getName();
		$i_location = InventoryFlag::collapse($dropped->getLocationID());
		$i_id = $item->getID();
		$i_usedgroup = $item->get_used_launcher_group($i_name);
		// Nanite Repair Paste for ancillary armor repairers is a special snowflake
		// there are no type attributes indicating a used group
		// if item is nanite repair paste
		if($i_id == 28668) 
		{
			// ancillary armor repairers
			$i_usedgroup = 1199;
		}
		
		//Fitting -KE, add dropped items to the list
		if($i_location != InventoryFlag::$CARGO)
		{
			if(($i_usedgroup != 0))
			{
				if ($i_location == InventoryFlag::$HIGH_SLOT_1)
				{
					$i_ammo=$item->get_ammo_size($i_name);
				}
				else
				{
					$i_ammo = 0;
				}
				$ammo_array[$i_location][]=array('Name'=>$i_name, 'usedgroupID' => $i_usedgroup, 'size' => $i_ammo);
			} else
			{
				for ($count = 0; $count < $i_qty; $count++)
				{
					if ($i_location == InventoryFlag::$HIGH_SLOT_1)
					{
						$i_charge=$item->get_used_charge_size($i_name);
					}
					else
					{
						$i_charge = 0;
					}
					$fitting_array[$i_location][]=array('Name'=>$i_name, 'groupID' => $item->get_group_id($i_name), 'chargeSize' => $i_charge);
				}
			}
		}
	//fitting thing end


	}
}



$slots = array(InventoryFlag::$LOW_SLOT_1 => "low slot",
	InventoryFlag::$MED_SLOT_1 => "med slot",
	InventoryFlag::$HIGH_SLOT_1 => "hi slot",
	InventoryFlag::$RIG_SLOT_1 => "rig slot",
	InventoryFlag::$SUB_SYSTEM_SLOT_1 => "subsystem slot",
	InventoryFlag::$DRONE_BAY => "drone bay");


// Some tools require xml formatted with indents.
// So let's do this the ugly way
/*
$xml = "<?xml version=\"1.0\" ?>
	<fittings>
	</fittings>\n";

$sxe = new SimpleXMLElement($xml);
$fittingxml = $sxe->addChild('fitting');
$fittingxml->addAttribute('name', $killtitle);
$desc = $fittingxml->addChild('description');
$desc->addAttribute("value", "From ".KB_HOST."?a=kill_detail&amp;kll_id=".$kll_id);
$shiptype = $fittingxml->addChild('shipType');
$shiptype->addAttribute('value', $shipname);

foreach ($slots as $i => $empty)
{
	if (!empty($fitting_array[$i]))
	{
		$usedslots = 0;
		foreach ($fitting_array[$i] as $k => $a_item)
		{
			$item = $a_item['Name'];
			$hardware = $fittingxml->addChild('hardware');
			if($i == 6)
			{
				$hardware->addAttribute('slot', $slots[$i]);
				$hardware->addAttribute('type', $a_item['Name']);
				$hardware->addAttribute('quantity', '1');
			}
			else
			{
				$hardware->addAttribute('slot', $slots[$i].' '.$usedslots);
				$hardware->addAttribute('type', $a_item['Name']);
			}

			$usedslots++;
		}
	}
}

echo $sxe->asXML();
*/

$xml = "<?xml version=\"1.0\" ?>
	<fittings>\n";

$xml .= "\t\t<fitting name=\"".$killtitle."\">\n";
$xml .= "\t\t\t<description value=\"From ".KB_HOST."?a=kill_detail&amp;kll_id=".$kll_id."\"/>\n";
$xml .= "\t\t\t<shipType value=\"".$shipname."\"/>\n";

foreach ($slots as $i => $empty)
{
	if (!empty($fitting_array[$i]))
	{
		$usedslots = 0;
		foreach ($fitting_array[$i] as $k => $a_item)
		{
			$item = $a_item['Name'];
			$xml .= "\t\t\t<hardware ";
			if($i == InventoryFlag::$DRONE_BAY)
			{
				$xml .= "qty=\"1\" ";
				$xml .= "slot=\"".$slots[$i]."\" ";
				$xml .= "type=\"".$a_item['Name']."\"/>\n";
			}
			else
			{
				$xml .= "slot=\"".$slots[$i]." ".$usedslots."\" ";
				$xml .= "type=\"".$a_item['Name']."\"/>\n";
			}

			$usedslots++;
		}
	}
}
$xml .= "\t\t</fitting>\n\t</fittings>";

if(!IS_IGB)
{
	header("Content-Type: text/xml");
	header('Content-Disposition: attachment; filename="'.$shipname.'.xml"');
	echo $xml;
}
else
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" ';
	echo '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> ';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr">';
	echo "<head><title>Eve fitting xml</title></head><body><form action = ''>\n<table><tr><td>\n<textarea id=\"fitting\" name=\"fitting\" cols=\"80\" rows=\"40\" readonly=\"readonly\">\n";
	echo htmlspecialchars($xml, ENT_NOQUOTES);
	echo "\n</textarea></td></tr>";
	echo '<tr><td><input type="button" value="Select All" onclick="this.form.fitting.select();this.form.fitting.focus(); document.execCommand(\'Copy\')" />';
	echo "</td></tr></table></form></body></html>";
}
