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

class LikePostsDB {
	public function __construct() {}

	/*
	* Functions for admin panel
	*/
	// To update permission settings from admin panel
	public function updatePermissions($replaceArray) {
		global $smcFunc;

		$smcFunc['db_insert']('replace',
			'{db_prefix}settings',
			array('variable' => 'string-255', 'value' => 'string-65534'),
			$replaceArray,
			array('variable')
		);
		cache_put_data('modSettings', null, 90);
	}

	public function recountLikesTotal($startLimit, $endLimit, $totalWork) {
		global $smcFunc;

		if(!isset($totalWork) || empty($totalWork)) {
			$request = $smcFunc['db_query']('', '
				SELECT COUNT(id_member)
				FROM {db_prefix}members'
			);
			list($totalWorkCalc) = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);
		} else {
			$totalWorkCalc = $totalWork;
		}

		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}members
			LIMIT {int:start}, {int:max}',
			array(
				'start' => $startLimit,
				'max' => 100,
			)
		);

		$insertData = array();
		$updateIds = array();
		$updateData = '';
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$calculatedLikeCount = 0;
			$request1 = $smcFunc['db_query']('', '
				SELECT COUNT(lp.id_member_received) as count, lc.like_count
				FROM {db_prefix}like_post AS lp
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = lp.id_msg)
				LEFT JOIN {db_prefix}like_count AS lc ON (lc.id_member = {int:id_member})
				where m.id_member = {int:id_member}',
				array(
					'id_member' => $row['id_member'],
				)
			);
			if ($smcFunc['db_num_rows']($request1) !== 0) {
				list ($calculatedLikeCount, $presentCount) = $smcFunc['db_fetch_row']($request1);
				if($presentCount === NULL) {
					$insertData[] = array($row['id_member'], $calculatedLikeCount);
				} else if($calculatedLikeCount !== $presentCount) {
					$updateIds[] = $row['id_member'];
					$updateData .= '
							WHEN ' . $row['id_member'] . ' THEN ' . $calculatedLikeCount;
				}
			} else {
				$insertData[] = array($row['id_member'], $calculatedLikeCount);
			}
			$smcFunc['db_free_result']($request1);
		}
		$smcFunc['db_free_result']($request);

		if(!empty($updateData) && !empty($updateIds)) {
			$result = $smcFunc['db_query']('', '
				UPDATE {db_prefix}like_count
				SET like_count = CASE id_member '. $updateData .' END
				WHERE id_member IN ({array_int:updateIds})',
				array(
					'updateIds' => $updateIds
				)
			);
		}

		if(!empty($insertData)) {
			$result = $smcFunc['db_insert']('replace',
				'{db_prefix}like_count',
				array('id_member' => 'int', 'like_count' => 'int'),
				$insertData,
				array('id_member')
			);
		}
	}

	/*
	* Functions for like post entry/delete handling
	*/
	/*
	 * To insert new likes in DB
	*/
	public function insertLikePost($data = array()) {
		global $smcFunc, $user_info;

		if ($user_info['is_guest']) {
			return false;
		}

		if (!is_array($data)) {
			return false;
		}

		$smcFunc['db_insert']('replace',
			'{db_prefix}like_post',
			array('id_msg' => 'int', 'id_topic' => 'int', 'id_board' => 'int', 'id_member_gave' => 'int', 'id_member_received' => 'int', 'rating' => 'int', 'liked_timestamp' => 'int'),
			array($data['id_msg'], $data['id_topic'], $data['id_board'], $data['id_member_gave'], $data['id_member_received'], $data['rating'], time()),
			array('id_like')
		);

		$result = $smcFunc['db_query']('', '
			UPDATE {db_prefix}like_count
			SET like_count = like_count + {int:count}
			WHERE id_member = {int:id_member_received}',
			array(
				'id_member_received' => $data['id_member_received'],
				'count' => 1,
			)
		);

		if ($smcFunc['db_affected_rows']() == 0) {
			$result = $smcFunc['db_insert']('ignore',
				'{db_prefix}like_count',
				array('id_member' => 'int', 'like_count' => 'int'),
				array($data['id_member_received'], 1),
				array('id_member')
			);
		}
		return true;
	}

	/*
	 * Used when a topic is unliked
	*/
	public function deleteLikePost($data = array()) {
		global $smcFunc, $user_info;

		if ($user_info['is_guest']) {
			return false;
		}

		if (!is_array($data)) {
			return false;
		}

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}like_post
			WHERE id_msg = {int:id_msg}
				AND id_topic = {int:id_topic}
				AND id_board = {int:id_board}
				AND id_member_gave = {int:id_member_gave}',
			array(
				'id_msg' => $data['id_msg'],
				'id_topic' => $data['id_topic'],
				'id_board' => $data['id_board'],
				'id_member_gave' => $data['id_member_gave'],
			)
		);

		$result = $smcFunc['db_query']('', '
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
	 * To count number of like posts
	 * Update UI accordingly
	*/
	public function getLikeTopicCount($boardId = 0, $topicId = 0, $msg_id = 0) {
		global $smcFunc;

		$count = 0;
		if (empty($boardId) || empty($topicId) || empty($msg_id)) {
			return false;
		}

		$request = $smcFunc['db_query']('', '
			SELECT COUNT(lp.id_msg) as count
			FROM {db_prefix}like_post as lp
			WHERE lp.id_board = {int:id_board}
			AND lp.id_topic = {int:id_topic}
			AND lp.id_msg = {int:id_msg}
			ORDER BY lp.id_msg',
			array(
				'id_board' => $boardId,
				'id_topic' => $topicId,
				'id_msg' => $msg_id
			)
		);
		list($count) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		return $count;
	}




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
	 * Underlying DB implementation of LP_getAllMessagesInfo
	*/
	public function getAllMessagesInfo($msgsArr, $boardId = '', $topicId = '') {
		global $smcFunc, $scripturl;

		$topicsLikeInfo = array();
		if (count($msgsArr) == 0 || empty($boardId) || empty($topicId)) {
			return $topicsLikeInfo;
		}

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, lp.id_member_gave, lp.rating, mem.real_name
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			WHERE lp.id_board = {int:id_board}
			AND lp.id_topic = {int:id_topic}
			AND lp.id_msg IN ({array_int:message_list})
			ORDER BY lp.id_msg',
			array(
				'id_board' => $boardId,
				'id_topic' => $topicId,
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
	 * Underlying DB implementation of LP_getMessageLikeInfo
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
	 * Underlying DB implementation of LP_getAllTopicsInfo
	*/
	public function getAllTopicsInfo($topicsArr = array(), $boardId = 0) {
		global $smcFunc, $scripturl;

		$topicsLikeInfo = array();
		if (count($topicsArr) == 0 || empty($boardId)) {
			return $topicsLikeInfo;
		}

		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, lp.id_msg, lp.id_member_gave, lp.rating
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			INNER JOIN {db_prefix}topics as t ON (t.id_first_msg = lp.id_msg)
			WHERE lp.id_board = {int:id_board}
			AND lp.id_topic IN ({array_int:topics_list})
			ORDER BY lp.id_msg',
			array(
				'id_board' => $boardId,
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

	/*
	 * To get posts liked by user
	 * add permissions to this
	*/
	public function getOwnLikes($user_id = 0, $start_limit = 0) {
		global $smcFunc, $scripturl, $modSettings;

		if (empty($user_id)) {
			return false;
		}

		$end_limit = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

		$request = $smcFunc['db_query']('', '
			SELECT m.id_msg, m.subject, m.id_topic, m.poster_time, m.body, m.smileys_enabled
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			WHERE {query_wanna_see_board}
			AND lp.id_member_gave = {int:id_member}
			ORDER BY m.id_msg
			LIMIT {int:start_limit}, {int:end_limit}',
			array(
				'id_member' => $user_id,
				'start_limit' => $start_limit,
				'end_limit' => $end_limit
			)
		);

		$likedData = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			if(isset($likedData[$row['id_msg']])) {
				$likedData[$row['id_msg']]['total_likes']++;
			} else {
				$likedData[$row['id_msg']] = array(
					'id' => $row['id_msg'],
					'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
					'subject' => $row['subject'],
					'body' => parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']),
					'time' => timeformat($row['poster_time']),
					'total_likes' => 1
				);
			}
		}
		$smcFunc['db_free_result']($request);
		return $likedData;
	}


	/*
	 * To get posts of a user liked by other
	 * add permissions to this
	*/
	public function getOthersLikes($user_id = 0, $start_limit = 0) {
		global $smcFunc, $scripturl, $modSettings;

		if (empty($user_id)) {
			return false;
		}

		$end_limit = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

		$request = $smcFunc['db_query']('', '
			SELECT m.id_msg, m.subject, m.id_topic, m.poster_time, m.body, m.smileys_enabled, GROUP_CONCAT(CONVERT(lp.id_member_gave, CHAR(8)) SEPARATOR ",") AS member_count
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			WHERE {query_wanna_see_board}
			AND m.id_member = {int:id_member}
			GROUP BY m.id_msg
			ORDER BY m.id_msg
			LIMIT {int:start_limit}, {int:end_limit}',
			array(
				'id_member' => $user_id,
				'start_limit' => $start_limit,
				'end_limit' => $end_limit
			)
		);

		$likedData = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$total_likes = explode(',' , $row['member_count']);
			$likedData[$row['id_msg']] = array(
				'id' => $row['id_msg'],
				'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
				'subject' => $row['subject'],
				'body' => parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']),
				'time' => timeformat($row['poster_time']),
				'total_likes' => count($total_likes)
			);
		}
		$smcFunc['db_free_result']($request);
		return $likedData;
	}

	public function getAllNotification() {
		global $context, $smcFunc, $scripturl, $settings, $user_info, $modSettings;

		$notificationData = array(
			'all' => array(),
			'mine' => array()
		);
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, lp.id_topic, m.subject, mem.real_name, lp.id_member_gave, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
			WHERE {query_wanna_see_board}
			ORDER BY lp.id_like DESC
			LIMIT {int:limit}',
			array(
				'limit' => isset($modSettings['like_in_notification']) && !empty($modSettings['like_in_notification']) ? (int) $modSettings['like_in_notification'] : 10,
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$notificationData['all'][$row['id_msg'] . '-' . $row['id_member_gave']] = array(
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

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, lp.id_topic, m.subject, mem.real_name, lp.id_member_gave, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
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
				'limit' => isset($modSettings['like_in_notification']) && !empty($modSettings['like_in_notification']) ? (int) $modSettings['like_in_notification'] : 10,
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$notificationData['mine'][$row['id_msg'] . '-' . $row['id_member_gave']] = array(
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
		return $notificationData;
	}

	public function getStatsMostLikedMessage() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked Message
		$mostLikedMessage = array();
		$request = $smcFunc['db_query']('', '
			SELECT mem.real_name as member_received_name, lp.id_msg, lp.id_topic, lp.id_board, lp.id_member_received, GROUP_CONCAT(CONVERT(lp.id_member_gave, CHAR(8)) SEPARATOR ",") AS id_member_gave, COUNT(lp.id_msg) AS like_count, m.subject, m.body, m.poster_time, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar, mem.posts, m.smileys_enabled
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_received)
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_received)
			WHERE {query_wanna_see_board}
			GROUP BY lp.id_msg
			ORDER BY like_count DESC
			LIMIT 1',
			array()
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$mostLikedMessage = array(
				'id_msg' => $row['id_msg'],
				'id_topic' => $row['id_topic'],
				'id_board' => $row['id_board'],
				'like_count' => $row['like_count'],
				'subject' => $row['subject'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
				'member_received' => array(
					'id_member' => $row['id_member_received'],
					'name' => $row['member_received_name'],
					'total_posts' => $row['posts'],
					'href' => $row['member_received_name'] != '' && !empty($row['id_member_received']) ? $scripturl . '?action=profile;u=' . $row['id_member_received'] : '',
					'avatar' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
			);
			$id_member_gave = $row['id_member_gave'];
		}
		$smcFunc['db_free_result']($request);

		if(!isset($id_member_gave) || empty($id_member_gave)) {
			return $mostLikedMessage = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		}

		// Lets fetch info of users who liked the message
		$request = $smcFunc['db_query']('', '
			SELECT mem.id_member, mem.real_name, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
			FROM {db_prefix}members as mem
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
			WHERE mem.id_member IN ({raw:id_member_gave})',
			array(
				'id_member_gave' => $id_member_gave
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$mostLikedMessage['member_liked_data'][] = array(
				'id_member' => $row['id_member'],
				'real_name' => $row['real_name'],
				'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'avatar' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),

			);
		}
		$smcFunc['db_free_result']($request);

		return $mostLikedMessage;
	}

	public function getStatsMostLikedTopic() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked topic
		$mostLikedTopic = array();
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_topic, lp.id_board, GROUP_CONCAT(DISTINCT(CONVERT(lp.id_msg, CHAR(8))) SEPARATOR ",") AS id_msg, COUNT(lp.id_topic) AS like_count
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = lp.id_board)
			WHERE {query_wanna_see_board}
			GROUP BY lp.id_topic
			ORDER BY like_count DESC
			LIMIT 1',
			array()
		);
		list ($mostLikedTopic['id_topic'], $mostLikedTopic['id_board'], $id_msg, $mostLikedTopic['like_count']) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if(!isset($id_msg) || empty($id_msg)) {
			return $mostLikedTopic = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		}

		// Lets fetch few messages in the topic
		$request = $smcFunc['db_query']('', '
			SELECT m.id_msg, m.body, m.poster_time, m.smileys_enabled, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.id_member, mem.real_name, mem.avatar
			FROM {db_prefix}messages as m
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
			WHERE m.id_msg IN ({raw:id_msg})
			ORDER BY m.id_msg
			LIMIT 10',
			array(
				'id_msg' => $id_msg
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$mostLikedTopic['msg_data'][] = array(
				'id_msg' => $row['id_msg'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
				'member' => array(
					'id_member' => $row['id_member'],
					'name' => $row['real_name'],
					'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
					'avatar' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
			);
		}
		$smcFunc['db_free_result']($request);

		return $mostLikedTopic;
	}

	public function getStatsMostLikedBoard() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked board
		$mostLikedBoard = array();
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_board, b.name, b.num_topics, b.num_posts, count(DISTINCT(lp.id_topic)) AS topics_liked, count(DISTINCT(lp.id_msg)) AS msgs_liked, SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT(CONVERT(lp.id_topic, CHAR(8))) ORDER BY lp.id_topic DESC SEPARATOR ","), ",", 10) AS id_topic, COUNT(lp.id_board) AS like_count
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = lp.id_board)
			WHERE {query_wanna_see_board}
			GROUP BY lp.id_board
			ORDER BY like_count DESC
			LIMIT 1',
			array()
		);
		list ($mostLikedBoard['id_board'], $mostLikedBoard['name'], $mostLikedBoard['num_topics'], $mostLikedBoard['num_posts'], $mostLikedBoard['topics_liked'], $mostLikedBoard['msgs_liked'], $id_topics, $mostLikedBoard['like_count']) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if(!isset($id_topics) || empty($id_topics)) {
			return $mostLikedBoard = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		}

		// Lets fetch few topics from this board
		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, m.id_msg, m.body, m.poster_time, m.smileys_enabled, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.id_member, mem.real_name, mem.avatar
			FROM {db_prefix}topics as t
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = t.id_first_msg)
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
			WHERE t.id_topic IN ({raw:id_topics})
			ORDER BY t.id_topic DESC',
			array(
				'id_topics' => $id_topics
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$mostLikedBoard['topic_data'][] = array(
				'id_topic' => $row['id_topic'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
				'member' => array(
					'id_member' => $row['id_member'],
					'name' => $row['real_name'],
					'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
					'avatar' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
			);
		}
		$smcFunc['db_free_result']($request);

		return $mostLikedBoard;
	}

	public function getStatsMostLikedUser() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked board
		$mostLikedMember = array();
		$request = $smcFunc['db_query']('', '
			SELECT lc.id_member, lc.like_count, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.id_member, mem.real_name, mem.avatar, mem.date_registered, mem.posts
			FROM {db_prefix}like_count as lc
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lc.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lc.id_member)
			ORDER BY lc.like_count DESC
			LIMIT 1',
			array()
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$mostLikedMember = array(
				'member_received' => array(
					'id_member' => $row['id_member'],
					'name' => $row['real_name'],
					'total_posts' => $row['posts'],
					'date_registered' => $row['date_registered'],
					'href' => $row['real_name'] != '' && !empty($row['id_member_received']) ? $scripturl . '?action=profile;u=' . $row['id_member_received'] : '',
					'avatar' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
				'like_count' => $row['like_count'],
			);
			$id_member = $row['id_member'];
		}
		$smcFunc['db_free_result']($request);

		if(!isset($id_member) || empty($id_member)) {
			return $mostLikedMember = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		}

		// Lets fetch highest liked posts by this user
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, lp.id_topic, COUNT(lp.id_msg) AS like_count, m.subject, m.body, m.poster_time, m.smileys_enabled
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			WHERE {query_wanna_see_board}
			AND lp.id_member_received = {int:id_member}
			GROUP BY lp.id_msg
			ORDER BY like_count DESC
			LIMIT 10',
			array(
				'id_member' => $id_member
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$mostLikedMember['topic_data'][] = array(
				'id_topic' => $row['id_topic'],
				'id_msg' => $row['id_msg'],
				'like_count' => $row['like_count'],
				'subject' => $row['subject'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
			);
		}
		$smcFunc['db_free_result']($request);

		return $mostLikedMember;
	}

	public function getStatsMostLikesGivenUser() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked board
		$mostLikeGivingMember = array();
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_member_gave, COUNT(lp.id_msg) AS like_count, GROUP_CONCAT(DISTINCT(CONVERT(lp.id_msg, CHAR(8))) ORDER BY lp.id_topic DESC SEPARATOR ",") AS id_msgs, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.real_name, mem.avatar, mem.date_registered, mem.posts
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
			GROUP BY lp.id_member_gave
			ORDER BY like_count DESC
			LIMIT 1',
			array()
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$mostLikeGivingMember = array(
				'member_given' => array(
					'id_member' => $row['id_member_gave'],
					'name' => $row['real_name'],
					'total_posts' => $row['posts'],
					'date_registered' => $row['date_registered'],
					'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
					'avatar' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/LikePosts/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
				'like_count' => $row['like_count'],
			);
			$id_msgs = $row['id_msgs'];
		}
		$smcFunc['db_free_result']($request);

		if(!isset($id_msgs) || empty($id_msgs)) {
			return $mostLikeGivingMember = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		}

		// Lets fetch highest liked posts by this user
		$request = $smcFunc['db_query']('', '
			SELECT m.id_msg, m.id_topic, m.subject, m.body, m.poster_time, m.smileys_enabled
			FROM {db_prefix}messages as m
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			WHERE {query_wanna_see_board}
			AND m.id_msg IN ({raw:id_msgs})
			ORDER BY m.id_msg DESC
			LIMIT 10',
			array(
				'id_msgs' => $id_msgs
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$mostLikeGivingMember['topic_data'][] = array(
				'id_msg' => $row['id_msg'],
				'id_topic' => $row['id_topic'],
				'subject' => $row['subject'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
			);
		}
		$smcFunc['db_free_result']($request);

		return $mostLikeGivingMember;
	}
}

?>
