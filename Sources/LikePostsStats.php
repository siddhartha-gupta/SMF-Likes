<?php

/**
* @package manifest file for Like Posts
* @version 1.4
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

function LP_statsMainIndex() {
	global $context, $txt, $sourcedir;

	loadtemplate('LikePostsStats');

	$context['page_title'] = 'Like posts stats';

	$context['like_posts']['tab_desc'] = $txt['like_posts_stats_desc'];
	$context['like_posts']['tab_name'] = $txt['like_post_message_stats'];

	// Load up the guns
	$context['lp_stats_tabs'] = array(
		'messagestats' => array(
			'label' => 'Message',
			'url' => 'messagestats',
		),
		'topicsstats' => array(
			'label' => 'Topic',
			'url' => 'topicsstats',
		),
		'boardsstats' => array(
			'label' => 'boards',
			'url' => 'boardsstats',
		),
		'userstats' => array(
			'label' => 'User',
			'url' => 'userstats',
		),
	);
	// active state is handled by JS
	// $context['lp_stats_tabs_active_button'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'messagestats';

	$context['sub_template'] = 'lp_stats';
}

function LP_statsAjax() {
	$default_action_func = 'LP_messageStats';
	$subActions = array(
		'messagestats' => 'LP_messageStats',
		'topicsstats' => 'LP_topicsStats',
		'boardsstats' => 'LP_boardsStats',
		'userstats' => 'LP_userStats',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$_REQUEST['sa']]();

	// At this point we can just do our default.
	$default_action_func();
}

function LP_messageStats () {
	global $context, $txt, $sourcedir, $settings, $user_info;

	require_once($sourcedir . '/Subs-LikePosts.php');
	$data = LP_DB_getStatsMostLikedMessage();
	$resp = array('response' => true, 'data' => $data);
	echo json_encode($resp);
	die();
}

function LP_topicStats() {
	global $context, $txt, $sourcedir, $settings, $user_info;

	$context['like_stats_most_liked_topic'] = LP_DB_getStatsMostLikedTopic();
}

function LP_boardsStats() {
	global $context, $txt, $sourcedir, $settings, $user_info;

	$context['like_stats_most_liked_board'] = LP_DB_getStatsMostLikedBoard();
}

function LP_userStats() {
	global $context, $txt, $sourcedir, $settings, $user_info;

	echo 'nothing to show here';
}

?>
