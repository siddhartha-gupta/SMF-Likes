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

class LikePostsStats {
	public function __construct() {
	}

	public function checkStatsPermission() {
		global $context, $txt, $sourcedir, $modSettings, $user_info;

		$context['like_post_stats_error'] = '';
		if(!isset($modSettings['like_post_enable']) || empty($modSettings['like_post_enable'])) {
			$context['like_post_stats_error'] = $txt['lp_no_access'];
		}

		if($user_info['is_guest'] && !LikePosts::$LikePostsUtils->isAllowedTo(array('guests_can_view_likes_stats'))) {
			$context['like_post_stats_error'] = $txt['lp_no_access'];
		}
		if(!LikePosts::$LikePostsUtils->isAllowedTo(array('can_view_likes_stats'))) {
			$context['like_post_stats_error'] = $txt['lp_no_access'];
		}
	}

	public function messageStats() {
		global $context, $txt, $sourcedir, $settings, $user_info;

		$data = LikePosts::$LikePostsDB->getStatsMostLikedMessage();
		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function topicStats() {
		global $context, $txt, $sourcedir, $settings, $user_info;

		$data = LikePosts::$LikePostsDB->getStatsMostLikedTopic();
		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function boardStats() {
		global $context, $txt, $sourcedir, $settings, $user_info;

		$data = LikePosts::$LikePostsDB->getStatsMostLikedBoard();

		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function mostLikesReceivedUserStats() {
		global $context, $txt, $sourcedir, $settings, $user_info;

		$data = LikePosts::$LikePostsDB->getStatsMostLikedUser();

		if($data) {
			$resp = array('response' => true, 'data' => $data);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		} else {
			$resp = array('response' => false, 'error' => $txt['lp_error_something_wrong']);
			return LikePosts::$LikePostsUtils->sendJSONResponse($resp);
		}
	}

	public function mostLikesGivenUserStats() {
		global $context, $txt, $sourcedir, $settings, $user_info;

		$data = LikePosts::$LikePostsDB->getStatsMostLikesGivenUser();

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
