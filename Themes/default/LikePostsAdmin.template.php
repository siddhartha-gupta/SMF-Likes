<?php

/**
* @package manifest file for Like Posts
* @version 1.6.1
* @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* @copyright Copyright (c) 2014, Siddhartha Gupta
* @license http://www.mozilla.org/MPL/MPL-1.1.html
*/

/*
* Version: MPL 1.1
*
* The contents of this file are subject to the Mozilla Public License Version
* 1.1 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Initial Developer of the Original Code is
*  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* Portions created by the Initial Developer are Copyright (C) 2012
* the Initial Developer. All Rights Reserved.
*
* Contributor(s): Big thanks to all contributor(s)
* emanuele45 (https://github.com/emanuele45)
*
*/

function template_lp_admin_info() {
	global $context, $txt, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<span class="ie6_header floatleft">', $txt['lp_admin_panel'] ,'</span>
		</h3>
	</div>
	<p class="windowbg description">', isset($context['like_posts']['tab_desc']) ? $context['like_posts']['tab_desc'] : $txt['rp_general_desc'] ,'</p>';
	
	// The admin tabs.
		echo '
	<div id="adm_submenus">
		<ul class="dropmenu">';
	
		// Print out all the items in this tab.
		$menu_buttons = $context[$context['admin_menu_name']]['tab_data'];
		foreach ($menu_buttons['tabs'] as $sa => $tab)
		{
			echo '
			<li>
				<a class="', ($menu_buttons['active_button'] == $tab['url']) ? 'active ' : '', 'firstlevel" href="', $scripturl, '?action=admin;area=likeposts;sa=', $tab['url'],'"><span class="firstlevel">', $tab['label'], '</span></a>
			</li>';
		}
	
		// the end of tabs
		echo '
		</ul>
	</div><br class="clear" />';

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			', $context['like_posts']['tab_name'] ,'
		</h3>
	</div>';
}

function template_lp_admin_general_settings() {
	global $context, $txt, $scripturl;

	template_lp_admin_info();

	echo '
	<div id="admincenter">
		<form action="'. $scripturl .'?action=admin;area=likeposts;sa=savegeneralsettings" method="post" accept-charset="UTF-8">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
					<div class="content">';

					foreach ($context['config_vars'] as $config_var) {
						echo '
						<dl class="settings">
							<dt>
								<span>'. $txt[$config_var['name']] .'</span>';
								if (isset($config_var['subtext']) && !empty($config_var['subtext'])) {
									echo '
									<br /><span class="smalltext">', $config_var['subtext'] ,'</span>';
								}
							echo '
							</dt>
							<dd>';

							if($config_var['type'] === 'check') {
								echo '
								<input type="checkbox" name="', $config_var['name'], '" id="', $config_var['name'], '"', ($config_var['value'] ? ' checked="checked"' : ''), ' value="1" class="input_check" />';
							} elseif ($config_var['type'] === 'text') {
								echo '
								<input type="text" name="', $config_var['name'], '" id="', $config_var['name'], '" value="', $config_var['value'], '"', ($config_var['size'] ? ' size="' . $config_var['size'] . '"' : ''), ' class="input_text" />';
							}

							echo '
							</dd>
						</dl>';
					}

					echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="submit" value="', $txt['lp_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';
		
					echo '
					</div>
				<span class="botslice"><span></span></span>
			</div>
	
		</form>
	</div>
	<br class="clear">';
}

