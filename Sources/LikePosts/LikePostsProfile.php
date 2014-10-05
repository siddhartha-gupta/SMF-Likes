<?php

/**
 *
 *
 * @package manifest file for Like Posts
 * @version 1.6.1
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

function LP_showLikeProfile($memID) {
	global $context, $txt, $sourcedir, $settings, $user_info;

	require_once($sourcedir . '/LikePosts.php');
	if($user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes_in_profiles'))) return false;

	$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/css/likeposts.css" />';
	$default_action_func = 'LP_getOwnLikes';
	$default_template_func = 'lp_show_own_likes';
	$default_title = $txt['lp_you_liked'];

	// array is defined as follow
	// source func, template func name
	$subActions = array(
		'seeownlikes' => array('LP_getOwnLikes', 'lp_show_own_likes', $txt['lp_you_liked']),
		'seeotherslikes' => array('LP_getOthersLikes', 'lp_show_others_likes', $txt['lp_liked_by_others']),
	);

	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => $txt['lp_tab_title'],
		'description' => $txt['lp_tab_description'],
		'icon' => 'profile_sm.gif',
		'tabs' => array(
			'seeownlikes' => array(),
			'seeotherslikes' => array(),
		),
	);

	$context['like_active_area_func'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']][0]) ? $subActions[$_REQUEST['sa']][0] : $default_action_func;

	$context['like_active_area_temp'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $subActions[$_REQUEST['sa']][1] : $default_template_func;

	$context['like_active_area_title'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $subActions[$_REQUEST['sa']][2] : $default_title;


	$context['sub_template'] = $context['like_active_area_temp'];
	$context['page_title'] = $context['like_active_area_title'];
	$context['like_active_area_func']($memID);
}

class LikePostsProfile {
	public function __construct() {
		loadLanguage('LikePosts');
		loadtemplate('LikePosts');
	}
}

?>
