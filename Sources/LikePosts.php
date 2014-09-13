<?php

/**
* @package manifest file for Like Posts
* @version 1.5.2
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
 * A generic function to load JS and css related to mod
*/
function LP_includeAssets() {
	global $settings, $context;

	loadlanguage('LikePosts');
	$context['insert_after_template'] .= '
	<script type="text/javascript"><!-- // --><![CDATA[
		var lpLoaded = false,
		inConflict = false;

		function compareJQueryVersion(v1, v2, callback) {
			var v1parts = v1.split('.');
			var v2parts = v2.split('.');

			for (var i = 0; i < v1parts.length; ++i) {
				if (v2parts.length == i) {
					//v1 + " is larger"
					callback(1);
					return;
				}

				if (v1parts[i] == v2parts[i]) {
					continue;
				} else if (v1parts[i] > v2parts[i]) {
					//v1 + " is larger";
					callback(1);
					return;
				} else {
					//v2 + " is larger";
					callback(2);
					return;
				}
			}

			if (v1parts.length != v2parts.length) {
				//v2 + " is larger";
				callback(2);
				return;
			}
			callback(false);
			return;
		}

		function loadJquery(url, callback) {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = url;

			var head = document.getElementsByTagName("head")[0],
				done = false;

			script.onload = script.onreadystatechange = function() {
				if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
					done = true;
					callback();
					script.onload = script.onreadystatechange = null;
					head.removeChild(script);
				};
			};
			head.appendChild(script);
		}

		// Only do anything if jQuery isn"t defined
		if (typeof(jQuery) == "undefined") {
			console.log("jquery not found");
			if (typeof($) == "function") {
				console.log("jquery but in conflict");
				inConflict = true;
			}

			loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
				if (typeof(jQuery) !=="undefined") {
					console.log("directly loaded with version: " + jQuery.fn.jquery);
					lp_jquery2_0_3 = jQuery.noConflict(true);
					loadLPScript();
				}
			});
		} else {
			// jQuery is already loaded
			console.log("jquery is already loaded with version: " + jQuery.fn.jquery);
			compareJQueryVersion(jQuery.fn.jquery, "2.0.3", function(result) {
				console.log("result of version check: " + result)
				switch(result) {
					case false:
					case 1:
						lp_jquery2_0_3 = jQuery.noConflict(true);
						loadLPScript();
						break;

					case 2:
						loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
							if (typeof(jQuery) !=="undefined") {
								console.log("after version check loaded with version: " + jQuery.fn.jquery);
								lp_jquery2_0_3 = jQuery.noConflict(true);
								loadLPScript();
							}
						});
						break;

					default:
						loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
							if (typeof(jQuery) !=="undefined") {
								console.log("default version check loaded with version: " + jQuery.fn.jquery);
								lp_jquery2_0_3 = jQuery.noConflict(true);
								loadLPScript();
							}
						});
						break;
				}
			})
		};

		function loadLPScript() {
			var js = document.createElement("script");
			js.type = "text/javascript";
			js.src = "' . $settings['default_theme_url'] . '/scripts/LikePosts.js";
			document.body.appendChild(js);

			var isLPStatsPage = (window.location.href.indexOf("likepostsstats") >= 0) ? true : false;

			if(isLPStatsPage) {
				var statsJS = document.createElement("script");
				statsJS.type = "text/javascript";
				statsJS.src = "' . $settings['default_theme_url'] . '/scripts/LikePostStats.js";
				document.body.appendChild(statsJS);
			}
		}
	// ]]></script>';

	LP_checkJsonEncode();
}

function LP_checkJsonEncode() {
	if (!function_exists('json_encode')) {
		function json_encode($a = false) {

			switch(gettype($a)) {
				case 'integer':
				case 'double':
					return floatval(str_replace(",", ".", strval($a)));
				break;

				case 'NULL':
				case 'resource':
				case 'unknown':
					return 'null';
				break;

				case 'boolean':
					return $a ? 'true' : 'false' ;
				break;

				case 'array':
				case 'object':
					$output = array();
					$isAssoc = false;

					foreach(array_keys($a) as $key) {
						if (!is_int($key)) {
							$isAssoc = true;
							break;
						}
					}

					if($isAssoc) {
						foreach($a as $k => $val) {
							$output []= json_encode($k) . ':' . json_encode($val);
						}
						$output = '{' . implode(',', $output) . '}';
					} else {
						foreach($a as $val){
							$output []= json_encode($val);
						}
						$output = '[' . implode(',', $output) . ']';
					}
					return $output;
				break;

				default:
				return '"' . addslashes($a) . '"';
			}
		}
	}
}
/*
 * Our main function which decides which sub-function will be utilized
*/
function LP_mainIndex() {

	// No globals :D
	$subActions = array(
		// Main views.
		'like_post' => 'LP_likePosts',
		'get_message_like_info' => 'LP_getMessageLikeInfo',
		'get_all_messages_info' => 'LP_getAllMessagesInfo',
		'get_all_topics_info' => 'LP_getAllTopicsInfo',
		'like_posts_notification'=> 'LP_getAllNotification',
	);

	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$_REQUEST['sa']]();
}

