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

if (!defined('SMF'))
	die('Hacking attempt...');

class LikePostsUtils {

	public function __construct() {}

	public function checkJsonEncodeDecode() {
		global $sourcedir;

		if (!function_exists('json_decode')) {
			require_once ($sourcedir . '/LikePosts/JSON.php');

			function json_decode($content, $assoc = false) {
				if ($assoc) {
					$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
				} else {
					$json = new Services_JSON;
				}
				return $json->decode($content);
			}
		}

		if (!function_exists('json_encode')) {
			require_once ($sourcedir . '/LikePosts/JSON.php');

			function json_encode($content) {
				$json = new Services_JSON;
				return $json->encode($content);
			}
		}
	}

	public function isAllowedTo($permissions) {
		global $modSettings, $user_info;

		if ($user_info['is_admin']) {return true;
		}

		if (!is_array($permissions)) {
			$permissions = array($permissions);
		}

		$result = true;
		$guestPermission = array(
			'can_view_likes_in_posts',
			'can_view_likes_in_boards',
			'can_view_likes_in_profiles',
		);

		if ($user_info['is_guest']) {
			$result = false;
			$permToCheck = array_intersect($guestPermission, $permissions);
			foreach ($permToCheck as $permission) {
				if (in_array($permission, $guestPermission) && isset($modSettings[$permission]) && !empty($modSettings[$permission])) {
					$result = true;
				} else {
					$result = false;
				}
			}
		} else {
			$permToCheck = array_diff($permissions, $guestPermission);
			foreach ($permToCheck as $permission) {
				if (!isset($modSettings[$permission]) || strlen($modSettings[$permission]) === 0) {
					$result = false;
				} else {
					$allowedGroups = explode(',', $modSettings[$permission]);
					$groupsPassed = array_intersect($allowedGroups, $user_info['groups']);

					if (empty($groupsPassed)) {
						$result = false;
						break;
					}
				}
			}
		}
		return $result;
	}

	public function isBoardIncluded($boardId) {
		global $modSettings;

		$activeBoards = isset($modSettings['lp_active_boards']) && strlen($modSettings['lp_active_boards']) > 0 ? (explode(',', $modSettings['lp_active_boards'])) : '';

		if(is_array($activeBoards) && in_array($boardId, $activeBoards)) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * To check whether a specific topic is liked or not
	 * Used in MessageIndex template
	*/
	public function isTopicLiked($arr, $id) {
		global $txt, $user_info;

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
				if ($remaining_likes > 0) {
					if($remaining_likes > 1) {
						$data['count_text'] = $txt['lp_string_you'] . ' ' . sprintf($txt['lp_string_other_multiple_people_liked'], $remaining_likes);
					} else {
						$data['count_text'] = $txt['lp_string_you'] . ' ' . sprintf($txt['lp_string_other_people_liked'], $remaining_likes);
					}
				} else {
					$data['count_text'] = $txt['lp_string_you_liked'];
				}
				//If already liked make it to unlink
				$data['already_liked'] = 0;
			} else {
				$data['text'] = $txt['lp_like'];
				if($data['count'] > 1) {
					$data['count_text'] = sprintf($txt['lp_string_multiple_people_liked'], $data['count']);
				} else {
					$data['count_text'] = sprintf($txt['lp_string_people_liked'], $data['count']);
				}

				//Give them the option to like
				$data['already_liked'] = 1;
			}
		}
		return $data;
	}

	/*
	 * To check whether a specific message is liked or not
	 * Used in Display template
	*/
	public function isPostLiked($arr, $id) {
		global $txt, $user_info;

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

				if ($remaining_likes > 0) {
					if($remaining_likes > 1) {
						$data['count_text'] = $txt['lp_string_you'] . ' ' . sprintf($txt['lp_string_other_multiple_people_liked'], $remaining_likes);
					} else {
						$data['count_text'] = $txt['lp_string_you'] . ' ' . sprintf($txt['lp_string_other_people_liked'], $remaining_likes);
					}
				} else {
					$data['count_text'] = $txt['lp_string_you_liked'];
				}

				//If already liked make it to unlink
				$data['already_liked'] = 0;
			} else {
				$data['text'] = $txt['lp_like'];
				if($data['count'] > 1) {
					$data['count_text'] = sprintf($txt['lp_string_multiple_people_liked'], $data['count']);
				} else {
					$data['count_text'] = sprintf($txt['lp_string_people_liked'], $data['count']);
				}

				//Give them the option to like
				$data['already_liked'] = 1;
			}
		}
		return $data;
	}

	public function trimContent($str, $delimiter, $limit = 255) {
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

	public function sendJSONResponse($resp) {
		echo json_encode($resp);
		die();
	}
}

?>
