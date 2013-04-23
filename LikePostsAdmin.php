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
	global $txt, $context;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	//require_once($sourcedir . '/Subs-LikePosts.php');
	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$context['page_title'] = $txt['like_post_admin_panel'];
	$default_action_func = 'LP_generalSettings';

	// Load up the guns
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['like_post_admin_panel'],
		'tabs' => array(
			'generalsettings' => array(
				'label' => $txt['like_post_general_settings'],
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
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$_REQUEST['sa']]();

	// At this point we can just do our default.
	$default_action_func();
}

/*
 *default/basic function
 */
function LP_generalSettings($return_config = false) {
	global $txt, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	require_once($sourcedir . '/ManageServer.php');

	$general_settings = array(
		array('check', 'like_post_enable', 'subtext' => $txt['like_post_enable_desc']),
	);

	$context['page_title'] = $txt['like_post_admin_panel'];
	$context['sub_template'] = 'lp_admin_general_settings';
	$context['like_posts']['tab_name'] = $txt['like_post_general_settings'];
	$context['like_posts']['tab_desc'] = $txt['like_post_general_settings_desc'];
	prepareDBSettingContext($general_settings);
}

function LP_saveGeneralSettings() {
	global $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	if (isset($_POST['submit'])) {
		checkSession();

		$general_settings = array(
			array('check', 'like_post_enable'),
		);

		require_once($sourcedir . '/ManageServer.php');
		saveDBSettings($general_settings);
		redirectexit('action=admin;area=likeposts;sa=generalsettings');
	}
}

?>