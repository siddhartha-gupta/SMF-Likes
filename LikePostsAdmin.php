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

	$context['like_posts']['permission_settings'] = array(
		'can_like_posts',
		'can_view_likes'
	);

	// Load up the guns
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['like_post_admin_panel'],
		'tabs' => array(
			'generalsettings' => array(
				'label' => $txt['like_post_general_settings'],
				'url' => 'generalsettings',
			),
			'permissions' => array(
				'label' => $txt['like_post_permission_settings'],
				'url' => 'permissionsettings',
			),
		),
	);
	$context[$context['admin_menu_name']]['tab_data']['active_button'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'generalsettings';

	$subActions = array(
		'generalsettings' => 'LP_generalSettings',
		'savegeneralsettings' => 'LP_saveGeneralSettings',
		'permissionsettings' => 'LP_permissionSettings',
		'savepermissionsettings' => 'LP_savePermissionsettings',
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

function LP_permissionSettings() {
	global $txt, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	require_once($sourcedir . '/ManageServer.php');

	require_once($sourcedir . '/Subs-Membergroups.php');
	$context['like_posts']['groups'][-1] = array(
		'id_group' => -1,
		'group_name' => $txt['guests'],
	);
	$context['like_posts']['groups'][0] = array(
		'id_group' => 0,
		'group_name' => $txt['like_post_regular_members'],
	);
	$context['like_posts']['groups'] += list_getMembergroups(null, null, 'id_group', 'regular');
	unset($context['like_posts']['groups'][3]);
	unset($context['like_posts']['groups'][1]);

	$context['page_title'] = $txt['like_post_admin_panel'];
	$context['sub_template'] = 'lp_admin_permission_settings';
	$context['like_posts']['tab_name'] = $txt['like_post_permission_settings'];
	$context['like_posts']['tab_desc'] = $txt['like_post_permission_settings_desc'];
}

function LP_savePermissionsettings() {
	global $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	if (isset($_POST['submit'])) {
		checkSession();
		unset($_POST['submit']);

		$general_settings = array();
		foreach($_POST as $key => $val) {
			if(in_array($key, $context['like_posts']['permission_settings'])) {
				if(array_filter($_POST[$key], 'is_numeric') === $_POST[$key]) {
					$_POST[$key] = implode(',', $_POST[$key]);
					$general_settings[] = array('text', $key);
				}
			}
		}

		require_once($sourcedir . '/ManageServer.php');
		saveDBSettings($general_settings);
		redirectexit('action=admin;area=likeposts;sa=permissionsettings');
	}
}

?>