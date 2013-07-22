<?php

/**
* @package manifest file for Like Posts
* @version 1.0
* @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* @copyright Copyright (c) 2012, Siddhartha Gupta
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
* Contributor(s):
*
*/

function template_lp_admin_info() {
	global $context, $txt, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<span class="ie6_header floatleft">', $txt['like_post_admin_panel'] ,'</span>
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
							<dd>
								<input type="checkbox" name="', $config_var['name'], '" id="', $config_var['name'], '"', ($config_var['value'] ? ' checked="checked"' : ''), ' value="1" class="input_check" />
							</dd>
						</dl>';
					}

					echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="submit" value="', $txt['like_post_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';
		
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

					foreach ($context['like_posts']['permission_settings'] as $perm) {
						$permVals = isset($modSettings[$perm]) && !empty($modSettings[$perm]) ? (explode(',', $modSettings[$perm])) : '';

						echo ' <fieldset>';
						echo '<legend>' . $txt['like_post_perm_' . $perm] . '</legend>';
					
						foreach ($context['like_posts']['groups'] as $group) {
							echo '
								<input' . (is_array($permVals) && in_array($group['id_group'], $permVals) ? ' checked="checked"' : '') . ' id="' . $group['id_group'] . '" type="checkbox" name="' . $perm . '[]" value="' . $group['id_group'] . '" /> <label for="' . $group['id_group'] . '">' . $group['group_name'] . '</label><br />';
						}
						echo ' </fieldset>';
					}

					echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="submit" value="', $txt['like_post_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';
		
					echo '
					</div>
				<span class="botslice"><span></span></span>
			</div>
	
		</form>
	</div>
	<br class="clear">';
}

function template_lp_show_own_likes() {
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['like_post_like_you_gave'], '</h3>
		</div>';

		echo '
		<table class="table_grid" width="100%" cellspacing="0">
			<thead>
				<tr class="titlebg">
					<th class="lefttext first_th" scope="col" width="80%">', $txt['like_post_post_info'], '</th>
					<th class="lefttext last_th" scope="col" width="20%">', $txt['like_post_no_of_likes'], '</th>
				</tr>
			</thead>
			<tbody>';

	echo '
		<div class="pagesection" style="margin-bottom: 0;">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		foreach ($context['like_post']['own_like_data'] as $key => $data) {
			echo '
				<tr>
					<td class="windowbg" title="', $data['id'], '">
						<a class="some_data" href="', $data['href'] ,'">
							', $data['subject'],'
						</a>
						<div class="subject_details" style="display:none">
							', $data['body'],'
						</div>
						<br />
						<span class="smalltext">
							', $data['time'],'
						</span>
					</td>
					<td class="windowbg2 smalltext">';

				echo '
						<span>', $data['total_likes'],'</span>';

				echo '
					</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>
	</div>';

	echo '
	
	<script type="text/javascript">
		$(".some_data").hover(function() {
			console.log($(this).find(".detail").html().trim());
		});
	</script>';
}

function template_lp_show_others_likes() {
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['like_post_like_you_obtained'], '</h3>
		</div>';

		echo '
		<table class="table_grid" width="100%" cellspacing="0">
			<thead>
				<tr class="titlebg">
					<th class="lefttext first_th" scope="col" width="80%">', $txt['like_post_post_info'], '</th>
					<th class="lefttext last_th" scope="col" width="20%">', $txt['like_post_no_of_likes'], '</th>
				</tr>
			</thead>
			<tbody>';

		echo '
		<div class="pagesection" style="margin-bottom: 0;">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		foreach ($context['like_post']['others_like_data'] as $key => $data) {
			echo '
				<tr>
					<td class="windowbg" title="', $data['id'], '">
						<a class="some_data" href="', $data['href'] ,'">
							', $data['subject'],'
						</a>
						<div class="subject_details" style="display:none">
							', $data['body'],'
						</div>
						<br />
						<span class="smalltext">', $data['time'], '</span>
					</td>
					<td class="windowbg2 smalltext" onclick="lpObj.showMessageLikedInfo(', $data['id'], ');">
						<span>
							', $data['total_likes'], '
						</span>
					</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>
	</div>';
}

?>