<?php
	if(!defined('KB_SITE')) die ("Go Away!");        
        
	require_once("common/admin/admin_menu.php");
    require_once("mods/info_links/init.php");
	
	$module = "Info Links Settings";
	
	$page = new Page("$module");
	
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
	
    // update values
	if ($_SERVER['REQUEST_METHOD'] == "POST")
	{
		$infoLinksConfig = array(
			'allianceDetail' => array(
				'dotlan' => isSettingEnabled($_POST['alliance_dotlan']),
				'evewho' => isSettingEnabled($_POST['alliance_evewho']),
				'evegate' => isSettingEnabled($_POST['alliance_evegate'])
			),
			'corporationDetail' => array(
				'dotlan' => isSettingEnabled($_POST['corporation_dotlan']),
				'evewho' => isSettingEnabled($_POST['corporation_evewho']),
				'evegate' => isSettingEnabled($_POST['corporation_evegate'])
			),
			'pilotDetail' => array(
				'evegate' => isSettingEnabled($_POST['pilot_evewho']),
				'evewho' => isSettingEnabled($_POST['pilot_evegate'])
			),
			'killDetail' => array(
				'dotlan' => isSettingEnabled($_POST['kill_dotlan']),
				'osmium' => isSettingEnabled($_POST['kill_osmium'])
			),
			'systemDetail' => array(
				'dotlan' => isSettingEnabled($_POST['system_dotlan'])
			)
		);
		
		config::set('infoLinksSettings', $infoLinksConfig);
		
		$html .= 'Settings saved!';
	}
	
	if(config::get('infoLinksSettings'))
	{
		$infoLinksConfig = config::get('infoLinksSettings');
	}
	
	$html .='<form name="update" id="update" method="post">';
	
	// alliance details view settings
	$html .='<div class="block-header2">Alliance Detail View</div>';
	$html .='<table class="kb-subtable">';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Dotlan link</strong></td>';
	$html .='<td><input type="checkbox" name="alliance_dotlan" '; if($infoLinksConfig['allianceDetail']['dotlan']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Eve Gate link</strong></td>';
	$html .='<td><input type="checkbox" name="alliance_evegate" '; if($infoLinksConfig['allianceDetail']['evegate']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Eve Who link</strong></td>';
	$html .='<td><input type="checkbox" name="alliance_evewho" '; if($infoLinksConfig['allianceDetail']['evewho']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .=<<<HTML
	</td>
		</tr>
	</table>
HTML;

	// corporation details view settings
	$html .='<div class="block-header2">Corporation Detail View</div>';
	$html .='<table class="kb-subtable">';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Dotlan link</strong></td>';
	$html .='<td><input type="checkbox" name="corporation_dotlan" '; if($infoLinksConfig['corporationDetail']['dotlan']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Eve Gate link</strong></td>';
	$html .='<td><input type="checkbox" name="corporation_evegate" '; if($infoLinksConfig['corporationDetail']['evegate']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Eve Who link</strong></td>';
	$html .='<td><input type="checkbox" name="corporation_evewho" '; if($infoLinksConfig['corporationDetail']['evewho']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .=<<<HTML
	</td>
		</tr>
	</table>
HTML;

	// pilot details view settings
	$html .='<div class="block-header2">Pilot Detail View</div>';
	$html .='<table class="kb-subtable">';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Eve Gate link</strong></td>';
	$html .='<td><input type="checkbox" name="pilot_evegate" '; if($infoLinksConfig['pilotDetail']['evegate']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Eve Who link</strong></td>';
	$html .='<td><input type="checkbox" name="pilot_evewho" '; if($infoLinksConfig['pilotDetail']['evewho']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .=<<<HTML
	</td>
		</tr>
	</table>
HTML;

	// kill details view settings
	$html .='<div class="block-header2">Kill Detail View</div>';
	$html .='<table class="kb-subtable">';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Dotlan link</strong></td>';
	$html .='<td><input type="checkbox" name="kill_dotlan" '; if($infoLinksConfig['killDetail']['dotlan']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Osmium link</strong></td>';
	$html .='<td><input type="checkbox" name="kill_osmium" '; if($infoLinksConfig['killDetail']['osmium']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	$html .=<<<HTML
	</td>
		</tr>
	</table>
HTML;

	// system details view settings
	$html .='<div class="block-header2">System Detail View</div>';
	$html .='<table class="kb-subtable">';
	
	$html .='<tr>';
	$html .='<td width="150"><strong>Show Dotlan link</strong></td>';
	$html .='<td><input type="checkbox" name="system_dotlan" '; if($infoLinksConfig['systemDetail']['dotlan']) $html .= 'checked="checked"'; $html .= '" /></td>';
	$html .='</tr>';
	
	
	$html .=<<<HTML
	</td>
		</tr>
	</table>
HTML;

	$html .=<<<HTML
	<div class="block-header2">Save changes</div>
	<table class="kb-subtable">
		<tr>
			<td width="160"></td>
			<td><input type="submit" name="submit" value="Save" /></td>
		</tr>
	</table>
	</form>
HTML;
	$html .= "<div style=\"padding: 5px; margin: 20px 10px 10px; text-align: right; border-top: 1px solid #ccc\">$module ".INFO_LINKS_MOD_VERSION." by <a href=\"https://gate.eveonline.com/Profile/Salvoxia/\">Salvoxia</a>.</div>";
	$page->setContent($html);
	$page->addContext($menubox->generate());
	$page->generate();
	
	/**
	 * evaluates the setting of a checkbox
	 * @param string $checkboxSetting the POST setting of a checkbox input element
	 * @return TRUE if the checkbox is checked, otherwise FLASE
	 */
	function isSettingEnabled($checkboxSetting)
	{
		if($checkboxSetting == "on")
		{
			return TRUE;
		}
		
		return FALSE;
	}
?>
