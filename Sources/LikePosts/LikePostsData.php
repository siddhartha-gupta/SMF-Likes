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

class LikePostsData {
	public function __construct() {}

	public function getAllTopicsInfo($topicsArr = array(), $boardId = '') {
		global $context, $user_info;

		if($user_info['is_guest'] && !LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes_in_boards'))) {
			return false;
		}

		if (!is_array($topicsArr)) {
			$topicsArr = array($topicsArr);
		}
		$boardId = isset($boardId) && !empty($boardId) ? $boardId : $context['current_board'];

		if (empty($boardId)) {
			return false;
		}
		$result = LikePosts::$LikePostsDB->getAllTopicsInfo($topicsArr, $boardId);
		return $result;
	}

	/*
	 * To get the info of members who liked the post
	 */
	public function getMessageLikeInfo() {
		global $user_info;

		if($user_info['is_guest'] && 
			!LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes_in_posts')) && 
			!LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes_in_boards'))) {
			return false;
		} elseif (!$user_info['is_guest'] && !LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes'))) {
			return false;
		}

		if (!isset($_REQUEST['msg_id']) || empty($_REQUEST['msg_id'])) {
			$resp = array('response' => false);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
		$msg_id = (int) $_REQUEST['msg_id'];
		$result = LikePosts::$LikePostsDB->getMessageLikeInfo($msg_id);

		$resp = array('response' => true, 'data' => $result);
		return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
	}

	public function posterInfo($postersArr = array()) {
		global $user_info;

		if($user_info['is_guest'] && !LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes_in_posts'))) {
			return false;
		}

		if (!is_array($postersArr)) {
			$postersArr = array($postersArr);
		}
		$result = LikePosts::$LikePostsDB->posterInfo($postersArr);
		return $result;
	}

	public function getAllMessagesInfo($msgsArr = array(), $boardId = '', $topicId = '') {
		global $context, $user_info;

		if($user_info['is_guest'] && !LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes_in_posts'))) {
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
		$result = LikePosts::$LikePostsDB->getAllMessagesInfo($msgsArr, $boardId, $topicId);
		return $result;
	}

	public function getAllNotification() {
		global $user_info;

		if(!LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes')) || $user_info['is_guest']) {
			return false;
		}
		$result = LikePosts::$LikePostsDB->getAllNotification();

		$resp = array('response' => true, 'data' => $result);
		return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
	}
}

?>
