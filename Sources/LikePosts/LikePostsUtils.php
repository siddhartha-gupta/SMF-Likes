<?php

/**
 * @package manifest file for Like Posts
 * @version 2.0.4
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

	private $permToCheck = array();
	private $guestPermission = array(
		'lp_guest_can_view_likes_in_posts',
		'lp_guest_can_view_likes_in_boards',
		'lp_guest_can_view_likes_in_profiles',
		'lp_guests_can_view_likes_stats'
	);

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

	public function showLikeNotification() {
		global $modSettings, $user_info;

		if(!empty($modSettings['lp_mod_enable']) && !empty($modSettings['lp_notification_enable']) &&
			LikePosts::$LikePostsUtils->isAllowedTo(array('lp_can_view_likes_notification'))) {
			return true;
		} else {
			return false;
		} 
	}

	/**
	 * @param string[] $permissions
	 */
	public function isAllowedTo($permissions) {
		global $user_info;

		if ($user_info['is_admin']) {
			return true;
		}

		if (!is_array($permissions)) {
			$permissions = array($permissions);
		}

		if ($user_info['is_guest']) {
			$this->permToCheck = array_intersect($this->guestPermission, $permissions);
			$result = $this->checkGuestPermission();
		} else {
			$this->permToCheck = array_diff($permissions, $this->guestPermission);
			$result = $this->checkUserPermission();
		}
		return $result;
	}

	private function checkGuestPermission() {
		global $modSettings;

		$result = false;
		foreach ($this->permToCheck as $permission) {
			if (isset($modSettings[$permission]) && !empty($modSettings[$permission])) {
				$result = true;
			} else {
				$result = false;
			}
		}
		return $result;
	}

	private function checkUserPermission() {
		global $modSettings, $user_info;

		$result = true;
		foreach ($this->permToCheck as $permission) {
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
		return $result;
	}

	public function isBoardIncluded($boardId) {
		global $modSettings;

		$isAllowed = false;
		if(isset($modSettings['lp_active_boards']) && strlen($modSettings['lp_active_boards']) > 0) {
			$activeBoards = explode(',', $modSettings['lp_active_boards']);
			if(is_array($activeBoards) && in_array($boardId, $activeBoards)) {
				$isAllowed = true;
			}
		}
		return $isAllowed;
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

	/**
	 * @param string $delimiter
	 */
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

	public function obtainAvatar($data) {
		global $modSettings, $scripturl, $settings;

		if(!empty($data['avatar'])) {
			if(stristr($data['avatar'], 'http://')) {
				$avatar = $data['avatar'];
			} else {
				$avatar = $modSettings['avatar_url'] . '/' . $data['avatar'];
			}
			
		} elseif ($data['id_attach'] > 0) {
			if(empty($data['attachment_type'])) {
				$avatar = $scripturl . '?action=dlattach;attach=' . $data['id_attach'] . ';type=avatar';
			} else {
				$avatar = $modSettings['custom_avatar_url'] . '/' . $data['filename'];
			}
		} else {
			$avatar = $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png';
		}
		return $avatar;
	}
}

?>