/*
 * Our darling thinggy to like/dislike posts
*/
function LP_likePosts() {
	global $user_info, $sourcedir, $txt, $settings;

	loadlanguage('LikePosts');
	if ($user_info['is_guest'] || !(LP_isAllowedTo(array('can_like_posts')))) {
		$resp = array('response' => false, 'error' => $txt['lp_cannot_like_posts']);
		echo json_encode($resp);
		die();
	}

	// Lets get and sanitize the data first
	$board_id = isset($_REQUEST['board']) && !empty($_REQUEST['board']) ? (int) ($_REQUEST['board']) : 0;
	$topic_id = isset($_REQUEST['topic']) && !empty($_REQUEST['topic']) ? (int) ($_REQUEST['topic']) : 0;
	$msg_id = isset($_REQUEST['msg']) && !empty($_REQUEST['msg']) ? (int) ($_REQUEST['msg']) : 0;
	$author_id = isset($_REQUEST['author']) ? (int) ($_REQUEST['author']) : 0;
	$rating = isset($_REQUEST['rating']) ? (int) ($_REQUEST['rating']) : 0;

	if (empty($board_id) || empty($topic_id) || empty($msg_id) || empty($author_id)) {
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
		'id_member_gave' => $user_info['id'],
		'id_member_received' => $author_id,
		'rating' => $rating,
	);

	if(empty($rating)) {
		$result = LP_DB_deleteLikePost($data);
	} else {
		$result = LP_DB_insertLikePost($data);
	}

	if ($result) {
		$count = LP_DB_getLikeTopicCount($board_id, $topic_id, $msg_id);
		$new_text = !empty($rating) ? $txt['lp_unlike'] : $txt['lp_like'];

		$remaining_likes = (int) ($count - 1);
		if(!empty($rating)) {
			if ($remaining_likes > 0)
				$liked_text = sprintf($txt['lp_string_you_and_liked'], $remaining_likes);
			else
				$liked_text = $txt['lp_string_you_liked'];
		} else {
			$liked_text = !empty($count) ? sprintf($txt['lp_string_people_liked'], $count) : '';
		}

		$resp = array('response' => true, 'newText' => $new_text, 'count' => $count, 'likeText' => $liked_text);
		echo json_encode($resp);
		die();
	} else {
		$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
		echo json_encode($resp);
		die();
	}
}

/*
 * To get like like data for all messages of a topic
*/
function LP_getAllMessagesInfo($msgsArr = array(), $boardId = '', $topicId = '') {
	global $context, $sourcedir, $user_info;

	if($user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes_in_posts'))) {
		return false;
	}

	if (!is_array($msgsArr)) {
		$msgsArr = array($msgsArr);
	}

	$boardId = isset($boardId) && !empty($boardId) ? $boardId : $context['current_board'];
	$topicId = isset($topicId) && !empty($topicId) ? $topicId : $context['current_topic'];

	if (empty($boardId) || empty($topicId)) {
		return false;
	}
	require_once($sourcedir . '/Subs-LikePosts.php');
	$result = LP_DB_getAllMessagesInfo($msgsArr, $boardId, $topicId);
	return $result;
}


function LP_posterInfo($postersArr = array()) {
	global $context, $sourcedir, $user_info;

	if($user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes_in_posts'))) {
		return false;
	}

	if (!is_array($postersArr)) {
		$postersArr = array($postersArr);
	}

	require_once($sourcedir . '/Subs-LikePosts.php');
	loadlanguage('LikePosts');
	$result = LP_DB_posterInfo($postersArr);
	return $result;
}
/*
 * To check whether a specific message is liked or not
 * Used in Display template
*/
function LP_isPostLiked($arr, $id) {
	global $context, $txt, $user_info;

	loadlanguage('LikePosts');

	$data = array(
		'text' => $txt['lp_like'],
		'count' => 0,
		'members' => array(),
		'already_liked' => 1,
	);

	if (!is_array($arr) || empty($arr) || empty($id))
		return $data;

	if (array_key_exists($id, $arr)) {
		$data = array(
			'members' => $arr[$id]['members'],
			'count' => $arr[$id]['count'],
		);

		if (array_key_exists($user_info['id'], $arr[$id]['members'])) {
			$data['text'] = $txt['lp_unlike'];

			$remaining_likes = (int) ($data['count'] - 1);
			if ($remaining_likes > 0)
				$data['count_text'] = sprintf($txt['lp_string_you_and_liked'], $remaining_likes);
			else
				$data['count_text'] = $txt['lp_string_you_liked'];

			//If already liked make it to unlink
			$data['already_liked'] = 0;
		} else {
			$data['text'] = $txt['lp_like'];
			$data['count_text'] = sprintf($txt['lp_string_people_liked'], $data['count']);

			//Give them the option to like
			$data['already_liked'] = 1;
		}
	}
	return $data;
}

