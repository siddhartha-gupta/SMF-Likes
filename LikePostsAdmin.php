<?php

/**
* @package manifest file for Like Posts
* @version 1.0 Alpha
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

if (!defined('SMF'))
	die('Hacking attempt...');

function LP_modifySettings($return_config = false) {
	global $txt, $scripturl, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	require_once($sourcedir . '/Subs-LikePosts.php');
	loadLanguage('LikePosts');

	$context['page_title'] = $txt['lp_admin_panel'];
	$default_action_func = 'LP_generalSettings';

	// Load up the guns
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['lp_admin_panel'],
		'tabs' => array(
			'generalsettings' => array(
				'label' => $txt['lp_general_settings'],
				'url' => 'generalsettings',
			),
		),
	);
	$context[$context['admin_menu_name']]['tab_data']['active_button'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'generalsettings';

	$subActions = array(
		'generalsettings' => 'LP_generalSettings',
		'savegeneralsettings' => 'LP_saveGeneralSettings',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['sa']) && isset($_REQUEST['sa']) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$key]();

	// At this point we can just do our default.
	$default_action_func();
}

/*
 *default/basic function
 */
function LP_generalSettings($return_config = false) {
	global $txt, $scripturl, $context, $sourcedir, $user_info;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$context['page_title'] = $txt['lp_admin_panel'];
	$context['sub_template'] = 'lp_admin_general_settings';
	$context['like_posts']['tab_name'] = $txt['lp_general_settings'];
	$context['like_posts']['tab_desc'] = $txt['lp_general_settings_desc'];
}

function LP_saveGeneralSettings() {
	global $context;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
}

?>