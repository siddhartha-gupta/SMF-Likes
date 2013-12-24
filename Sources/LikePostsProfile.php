<?php

/**
* @package manifest file for Like Posts
* @version 1.2.1
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

function LP_showLikeProfile($memID) {
	global $context, $txt, $sourcedir, $settings, $user_info;

	require_once($sourcedir . '/LikePosts.php');
	if($user_info['is_guest'])
		return false;

	LP_includeAssets();

	$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/css/likeposts.css" />';
	$default_action_func = 'LP_getOwnLikes';
	$default_template_func = 'lp_show_own_likes';
	$default_title = $txt['like_post_you_liked'];

	// array is defined as follow
	// source func, template func name
	$subActions = array(
		'seeownlikes' => array('LP_getOwnLikes', 'lp_show_own_likes', $txt['like_post_you_liked']),
		'seeotherslikes' => array('LP_getOthersLikes', 'lp_show_others_likes', $txt['like_post_liked_by_others']),
	);

	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => 'See likes',
		'description' => 'see likes given/taken',
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

function LP_getOwnLikes($memID) {
	global $context, $sourcedir, $scripturl, $modSettings;

	require_once($sourcedir . '/Subs-LikePosts.php');
	$select = 'COUNT(*)';
	$where = 'lp.id_member_gave = ' . $memID;
	$context['total_visible_likes'] = isset($_REQUEST['total']) && !empty($_REQUEST['total']) ? (int) $_REQUEST['total'] : LP_DB_getTotalResults($select, $where);

	$context['start'] = isset($_REQUEST['start']) && !empty($_REQUEST['start']) ? $_REQUEST['start']: 0;
	$context['start'] = !is_numeric($context['start']) ? 0 : $context['start'];

	// Give admin options for these
	$context['likes_per_page'] = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

	$context['like_post']['own_like_data'] = LP_DB_getOwnLikes($memID, $context['start']);
	$context['page_index'] = constructPageIndex($scripturl . '?action=profile;area=likeposts;sa=seeownlikes;u=' . $memID .';total=' . $context['total_visible_likes'], $context['start'], $context['total_visible_likes'], $context['likes_per_page']);
}

function LP_getOthersLikes($memID) {
	global $context, $sourcedir, $scripturl, $modSettings;

	require_once($sourcedir . '/Subs-LikePosts.php');
	$select = 'COUNT(DISTINCT(lp.id_msg))';
	$where = 'm.id_member = ' . $memID;
	$context['total_visible_likes'] = isset($_REQUEST['total']) && !empty($_REQUEST['total']) ? (int) $_REQUEST['total'] : LP_DB_getTotalResults($select, $where);

	$context['start'] = isset($_REQUEST['start']) && !empty($_REQUEST['start']) ? $_REQUEST['start']: 0;
	$context['start'] = !is_numeric($context['start']) ? 0 : $context['start'];

	// Give admin options for these
	$context['likes_per_page'] = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

	$context['like_post']['others_like_data'] = LP_DB_getOthersLikes($memID, $context['start']);
	$context['page_index'] = constructPageIndex($scripturl . '?action=profile;area=likeposts;sa=seeotherslikes;u=' . $memID .';total=' . $context['total_visible_likes'], $context['start'], $context['total_visible_likes'], $context['likes_per_page']);
}

?>