/*
 * To get the info of members who liked the post
 */
function LP_getMessageLikeInfo() {
	global $sourcedir, $user_info;

	if($user_info['is_guest'] && (!LP_isAllowedTo(array('can_view_likes_in_posts')) && !LP_isAllowedTo(array('can_view_likes_in_boards')))) {
		return false;
	} elseif (!$user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes'))) {
		return false;
	}

	if (!isset($_REQUEST['msg_id']) || empty($_REQUEST['msg_id'])) {
		$resp = array('response' => false);
	}
	$msg_id = (int) $_REQUEST['msg_id'];
	require_once($sourcedir . '/Subs-LikePosts.php');

	$result = LP_DB_getMessageLikeInfo($msg_id);
	$resp = array('response' => true, 'data' => $result);

	echo json_encode($resp);
	die();
}

/*
 * Get all like info concered to topics
 * Used on message index
*/
function LP_getAllTopicsInfo($topicsArr = array(), $boardId = '') {
	global $context, $sourcedir, $user_info;

	if($user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes_in_boards'))) {
		return false;
	}

	if (!is_array($topicsArr)) {
		$topicsArr = array($topicsArr);
	}
	$boardId = isset($boardId) && !empty($boardId) ? $boardId : $context['current_board'];

	if (empty($boardId)) {
		return false;
	}
	require_once($sourcedir . '/Subs-LikePosts.php');
	$result = LP_DB_getAllTopicsInfo($topicsArr, $boardId);
	return $result;
}

/*
 * To check whether a specific topic is liked or not
 * Used in MessageIndex template
*/
function LP_isTopicLiked($arr, $id) {
	global $context, $txt, $user_info;

	loadlanguage('LikePosts');

	$data = array(
		'text' => $txt['lp_like'],
		'count' => 0,
		'members' => array(),
		'already_liked' => 1,
	);

	if (!is_array($arr) || empty($arr) || empty($id))
		return $data;

	if (array_key_exists($id, $arr)) {
		$data = array(
			'members' => $arr[$id]['members'],
			'count' => $arr[$id]['count'],
		);

		if (array_key_exists($user_info['id'], $arr[$id]['members'])) {
			$data['text'] = $txt['lp_unlike'];

			$remaining_likes = (int) ($data['count'] - 1);
			if ($remaining_likes > 0)
				$data['count_text'] = sprintf($txt['lp_string_you_and_liked'], $remaining_likes);
			else
				$data['count_text'] = $txt['lp_string_you_liked'];

			//If already liked make it to unlink
			$data['already_liked'] = 0;
		} else {
			$data['text'] = $txt['lp_like'];
			$data['count_text'] = sprintf($txt['lp_string_people_liked'], $data['count']);
			//Give them the option to like
			$data['already_liked'] = 1;
		}
	}
	return $data;
}

/* global function for like post to check for permissions
 * just send the permission name to it
*/
function LP_isAllowedTo($permissions) {
	global $modSettings, $user_info;

	if($user_info['is_admin']) return true;

	if (!is_array($permissions))
		$permissions = array($permissions);

	$result = true;

	$guestPermission = array(
		'can_view_likes_in_posts',
		'can_view_likes_in_boards',
		'can_view_likes_in_profiles'
	);

	if($user_info['is_guest']) {
		$result = false;
		$permToCheck = array_intersect($guestPermission, $permissions);
		foreach($permToCheck as $permission) {
			if(in_array($permission, $guestPermission) && isset($modSettings[$permission]) && !empty($modSettings[$permission])) {
				$result = true;
			} else {
				$result = false;
			}
		}
	} else {
		$permToCheck = array_diff($permissions, $guestPermission);
		foreach($permToCheck as $permission) {
			if(!isset($modSettings[$permission]) || strlen($modSettings[$permission]) === 0) {
				$result = false;
			} else {
				$allowedGroups = explode(',', $modSettings[$permission]);
				$groupsPassed = array_intersect($allowedGroups, $user_info['groups']);

				if(empty($groupsPassed)) {
					$result = false;
					break;
				}
			}
		}
	}
	return $result;
}

function LP_getAllNotification() {
	global $sourcedir, $user_info;

	if(!(LP_isAllowedTo(array('can_view_likes'))) || $user_info['is_guest']) {
		return false;
	}
	require_once($sourcedir . '/Subs-LikePosts.php');

	$result = LP_DB_getAllNotification();
	$resp = array('response' => true, 'data' => $result);

	echo json_encode($resp);
	die();
}

function LP_trimContent($str, $delimiter, $limit = 255) {
	if (strlen($str) > $limit) {
		if(strpos($str, $delimiter) !== false) {
			$msgString = substr($str, 0, $limit - 1);
			$temp_post = strpos($str, $delimiter, $limit - 1);
			$msgString .= substr($str, $limit, $temp_post);
			return $msgString;
		}
		return $str;
	}
	return $str;
}

?>
