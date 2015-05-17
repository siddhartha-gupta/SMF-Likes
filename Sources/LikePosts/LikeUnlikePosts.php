<?php

/**
* @package manifest file for Like Posts
* @version 2.0.5
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

class LikeUnlikePosts {
	public function __construct() {
	}

	public function likeUnlikePostsHandler() {
		global $user_info, $txt;

		// Check and send them back if found guilty :D
		if (!(LikePosts::$LikePostsUtils->isAllowedTo(array('lp_can_like_posts')))) {
			$resp = array('response' => false, 'error' => $txt['lp_cannot_like_posts']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}

		// Lets get and sanitize the data first
		$msg_id = isset($_REQUEST['msg']) && !empty($_REQUEST['msg']) ? (int) ($_REQUEST['msg']) : 0;
		$author_id = isset($_REQUEST['author']) ? (int) ($_REQUEST['author']) : 0;
		$rating = isset($_REQUEST['rating']) ? (int) ($_REQUEST['rating']) : 0;

		// Woops! check out if they missed something
		if (empty($msg_id) || empty($author_id)) {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}

		// All good lets proceed
		$data = array(
			'id_msg' => $msg_id,
			'id_member_gave' => $user_info['id'],
			'id_member_received' => $author_id,
			'rating' => $rating,
		);

		if(empty($rating)) {
			$result = LikePosts::$LikePostsDB->deleteLikePost($data);
		} else {
			$result = LikePosts::$LikePostsDB->insertLikePost($data);
		}

		if ($result) {
			$this->sendResponse(array(
				'msg_id' => $msg_id,
				'rating' => $rating,
			));
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	private function sendResponse($data) {
		global $txt;

		$count = LikePosts::$LikePostsDB->getLikeTopicCount($data['msg_id']);

		$remaining_likes = (int) ($count - 1);
		if(!empty($data['rating'])) {
			$new_text = $txt['lp_unlike'];
			if ($remaining_likes > 0) {
				if($remaining_likes > 1) {
					$liked_text = $txt['lp_string_you'] . ' ' . sprintf($txt['lp_string_other_multiple_people_liked'], $remaining_likes);
				} else {
					$liked_text = $txt['lp_string_you'] . ' ' . sprintf($txt['lp_string_other_people_liked'], $remaining_likes);
				}
			} else {
				$liked_text = $txt['lp_string_you_liked'];
			}
		} else {
			$new_text = $txt['lp_like'];
			$liked_text = '';
			if(!empty($count)) {
				if($count > 1) {
					$liked_text = sprintf($txt['lp_string_multiple_people_liked'], $count);
				} else {
					$liked_text = sprintf($txt['lp_string_people_liked'], $count);
				}
			}
		}

		$resp = array('response' => true, 'newText' => $new_text, 'count' => $count, 'likeText' => $liked_text);
		return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
	}
}

?>