function template_lp_admin_permission_settings() {
	global $context, $txt, $scripturl, $modSettings;

	template_lp_admin_info();

	echo '
	<div id="admincenter">
		<form action="'. $scripturl .'?action=admin;area=likeposts;sa=savepermissionsettings" method="post" accept-charset="UTF-8">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
					<div class="content">';

					foreach ($context['like_posts']['groups_permission_settings'] as $perm) {
						$permVals = isset($modSettings[$perm]) && strlen($modSettings[$perm]) > 0 ? (explode(',', $modSettings[$perm])) : '';

						echo ' <fieldset>';
						echo '<legend>' . $txt['lp_perm_' . $perm] . '</legend>';
					
						foreach ($context['like_posts']['groups'] as $group) {
							echo '
								<input' . (is_array($permVals) && in_array($group['id_group'], $permVals) ? ' checked="checked"' : '') . ' id="' . $group['id_group'] . '" type="checkbox" name="' . $perm . '[]" value="' . $group['id_group'] . '" /> <label for="' . $group['id_group'] . '">' . $group['group_name'] . '</label><br />';
						}
						echo ' </fieldset>';
					}


					echo ' <fieldset>
						<legend>' . $txt['lp_guest_permissions'] . '</legend>';

						foreach ($context['like_posts']['guest_permission_settings'] as $perm) {
							echo '
								<input' . (isset($modSettings[$perm]) && !empty($modSettings[$perm]) ? ' checked="checked"' : '') . ' type="checkbox" name="' . $perm . '" value="1" /> <label>' . $txt['lp_guest_perm_' . $perm] . '</label><br />';
						}
					echo ' </fieldset>';

					echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="submit" value="', $txt['lp_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';
		
					echo '
					</div>
				<span class="botslice"><span></span></span>
			</div>
	
		</form>
	</div>
	<br class="clear">';
}

function template_lp_admin_board_settings() {
	global $context, $txt, $scripturl, $modSettings;

	template_lp_admin_info();

	echo '
	<div id="admincenter">
		<form action="'. $scripturl .'?action=admin;area=likeposts;sa=saveboardsettings" method="post" accept-charset="UTF-8">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content" id="lp_board_settings">';

				$activeBoards = isset($modSettings['lp_active_boards']) && strlen($modSettings['lp_active_boards']) > 0 ? (explode(',', $modSettings['lp_active_boards'])) : '';

				foreach ($context['categories'] as $key => $category) {
					echo ' <fieldset>';
					echo '<legend onclick="lpObj.likePostsUtils.selectInputByLegend(event, this)" data-allselected="false" style="cursor: pointer">' . $category['name'] . '</legend>';
				
					foreach ($category['boards'] as $board) {
						echo '<div style="', isset($board['child_level']) && !empty($board['child_level']) ? 'padding-left: 20px;': '' ,'">
							<input' . (is_array($activeBoards) && in_array($board['id'], $activeBoards) ? ' checked="checked"' : '') . ' id="' . $board['id'] . '" type="checkbox" name="active_board[]" value="' . $board['id'] . '" /><label for="' . $board['name'] . '">' . $board['name'] . '</label></div>';
					}
					echo ' </fieldset>';
				}
				echo '
				</div>
				<div style="padding: 0 10px 10px;">
					<input type="checkbox" value="0" onclick="lpObj.likePostsUtils.selectAllBoards(event, this)" /><label>' . $txt['lp_select_all_boards'] . '</label><br /><br />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="submit" value="', $txt['lp_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
				</div>
			</div>
	
		</form>
	</div>
	<br class="clear">';
}

function template_lp_admin_recount_stats() {
	global $context, $txt, $scripturl, $modSettings;

	template_lp_admin_info();

	echo '
	<div id="admincenter">
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
				<div class="content">';

	echo '
				<fieldset><legend>Recount members total likes</legend>
					<div style="width: 70%; float:left; position:relative">
						Reset members account
					</div>
					<div style="width: 30%; float:left; position:relative">
						<span class="floatright">
							<input type="submit" value="Run task now" class="button_submit" onclick="lpObj.likePostsAdmin.recountStats({\'activity\': \'totallikes\'}); return false;">
						</span>
					</div>
					<div class="member_count_precentage"></div>
				</fieldset>';

	echo '
				</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>
	<br class="clear">';
}

?>
