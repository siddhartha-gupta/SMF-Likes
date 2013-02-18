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

/*
 *This function is accessible using ?action=likeposts
 */

function LP_includeJSFiles() {
	global $settings;

	echo '
	<script>
		if (!window.jQuery) {
			var head= document.getElementsByTagName("head")[0];
			var script= document.createElement("script");
			script.type= "text/javascript";
			script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js";
			head.appendChild(script);
		}
	</script>';

	echo '
	<script>
		if (!window.lpObj) {
			var head= document.getElementsByTagName("head")[0];
			var script= document.createElement("script");
			script.type= "text/javascript";
			script.src = "', $settings['default_theme_url'], '/scripts/likePosts.js";
			head.appendChild(script);
		}
	</script>';
}

function LP_isPostLiked($arr, $id) {
	if (!is_array($arr) || empty($arr) || empty($id))
		return false;

	if (array_key_exists($id, $arr)) {
		if (!empty($arr[$id]['rating']))
		   return true;
		else
			return false;
	}
	return false;
}

function LP_mainIndex() {
	global $context, $txt, $scripturl, $settings;

	ob_start();
	LP_includeJSFiles();
	ob_end_clean();
	$default_action_func = 'LP_defaultFunc';
	$subActions = array(
		// Main views.
		'like_post' => 'LP_likePosts',
		'unlike_post' => 'LP_unlikePosts',
		'get_posts_info' => 'LP_getPostsInfo',
		'get_topics_info' => 'LP_getTopicsInfo',
		'get_boards_info' => 'LP_getBoardsInfo',
	);

	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$_REQUEST['sa']]();

	// At this point we can just do our default.
	$default_action_func();
}

function LP_defaultFunc() {
	global $context, $txt, $scripturl;

	echo 'we are in default func';
}

function LP_likePosts() {
	global $user_info, $sourcedir, $txt;

	loadlanguage('LikePosts');
	if ($user_info['is_guest']) {
		$resp = array('response' => false, 'error' => $txt['lp_error_cannot_like_posts']);
		echo json_encode($resp);
		die();
	}

	// Lets get and sanitize the data first
	$board_id = isset($_REQUEST['board']) && !empty($_REQUEST['board']) ? (int) ($_REQUEST['board']) : 0;
	$topic_id = isset($_REQUEST['topic']) && !empty($_REQUEST['topic']) ? (int) ($_REQUEST['topic']) : 0;
	$msg_id = isset($_REQUEST['msg']) && !empty($_REQUEST['msg']) ? (int) ($_REQUEST['msg']) : 0;
	$rating = isset($_REQUEST['rating']) ? (int) ($_REQUEST['rating']) : 0;

	if (empty($board_id) || empty($topic_id) || empty($msg_id)) {
		$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
		echo json_encode($resp);
		die();
	}

	//  All good lets proceed
	require_once($sourcedir . '/Subs-LikePosts.php');
	$data = array(
		'id_msg' => $msg_id,
		'id_topic' => $topic_id,
		'id_board' => $board_id,
		'id_member' => $user_info['id'],
		'rating' => $rating,
	);

	$result = LP_DB_insertLikePost($data);
	if ($result) {
		$resp = array('response' => true, 'msg' => $txt['lp_success']);
		echo json_encode($resp);
		die();
	} else {
		$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
		echo json_encode($resp);
		die();
	}
}

/*
 *this function is not utilized yet
 */
function LP_getPostsInfo($topicIds = array()) {
	global $context, $sourcedir;

	if (!is_array($topicIds)) {
		return false;
	}
	require_once($sourcedir . '/Subs-LikePosts.php');
	LP_DB_getLikePostsInfo();
}

/*
 *To get like posts data for topics, e.g. http://localhost/smf2/index.php?topic=14.0
 */
function LP_getTopicsInfo($msgsArr = array(), $boardId = '', $topicId = '') {
	global $context, $sourcedir;

	if (!is_array($msgsArr)) {
		$msgsArr = array($msgsArr);
	}
	$boardId = isset($boardId) && !empty($boardId) ? $boardId : $context['current_board'];
	$topicId = isset($topicId) && !empty($topicId) ? $topicId : $context['current_topic'];

	if (empty($boardId) || empty($topicId)) {
		return false;
	}
	require_once($sourcedir . '/Subs-LikePosts.php');
	$result = LP_DB_getLikeTopicsInfo($msgsArr, $boardId, $topicId);
	return $result;
}

?>