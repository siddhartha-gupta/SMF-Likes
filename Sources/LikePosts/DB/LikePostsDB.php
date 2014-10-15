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

class LikePostsDB {
	public function __construct() {}

	/*
	* Function to add like post entry in DB
	* @param array $data
	*/
	public function insertLikePost($data = array()) {
		global $smcFunc, $user_info;

		if ($user_info['is_guest'] || !is_array($data)) {
			return false;
		}

		$smcFunc['db_insert']('replace',
			'{db_prefix}like_post',
			array('id_msg' => 'int', 'id_member_gave' => 'int', 'id_member_received' => 'int', 'rating' => 'int', 'liked_timestamp' => 'int'),
			array($data['id_msg'], $data['id_member_gave'], $data['id_member_received'], $data['rating'], time()),
			array('id_like')
		);

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}like_count
			SET like_count = like_count + {int:count}
			WHERE id_member = {int:id_member_received}',
			array(
				'id_member_received' => $data['id_member_received'],
				'count' => 1,
			)
		);

		if ($smcFunc['db_affected_rows']() == 0) {
			$smcFunc['db_insert']('ignore',
				'{db_prefix}like_count',
				array('id_member' => 'int', 'like_count' => 'int'),
				array($data['id_member_received'], 1),
				array('id_member')
			);
		}
		return true;
	}

	/*
	* Functions to delete like post entry from DB
	* @param array $data
	*/
	public function deleteLikePost($data = array()) {
		global $smcFunc, $user_info;

		if ($user_info['is_guest'] || !is_array($data)) {
			return false;
		}

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}like_post
			WHERE id_msg = {int:id_msg}
				AND id_member_gave = {int:id_member_gave}',
			array(
				'id_msg' => $data['id_msg'],
				'id_member_gave' => $data['id_member_gave'],
			)
		);

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}like_count
			SET like_count = like_count - {int:count}
			WHERE id_member = {int:id_member_received}',
			array(
				'id_member_received' => $data['id_member_received'],
				'count' => 1,
			)
		);
		return true;
	}

	/*
	 * To count number of posts liked
	 * Update UI accordingly
	*/
	public function getLikeTopicCount($msg_id = 0) {
		global $smcFunc;

		if (empty($msg_id)) {
			return false;
		}

		$count = 0;
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(lp.id_msg) as count
			FROM {db_prefix}like_post as lp
			WHERE lp.id_msg = {int:id_msg}
			ORDER BY lp.id_msg',
			array(
				'id_msg' => $msg_id
			)
		);
		list($count) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		return $count;
	}

	/*
	 * Get who liked the post
	*/
	public function posterInfo($postersArr) {
		global $smcFunc;

		$postersInfo = array();
		if (count($postersArr) === 0) {
			return $postersInfo;
		}

		$request = $smcFunc['db_query']('', '
			SELECT id_member, like_count
			FROM {db_prefix}like_count
			WHERE id_member IN ({array_int:postersArr})
			ORDER BY id_member',
			array(
				'postersArr' => $postersArr,
			)
		);
		if ($smcFunc['db_num_rows']($request) == 0) {
			return $postersInfo;
		}

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$postersInfo[$row['id_member']] = $row['like_count'];
		}
		$smcFunc['db_free_result']($request);

		return $postersInfo;
	}

	/*
	 * Underlying DB implementation of getAllMessagesInfo
	*/
	public function getAllMessagesInfo($msgsArr) {
		global $smcFunc, $scripturl;

		$topicsLikeInfo = array();
		if (count($msgsArr) == 0) {
			return $topicsLikeInfo;
		}

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, lp.id_member_gave, lp.rating, mem.real_name
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			WHERE lp.id_msg IN ({array_int:message_list})
			ORDER BY lp.id_msg',
			array(
				'message_list' => $msgsArr,
			)
		);
		if ($smcFunc['db_num_rows']($request) == 0) {
			return $topicsLikeInfo;
		}

		$memberData = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$memberData[$row['id_msg'] . '_' .$row['id_member_gave']] = array(
				'id' => $row['id_member_gave'],
				'name' => $row['real_name'],
				'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
			);
			$topicsLikeInfo[$row['id_msg']] = array(
				'id_msg' => $row['id_msg'],
				'rating' => $row['rating'],
				'count' => isset($topicsLikeInfo[$row['id_msg']]['count']) ? ++$topicsLikeInfo[$row['id_msg']]['count'] : 1,
			);
		}
		$smcFunc['db_free_result']($request);

		foreach($topicsLikeInfo as $key => $val) {
			foreach($memberData as $memKey => $memVal) {
				$tempArray = explode('_', $memKey);
				if($tempArray[0] == $key) {
					$topicsLikeInfo[$key]['members'][$tempArray[1]] = $memVal;
				}
			}
		}

		return $topicsLikeInfo;
	}

	/*
	 * Underlying DB implementation of getMessageLikeInfo
	*/
	public function getMessageLikeInfo($msg_id = 0) {
		global $smcFunc, $scripturl, $settings, $modSettings;

		if (empty($msg_id)) {
			return false;
		}

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, lp.id_member_gave, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar, mem.real_name
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
			WHERE lp.id_msg = {int:id_msg}
			ORDER BY lp.id_member_gave',
			array(
				'id_msg' => $msg_id,
				'blank_string' => ''
			)
		);

		$memberData = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$memberData[] = array(
				'id' => $row['id_member_gave'],
				'name' => $row['real_name'],
				'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
				'avatar' => array(
					'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
			);
		}
		$smcFunc['db_free_result']($request);
		return $memberData;
	}

	/*
	 * Underlying DB implementation of getAllTopicsInfo
	*/
	public function getAllTopicsInfo($topicsArr = array()) {
		global $smcFunc;

		$topicsLikeInfo = array();
		if (count($topicsArr) == 0) {
			return $topicsLikeInfo;
		}

		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, lp.id_msg, lp.id_member_gave, lp.rating
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			INNER JOIN {db_prefix}topics as t ON (t.id_first_msg = lp.id_msg)
			WHERE t.id_topic IN ({array_int:topics_list})
			ORDER BY lp.id_msg',
			array(
				'topics_list' => $topicsArr,
			)
		);
		if ($smcFunc['db_num_rows']($request) == 0) {
			return $topicsLikeInfo;
		}

		$memberData = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$memberData[$row['id_topic'] . '_' .$row['id_member_gave']] = array(
				'id' => $row['id_member_gave']
			);
			$topicsLikeInfo[$row['id_topic']] = array(
				'id_msg' => $row['id_msg'],
				'rating' => $row['rating'],
				'count' => isset($topicsLikeInfo[$row['id_topic']]['count']) ? ++$topicsLikeInfo[$row['id_topic']]['count'] : 1,
			);
		}
		$smcFunc['db_free_result']($request);

		foreach($topicsLikeInfo as $key => $val) {
			foreach($memberData as $memKey => $memVal) {
				$tempArray = explode('_', $memKey);
				if($tempArray[0] == $key) {
					$topicsLikeInfo[$key]['members'][$tempArray[1]] = $memVal;
				}
			}
		}
		return $topicsLikeInfo;
	}

	// For profile section
	/**
	 * @param string $select
	 * @param string $where
	 */
	public function getTotalResults($select, $where) {
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT '. $select .' as total_results
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			WHERE {query_wanna_see_board}
			AND ' . $where
		);

		if ($smcFunc['db_num_rows']($request) == 0)
			return 'nothing found';

		list ($total_results) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return $total_results;
	}

	public function getAllNotification() {
		$notificationData = array(
			'all' => array(),
			'mine' => array()
		);

		$notificationData['all'] = $this->getAllLikeNotification();
		$notificationData['mine'] = $this->getMyPostNotificationData();
		
		return $notificationData;
	}

	private function getAllLikeNotification() {
		global $context, $smcFunc, $scripturl, $settings, $modSettings;

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, m.id_topic, m.subject, mem.real_name, lp.id_member_gave, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
			WHERE {query_wanna_see_board}
			ORDER BY lp.id_like DESC
			LIMIT {int:limit}',
			array(
				'limit' => isset($modSettings['lp_in_notification']) && !empty($modSettings['lp_in_notification']) ? (int) $modSettings['lp_in_notification'] : 10,
			)
		);

		$data = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$data[$row['id_msg'] . '-' . $row['id_member_gave']] = array(
				'id' => $row['id_msg'],
				'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
				'subject' => (!$context['utf8']) ? utf8_encode($row['subject']) : $row['subject'],
				'total_likes' => 1,
				'member' => array(
					'name' => $row['real_name'],
					'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
					'avatar' => array(
						'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
					),
				),
			);
		}
		$smcFunc['db_free_result']($request);
		return $data;
	}

	private function getMyPostNotificationData() {
		global $context, $smcFunc, $scripturl, $settings, $user_info, $modSettings;

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, m.id_topic, m.subject, mem.real_name, lp.id_member_gave, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
			WHERE {query_wanna_see_board}
			AND lp.id_member_received = {int:id_member_received}
			ORDER BY lp.id_like DESC
			LIMIT {int:limit}',
			array(
				'id_member_received' => $user_info['id'],
				'limit' => isset($modSettings['lp_in_notification']) && !empty($modSettings['lp_in_notification']) ? (int) $modSettings['lp_in_notification'] : 10,
			)
		);

		$data = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$data[$row['id_msg'] . '-' . $row['id_member_gave']] = array(
				'id' => $row['id_msg'],
				'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
				'subject' => (!$context['utf8']) ? utf8_encode($row['subject']) : $row['subject'],
				'total_likes' => 1,
				'member' => array(
					'name' => $row['real_name'],
					'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
					'avatar' => array(
						'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
					),
				),
			);
		}
		$smcFunc['db_free_result']($request);
		return $data;
	}
}

?>
