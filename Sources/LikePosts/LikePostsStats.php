<?php

/**
* @package manifest file for Like Posts
* @version 2.0.1
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

class LikePostsStats {
	private $dbInstance;

	public function __construct() {
		$this->dbInstance = new LikePostsStatsDB();
	}

	public function checkStatsPermission() {
		global $context, $txt, $modSettings, $user_info;

		$context['like_post_stats_error'] = '';
		if(!isset($modSettings['lp_mod_enable']) || empty($modSettings['lp_mod_enable'])) {
			$context['like_post_stats_error'] = $txt['lp_no_access'];
		}

		if(!LikePosts::$LikePostsUtils->isAllowedTo(array('lp_guests_can_view_likes_stats', 'lp_can_view_likes_stats'))) {
			$context['like_post_stats_error'] = $txt['lp_no_access'];
		}
	}

	public function messageStats() {
		global $txt;

		$data = $this->dbInstance->getStatsMostLikedMessage();
		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function topicStats() {
		global $txt;

		$data = $this->dbInstance->getStatsMostLikedTopic();
		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function boardStats() {
		global $txt;

		$data = $this->dbInstance->getStatsMostLikedBoard();

		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function mostLikesReceivedUserStats() {
		global $txt;

		$data = $this->dbInstance->getStatsMostLikedUser();

		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function mostLikesGivenUserStats() {
		global $txt;

		$data = $this->dbInstance->getStatsMostLikesGivenUser();

		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}
}

?>
