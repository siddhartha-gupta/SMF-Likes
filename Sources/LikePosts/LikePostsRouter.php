<?php

/**
* @package manifest file for Like Posts
* @version 2.0
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

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function routeLikePostsAdmin() {
	global $txt, $context;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	LikePosts::loadClass('LikePostsAdmin');
	loadtemplate('LikePostsAdmin');

	$context['page_title'] = $txt['lp_admin_panel'];
	$defaultActionFunc = 'generalSettings';

	// Load tabs menu, text etc for the admin panel
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
		'generalsettings' => 'generalSettings',
		'savegeneralsettings' => 'saveGeneralSettings',
		'permissionsettings' => 'permissionSettings',
		'savepermissionsettings' => 'savePermissionsettings',
		'boardsettings' => 'boardsettings',
		'saveboardsettings' => 'saveBoardsettings',
		'recountlikestats' => 'recountLikeStats',
		'recountlikestotal' => 'recountLikesTotal'
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikePostsAdmin, $subActions[$_REQUEST['sa']]))
		return LikePosts::$LikePostsAdmin->$subActions[$_REQUEST['sa']]();

	// At this point we can just do our default.
	LikePosts::$LikePostsAdmin->$defaultActionFunc();
}

function routeLikePostsProfile($memID) {
	global $context, $txt, $user_info;

	if(isset($_REQUEST['u']) && is_numeric($_REQUEST['u']) && 
		$user_info['id'] !== $_REQUEST['u'] && 
		!LikePosts::$LikePostsUtils->isAllowedTo(array('lp_guest_can_view_likes_in_profiles', 'lp_can_view_others_likes_profile'))) {
				return false;
	}

	LikePosts::loadClass('LikePostsProfile');
	loadtemplate('LikePostsProfile');
	$defaultActionFunc = 'getOwnLikes';

	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => $txt['lp_tab_title'],
		'description' => $txt['lp_tab_description'],
		'icon' => 'profile_sm.gif',
		'tabs' => array(
			'seeownlikes' => array(),
			'seeotherslikes' => array(),
		),
	);

	$subActions = array(
		'seeownlikes' => 'getOwnLikes',
		'seeotherslikes' => 'getOthersLikes',
	);

	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikePostsProfile, $subActions[$_REQUEST['sa']]))
		return LikePosts::$LikePostsProfile->$subActions[$_REQUEST['sa']]($memID);

	// At this point we can just do our default.
	LikePosts::$LikePostsProfile->$defaultActionFunc($memID);
}

class LikePostsRouter {
	public function __construct() {}

	public function routeLikes() {
		LikePosts::loadClass('LikeUnlikePosts');

		$subActions = array(
			'like_post' => 'likeUnlikePostsHandler'
		);

		if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikeUnlikePosts, $subActions[$_REQUEST['sa']]))
			return LikePosts::$LikeUnlikePosts->$subActions[$_REQUEST['sa']]();
	}

	public function routeLikesData() {
		LikePosts::loadClass('LikePostsData');

		$subActions = array(
			'get_message_like_info' => 'getMessageLikeInfo',
			'like_posts_notification'=> 'getAllNotification'
		);

		if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikePostsData, $subActions[$_REQUEST['sa']]))
			return LikePosts::$LikePostsData->$subActions[$_REQUEST['sa']]();
	}

	public function routeLikeStats() {
		global $context, $txt;

		LikePosts::loadClass('LikePostsStats');
		LikePosts::$LikePostsStats->checkStatsPermission();

		loadtemplate('LikePostsStats');
		$context['page_title'] = $txt['lp_stats'];
		$context['like_posts']['tab_desc'] = $txt['like_posts_stats_desc'];

		// Load up the guns
		$context['lp_stats_tabs'] = array(
			'messagestats' => array(
				'label' => $txt['lp_message'],
				'id' => 'messagestats',
			),
			'topicstats' => array(
				'label' => $txt['lp_topic'],
				'id' => 'topicstats',
			),
			'boardstats' => array(
				'label' => $txt['lp_board'],
				'id' => 'boardstats',
			),
			'usergivenstats' => array(
				'label' => $txt['lp_tab_mlmember'],
				'id' => 'mostlikesreceiveduserstats',
			),
			'userreceivedstats' => array(
				'label' => $txt['lp_tab_mlgmember'],
				'id' => 'mostlikesgivenuserstats',
			),
		);
		$context['sub_template'] = 'lp_stats';

		if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikePostsData, $subActions[$_REQUEST['sa']]))
			return LikePosts::$LikePostsData->$subActions[$_REQUEST['sa']]();
	}

	public function routeLikeStatsAjax() {
		global $context;

		LikePosts::loadClass('LikePostsStats');
		LikePosts::$LikePostsStats->checkStatsPermission();

		if(empty($context['like_post_stats_error'])) {
			$defaultActionFunc = 'messageStats';

			$subActions = array(
				'messagestats' => 'messageStats',
				'topicstats' => 'topicStats',
				'boardstats' => 'boardStats',
				'mostlikesreceiveduserstats' => 'mostLikesReceivedUserStats',
				'mostlikesgivenuserstats' => 'mostLikesGivenUserStats',
			);

			//wakey wakey, call the func you lazy
			if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikePostsStats, $subActions[$_REQUEST['sa']]))
				return LikePosts::$LikePostsStats->$subActions[$_REQUEST['sa']]();

			// At this point we can just do our default.
			LikePosts::$LikePostsStats->$defaultActionFunc();
		}
	}
}

?>
