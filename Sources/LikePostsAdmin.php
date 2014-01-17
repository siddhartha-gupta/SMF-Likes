<?php

/**
* @package manifest file for Like Posts
* @version 1.3.1
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

	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$context['page_title'] = $txt['like_post_admin_panel'];
	$default_action_func = 'LP_generalSettings';

	$context['like_posts']['permission_settings'] = array(
		'can_like_posts',
		'can_view_likes',
		'can_view_others_likes_profile'
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
			'recountstats' => array(
				'label' => $txt['like_post_recount_stats'],
				'url' => 'recountlikestats',
			),
		),
	);
	$context[$context['admin_menu_name']]['tab_data']['active_button'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'generalsettings';

	$subActions = array(
		'generalsettings' => 'LP_generalSettings',
		'savegeneralsettings' => 'LP_saveGeneralSettings',
		'permissionsettings' => 'LP_permissionSettings',
		'savepermissionsettings' => 'LP_savePermissionsettings',
		'recountlikestats' => 'LP_recountLikeStats',
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
		array('text', 'like_per_profile_page', 'subtext' => $txt['like_per_profile_page_desc']),
		array('text', 'like_in_notification', 'subtext' => $txt['like_in_notification_desc']),
		array('check', 'lp_show_like_on_boards', 'subtext' => $txt['lp_show_like_on_boards_desc']),
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
			array('text', 'like_per_profile_page'),
			array('text', 'like_in_notification'),
			array('check', 'lp_show_like_on_boards'),
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
		//unset($_POST[$context['session_var']]);

		$permissionKeys = array(
			'can_like_posts',
			'can_view_likes',
			'can_view_others_likes_profile'
		);

		$general_settings = array();
		foreach($_POST as $key => $val) {
			if(in_array($key, $context['like_posts']['permission_settings'])) {
				if(array_filter($_POST[$key], 'is_numeric') === $_POST[$key]) {
					if(($key1 = array_search($key, $permissionKeys)) !== false) {
						unset($permissionKeys[$key1]);
					}
					$_POST[$key] = implode(',', $_POST[$key]);
					$general_settings[] = array($key, $_POST[$key]);
				}
			}
		}

		if(!empty($permissionKeys)) {
			foreach ($permissionKeys as $value) {
				$general_settings[] = array($value, '');
			}
		}

		require_once($sourcedir . '/Subs-LikePosts.php');
		LP_DB_updatePermissions($general_settings);
		redirectexit('action=admin;area=likeposts;sa=permissionsettings');
	}
}

function LP_recountLikeStats() {
	global $txt, $context, $sourcedir, $settings;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	require_once($sourcedir . '/Subs-LikePosts.php');
	require_once($sourcedir . '/LikePosts.php');
	LP_includeAssets();

	$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/css/likeposts.css" />';
	$context['page_title'] = $txt['like_post_admin_panel'];
	$context['sub_template'] = 'lp_admin_recount_stats';
	$context['like_posts']['tab_name'] = $txt['like_post_recount_stats'];
	$context['like_posts']['tab_desc'] = $txt['like_post_recount_stats_desc'];

	$subActions = array(
		'totallikes' => 'LP_recountLikesTotal',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['activity']) && isset($subActions[$_REQUEST['activity']]) && function_exists($subActions[$_REQUEST['activity']]))
		return $subActions[$_REQUEST['activity']]();
}

function LP_recountLikesTotal() {
	global $txt, $context, $smcFunc;

	isAllowedTo('admin_forum');

	// Lets fire the bullet.
	@set_time_limit(300);

	$startLimit = !isset($_REQUEST['startLimit']) || empty($_REQUEST['startLimit']) ? 0 : (int) $_REQUEST['startLimit'];
	$endLimit = (int) $_REQUEST['endLimit'];

	if(!isset($_REQUEST['totalWork']) || empty($_REQUEST['totalWork'])) {
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id_member)
			FROM {db_prefix}members'
		);
		list($totalWork) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
	} else {
		$totalWork = (int) $_REQUEST['totalWork'];
	}

	$request = $smcFunc['db_query']('', '
		SELECT id_member
		FROM {db_prefix}members
		LIMIT {int:start}, {int:max}',
		array(
			'start' => $startLimit,
			'max' => 100,
		)
	);

	$insertData = array();
	$updateIds = array();
	$updateData = '';
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$calculatedLikeCount = 0;
		$request1 = $smcFunc['db_query']('', '
			SELECT COUNT(lp.id_member_received) as count, lc.like_count
			FROM {db_prefix}like_post AS lp
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = lp.id_msg)
			LEFT JOIN {db_prefix}like_count AS lc ON (lc.id_member = {int:id_member})
			where m.id_member = {int:id_member}',
			array(
				'id_member' => $row['id_member'],
			)
		);
		if ($smcFunc['db_num_rows']($request1) !== 0) {
			list ($calculatedLikeCount, $presentCount) = $smcFunc['db_fetch_row']($request1);
			if($presentCount === NULL) {
				$insertData[] = array($row['id_member'], $calculatedLikeCount);
			} else if($calculatedLikeCount !== $presentCount) {
				$updateIds[] = $row['id_member'];
				$updateData .= '
						WHEN ' . $row['id_member'] . ' THEN ' . $calculatedLikeCount;
			}
		} else {
			$insertData[] = array($row['id_member'], $calculatedLikeCount);
		}
		$smcFunc['db_free_result']($request1);
	}
	$smcFunc['db_free_result']($request);

	if(!empty($updateData) && !empty($updateIds)) {
		$result = $smcFunc['db_query']('', '
			UPDATE {db_prefix}like_count
			SET like_count = CASE id_member '. $updateData .' END
			WHERE id_member IN ({array_int:updateIds})',
			array(
				'updateIds' => $updateIds
			)
		);
	}

	if(!empty($insertData)) {
		$result = $smcFunc['db_insert']('replace',
			'{db_prefix}like_count',
			array('id_member' => 'int', 'like_count' => 'int'),
			$insertData,
			array('id_member')
		);
	}

	$resp = array('totalWork' => (int) $totalWork, 'endLimit' => (int) $endLimit);
	echo json_encode($resp);
	die();
}

?>