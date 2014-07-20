<?php

/**
* @package manifest file for Like Posts
* @version 1.5.1
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

function LP_addAdminPanel(&$admin_areas) {
	global $txt;

	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$admin_areas['config']['areas']['likeposts'] = array(
		'label' => $txt['like_post_menu'],
		'file' => 'LikePostsAdmin.php',
		'function' => 'LP_modifySettings',
		'icon' => 'administration.gif',
		'subsections' => array(),
	);
}

function LP_addProfilePanel(&$profile_areas) {
	global $txt, $user_info, $modSettings;

	if($user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes_in_profiles'))) return false;

	if(isset($_REQUEST['u']) && is_numeric($_REQUEST['u'])) {
		if($user_info['id'] !== $_REQUEST['u']) {
			if (!(LP_isAllowedTo(array('can_view_others_likes_profile', 'can_view_likes_in_profiles'))))
				return false;
		}
	}
	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$profile_areas['info']['areas']['likeposts'] = array(
		'label' => $txt['like_post_menu'],
		'file' => 'LikePostsProfile.php',
		'function' => 'LP_showLikeProfile',
		'subsections' => array(
			'seeownlikes' => array($txt['like_post_you_liked'], array('profile_view_own', 'profile_view_any')),
			'seeotherslikes' => array($txt['like_post_liked_by_others'], array('profile_view_own', 'profile_view_any')),
		),
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);
}

function LP_addAction(&$actionArray) {
	$actionArray['likeposts'] = array('LikePosts.php', 'LP_mainIndex');
	$actionArray['likepostsstats'] = array('LikePostsStats.php', 'LP_statsMainIndex');
}

function LP_addMenu(&$menu_buttons) {
	global $scripturl, $txt, $user_info, $modSettings;

	$isAllowedToAccess = true;

	if(!isset($modSettings['like_post_enable']) || empty($modSettings['like_post_enable'])) {
		$isAllowedToAccess = false;
	}

	if($user_info['is_guest'] && !LP_isAllowedTo(array('guests_can_view_likes_stats'))) {
		$isAllowedToAccess = false;
	}
	if(!LP_isAllowedTo(array('can_view_likes_stats'))) {
		$isAllowedToAccess = false;
	}

	if($isAllowedToAccess) {
		// insert before logout
		$initPos = 0;
		reset($menu_buttons);
		while((list($key, $val) = each($menu_buttons)) && $key != 'logout')
			$initPos++;

		$menu_buttons = array_merge(
			array_slice($menu_buttons, 0, $initPos),
			array(
				'like_post_stats' => array(
					'title' => $txt['like_post_stats'],
					'href' => $scripturl . '?action=likepostsstats',
					'show' => true,
				),
			),
			array_slice($menu_buttons, $initPos, count($menu_buttons) - $initPos)
		);
	}
}

?>
