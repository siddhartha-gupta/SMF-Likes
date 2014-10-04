// <?php

// /**
//  * @package manifest file for Like Posts
//  * @version 1.6.1
//  * @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
//  * @copyright Copyright (c) 2014, Siddhartha Gupta
//  * @license http://www.mozilla.org/MPL/MPL-1.1.html
//  */


//  * Version: MPL 1.1
//  *
//  * The contents of this file are subject to the Mozilla Public License Version
//  * 1.1 (the "License"); you may not use this file except in compliance with
//  * the License. You may obtain a copy of the License at
//  * http://www.mozilla.org/MPL/
//  *
//  * Software distributed under the License is distributed on an "AS IS" basis,
//  * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
//  * for the specific language governing rights and limitations under the
//  * License.
//  *
//  * The Initial Developer of the Original Code is
//  *  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
//  * Portions created by the Initial Developer are Copyright (C) 2012
//  * the Initial Developer. All Rights Reserved.
//  *
//  * Contributor(s): Big thanks to all contributor(s)
//  * emanuele45 (https://github.com/emanuele45)
//  *
 

if (!defined('SMF'))
	die('Hacking attempt...');

function LikePostsAdminIndex($return_config = false) {
	global $txt, $context;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	$LikePostsAdmin = LikePostsAdmin::getInstance();

	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$context['page_title'] = $txt['lp_admin_panel'];
	$default_action_func = 'LP_generalSettings';

	$context['like_posts']['permission_settings'] = array(
		'can_like_posts',
		'can_view_likes',
		'can_view_others_likes_profile',
		'can_view_likes_stats'
	);

	$context['like_posts']['guest_permission_settings'] = array(
		'can_view_likes_in_posts',
		'can_view_likes_in_boards',
		'can_view_likes_in_profiles',
		'guests_can_view_likes_stats'
	);

	// Load up the guns
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['lp_admin_panel'],
		'tabs' => array(
			'generalsettings' => array(
				'label' => $txt['lp_general_settings'],
				'url' => 'generalsettings',
			),
			'permissions' => array(
				'label' => $txt['lp_permission_settings'],
				'url' => 'permissionsettings',
			),
			'board_settings' => array(
				'label' => $txt['lp_board_settings'],
				'url' => 'boardsettings',
			),
			'recountstats' => array(
				'label' => $txt['lp_recount_stats'],
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
		'boardsettings' => 'LP_boardsettings',
		'saveboardsettings' => 'LP_saveBoardsettings',
		'recountlikestats' => 'LP_recountLikeStats',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']])) {
		return $LikePostsAdmin->$subActions[$_REQUEST['sa']]();
	}

	// At this point we can just do our default.
	$LikePostsAdmin->$default_action_func();
}

/*
 *default/basic function
 */

class LikePostsAdmin {
	protected static $instance;
	private $LikePostsUtilsInstance;

	/**
	 * Singleton method
	 *
	 * @return void
	 */
	public static function getInstance() {
		if (null === $instance) {
			$instance = new static ();
		}
		return $instance;
	}

	public function __construct() {}


	public function LP_generalSettings($return_config = false) {
		global $txt, $context, $sourcedir;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		require_once($sourcedir . '/ManageServer.php');

		$general_settings = array(
			array('check', 'like_post_enable', 'subtext' => $txt['lp_enable_desc']),
			array('text', 'like_per_profile_page', 'subtext' => $txt['like_per_profile_page_desc']),
			array('text', 'like_in_notification', 'subtext' => $txt['like_in_notification_desc']),
			array('check', 'lp_show_like_on_boards', 'subtext' => $txt['lp_show_like_on_boards_desc']),
		);

		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_general_settings';
		$context['like_posts']['tab_name'] = $txt['lp_general_settings'];
		$context['like_posts']['tab_desc'] = $txt['lp_general_settings_desc'];
		prepareDBSettingContext($general_settings);
	}

	public function LP_saveGeneralSettings() {
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

	public function LP_permissionSettings() {
		global $txt, $context, $sourcedir;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		require_once($sourcedir . '/ManageServer.php');

		require_once($sourcedir . '/Subs-Membergroups.php');
		$context['like_posts']['groups'][0] = array(
			'id_group' => 0,
			'group_name' => $txt['lp_regular_members'],
		);
		$context['like_posts']['groups'] += list_getMembergroups(null, null, 'id_group', 'regular');
		unset($context['like_posts']['groups'][3]);
		unset($context['like_posts']['groups'][1]);

		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_permission_settings';
		$context['like_posts']['tab_name'] = $txt['lp_permission_settings'];
		$context['like_posts']['tab_desc'] = $txt['lp_permission_settings_desc'];
	}

	public function LP_savePermissionsettings() {
		global $context, $sourcedir;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');

		if (isset($_POST['submit'])) {
			checkSession();
			unset($_POST['submit']);

			$permissionKeys = array(
				'can_like_posts',
				'can_view_likes',
				'can_view_others_likes_profile',
				'can_view_likes_stats'
			);

			$guestPermissionKeys = array(
				'can_view_likes_in_posts',
				'can_view_likes_in_boards',
				'can_view_likes_in_profiles',
				'guests_can_view_likes_stats'
			);

			// Array to be saved to DB
			$general_settings = array();
			// Array to be submitted to DB
			foreach($_POST as $key => $val) {
				if(in_array($key, $context['like_posts']['permission_settings'])) {
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
			require_once($sourcedir . '/Subs-LikePosts.php');
			LP_DB_updatePermissions($general_settings);
			redirectexit('action=admin;area=likeposts;sa=permissionsettings');
		}
	}

	public function LP_boardsettings() {
		global $txt, $context, $sourcedir, $cat_tree, $boards, $boardList;

		isAllowedTo('admin_forum');
		require_once($sourcedir . '/Subs-Boards.php');

		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_board_settings';
		$context['like_posts']['tab_name'] = $txt['lp_board_settings'];
		$context['like_posts']['tab_desc'] = $txt['lp_board_settings_desc'];
		getBoardTree();

		$context['categories'] = array();
		foreach ($cat_tree as $catid => $tree)
		{
			$context['categories'][$catid] = array(
				'name' => &$tree['node']['name'],
				'id' => &$tree['node']['id'],
				'boards' => array()
			);

			foreach ($boardList[$catid] as $boardid)
			{
				$context['categories'][$catid]['boards'][$boardid] = array(
					'id' => &$boards[$boardid]['id'],
					'name' => &$boards[$boardid]['name'],
					'child_level' => &$boards[$boardid]['level'],
				);
			}
		}
	}

	public function LP_saveBoardsettings() {
		global $sourcedir;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');

		if (isset($_POST['submit'])) {
			checkSession();

			$activeBoards = $_POST['active_board'];
			$activeBoards = isset($activeBoards) && !empty($activeBoards) ? implode(',', $activeBoards) : '';
			$general_settings[] = array('lp_active_boards', $activeBoards);

			require_once($sourcedir . '/Subs-LikePosts.php');
			LP_DB_updatePermissions($general_settings);
			redirectexit('action=admin;area=likeposts;sa=boardsettings');
		}
	}

	function LP_recountLikeStats() {
		global $txt, $context, $sourcedir, $settings;

		/* I can has Adminz? */
		isAllowedTo('admin_forum');
		require_once($sourcedir . '/Subs-LikePosts.php');
		require_once($sourcedir . '/LikePosts.php');

		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/css/likeposts.css" />';
		$context['page_title'] = $txt['lp_admin_panel'];
		$context['sub_template'] = 'lp_admin_recount_stats';
		$context['like_posts']['tab_name'] = $txt['lp_recount_stats'];
		$context['like_posts']['tab_desc'] = $txt['lp_recount_stats_desc'];

		$subActions = array(
			'totallikes' => 'LP_recountLikesTotal',
		);

		//wakey wakey, call the func you lazy
		if (isset($_REQUEST['activity']) && isset($subActions[$_REQUEST['activity']]) && function_exists($subActions[$_REQUEST['activity']]))
			return $subActions[$_REQUEST['activity']]();
	}

	public function LP_recountLikesTotal() {
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
}

?>
