<?php

/**
* @package manifest file for Like Posts
* @version 2.0.4
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
 

if (!defined('SMF'))
	die('Hacking attempt...');

class LikePostsAdmin {
	private $dbInstance;

	public function __construct() {
		$this->dbInstance = new LikePostsAdminDB();
	}

	public function generalSettings($return_config = false) {
		global $txt, $context, $sourcedir;

		require_once($sourcedir . '/ManageServer.php');
		$general_settings = array(
			array('check', 'lp_mod_enable', 'subtext' => $txt['lp_mod_enable_desc']),
			array('check', 'lp_stats_enable', 'subtext' => $txt['lp_stats_enable_desc']),
			array('check', 'lp_notification_enable', 'subtext' => $txt['lp_notification_enable_desc']),
			array('text', 'lp_per_profile_page', 'subtext' => $txt['lp_per_profile_page_desc']),
			array('text', 'lp_in_notification', 'subtext' => $txt['lp_in_notification_desc']),
			array('check', 'lp_show_like_on_boards', 'subtext' => $txt['lp_show_like_on_boards_desc']),
			array('check', 'lp_show_total_like_in_posts', 'subtext' => $txt['lp_show_total_like_in_posts_desc']),
		);

		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_general_settings';
		$context['like_posts']['tab_name'] = $txt['lp_general_settings'];
		$context['like_posts']['tab_desc'] = $txt['lp_general_settings_desc'];
		prepareDBSettingContext($general_settings);
	}

	public function saveGeneralSettings() {
		global $sourcedir;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		checkSession('request', '', true);

		$general_settings = array(
			array('check', 'lp_mod_enable'),
			array('check', 'lp_stats_enable'),
			array('check', 'lp_notification_enable'),
			array('text', 'lp_per_profile_page'),
			array('text', 'lp_in_notification'),
			array('check', 'lp_show_like_on_boards'),
			array('check', 'lp_show_total_like_in_posts'),
		);

		require_once($sourcedir . '/ManageServer.php');
		saveDBSettings($general_settings);
		redirectexit('action=admin;area=likeposts;sa=generalsettings');
	}

	public function permissionSettings() {
		global $txt, $context, $sourcedir;

		require_once($sourcedir . '/ManageServer.php');
		require_once($sourcedir . '/Subs-Membergroups.php');

		// set up the vars for groups and guests permissions
		$context['like_posts']['groups_permission_settings'] = array(
			'lp_can_like_posts',
			'lp_can_view_likes',
			'lp_can_view_others_likes_profile',
			'lp_can_view_likes_stats',
			'lp_can_view_likes_notification'
		);

		$context['like_posts']['guest_permission_settings'] = array(
			'lp_guest_can_view_likes_in_posts',
			'lp_guest_can_view_likes_in_boards',
			'lp_guest_can_view_likes_in_profiles',
			'lp_guests_can_view_likes_stats'
		);

		$context['like_posts']['groups'][0] = array(
			'id_group' => 0,
			'group_name' => $txt['lp_regular_members'],
		);
		$context['like_posts']['groups'] += list_getMembergroups(null, null, 'id_group', 'regular');
		unset($context['like_posts']['groups'][3]);
		unset($context['like_posts']['groups'][1]);
		$context['like_posts']['groups'] += list_getMembergroups(null, null, 'id_group', 'post_count');		

		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_permission_settings';
		$context['like_posts']['tab_name'] = $txt['lp_permission_settings'];
		$context['like_posts']['tab_desc'] = $txt['lp_permission_settings_desc'];
	}

	public function savePermissionsettings() {
		global $context;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		checkSession('request', '', true);
		unset($_POST['submit']);

		// set up the vars for groups and guests permissions
		$context['like_posts']['groups_permission_settings'] = array(
			'lp_can_like_posts',
			'lp_can_view_likes',
			'lp_can_view_others_likes_profile',
			'lp_can_view_likes_stats',
			'lp_can_view_likes_notification'
		);

		$context['like_posts']['guest_permission_settings'] = array(
			'lp_guest_can_view_likes_in_posts',
			'lp_guest_can_view_likes_in_boards',
			'lp_guest_can_view_likes_in_profiles',
			'lp_guests_can_view_likes_stats'
		);

		$permissionKeys = array(
			'lp_can_like_posts',
			'lp_can_view_likes',
			'lp_can_view_others_likes_profile',
			'lp_can_view_likes_stats',
			'lp_can_view_likes_notification',
		);

		$guestPermissionKeys = array(
			'lp_guest_can_view_likes_in_posts',
			'lp_guest_can_view_likes_in_boards',
			'lp_guest_can_view_likes_in_profiles',
			'lp_guests_can_view_likes_stats'
		);

		// Array to be saved to DB
		$general_settings = array();
		// Array to be submitted to DB
		foreach($_POST as $key => $val) {
			if(in_array($key, $context['like_posts']['groups_permission_settings'])) {
				// Extract the user permissions first
				if(array_filter($_POST[$key], 'is_numeric') === $_POST[$key]) {
					if(($key1 = array_search($key, $permissionKeys)) !== false) {
						unset($permissionKeys[$key1]);
					}
					$_POST[$key] = implode(',', $_POST[$key]);
					$general_settings[] = array($key, $_POST[$key]);
				}
			} elseif(in_array($key, $context['like_posts']['guest_permission_settings'])) {
				// Extract the guest permissions as well
				if(is_numeric($_POST[$key])) {
					if(($key1 = array_search($key, $guestPermissionKeys)) !== false) {
						unset($guestPermissionKeys[$key1]);
					}
					$general_settings[] = array($key, $_POST[$key]);
				}
			}
		}

		// Remove the keys which were saved previously but removed this time
		if(!empty($permissionKeys)) {
			foreach ($permissionKeys as $value) {
				$general_settings[] = array($value, '');
			}
		}

		if(!empty($guestPermissionKeys)) {
			foreach ($guestPermissionKeys as $value) {
				$general_settings[] = array($value, '');
			}
		}
		$this->dbInstance->updatePermissions($general_settings);
		redirectexit('action=admin;area=likeposts;sa=permissionsettings');
	}

	public function boardsettings() {
		global $txt, $context, $sourcedir, $cat_tree, $boards, $boardList;

		require_once($sourcedir . '/Subs-Boards.php');
		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_board_settings';
		$context['like_posts']['tab_name'] = $txt['lp_board_settings'];
		$context['like_posts']['tab_desc'] = $txt['lp_board_settings_desc'];
		getBoardTree();

		$context['categories'] = array();
		foreach ($cat_tree as $catid => $tree) {
			$context['categories'][$catid] = array(
				'name' => &$tree['node']['name'],
				'id' => &$tree['node']['id'],
				'boards' => array()
			);

			foreach ($boardList[$catid] as $boardid) {
				$context['categories'][$catid]['boards'][$boardid] = array(
					'id' => &$boards[$boardid]['id'],
					'name' => &$boards[$boardid]['name'],
					'child_level' => &$boards[$boardid]['level'],
				);
			}
		}
	}

	public function saveBoardsettings() {
		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		checkSession('request', '', true);

		$general_settings = array();
		$activeBoards = $_POST['active_board'];
		$activeBoards = isset($activeBoards) && !empty($activeBoards) ? implode(',', $activeBoards) : '';
		$general_settings[] = array('lp_active_boards', $activeBoards);

		$this->dbInstance->updatePermissions($general_settings);
		redirectexit('action=admin;area=likeposts;sa=boardsettings');
	}

	public function recountLikeStats() {
		global $txt, $context;

		isAllowedTo('admin_forum');
		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_recount_stats';
		$context['like_posts']['tab_name'] = $txt['lp_recount_stats'];
		$context['like_posts']['tab_desc'] = $txt['lp_recount_stats_desc'];
	}

	public function optimizeLikes() {
		isAllowedTo('admin_forum');

		// Lets fire the bullet.
		@set_time_limit(300);
		$this->dbInstance->optimizeLikes();

		$resp = array('result' => true);
		return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
	}

	public function removeDupLikes() {
		isAllowedTo('admin_forum');

		$this->dbInstance->removeDupLikes();
		$resp = array('result' => true);
		return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
	}

	public function recountLikesTotal() {
		isAllowedTo('admin_forum');

		// Lets fire the bullet.
		@set_time_limit(300);

		$startLimit = (int) $_REQUEST['startLimit'];
		$endLimit = (int) $_REQUEST['endLimit'];
		$totalWork = (int) $_REQUEST['totalWork'];

		// Result carries totalWork to do
		$result = $this->dbInstance->recountLikesTotal($startLimit, $totalWork);

		$resp = array('totalWork' => (int) $result, 'endLimit' => (int) $endLimit);
		return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
	}
}

?>
