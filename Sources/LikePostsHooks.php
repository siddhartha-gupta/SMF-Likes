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
	global $txt;

	loadLanguage('LikePosts');
	loadtemplate('LikePosts');

	$profile_areas['info']['areas']['likeposts'] = array(
		'label' => $txt['like_post_menu'],
		'file' => 'LikePostsProfile.php',
		'function' => 'LP_showLikeProfile',
		'subsections' => array(
			'seeownlikes' => array($txt['like_post_you_liked']),
			'seeotherslikes' => array($txt['like_post_liked_by_others']),
		),
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);
}

function LP_addAction(&$actionArray) {
	$actionArray['likeposts'] = array('LikePosts.php', 'LP_mainIndex');
}

?>