<?php

/**
* @package manifest file for Like Posts
* @version 1.2.1
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
 * To insert new likes in DB
*/
function LP_DB_insertLikePost($data = array()) {
	global $smcFunc, $user_info;

	if ($user_info['is_guest']) {
		return false;
	}

	if (!is_array($data)) {
		return false;
	}

	$smcFunc['db_insert']('replace',
		'{db_prefix}like_post',
		array('id_msg' => 'int', 'id_topic' => 'int', 'id_board' => 'int', 'id_member_gave' => 'int', 'id_member_received' => 'int', 'rating' => 'int'),
		array($data['id_msg'], $data['id_topic'], $data['id_board'], $data['id_member_gave'], $data['id_member_received'], $data['rating']),
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
function LP_DB_deleteLikePost($data = array()) {
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

function LP_DB_posterInfo($postersArr) {
	global $smcFunc, $user_info, $scripturl;

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
function LP_DB_getAllMessagesInfo($msgsArr, $boardId = '', $topicId = '') {
	global $smcFunc, $user_info, $scripturl;

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
 * To count number of like posts
 * Update UI accordingly
*/
function LP_DB_getLikeTopicCount($boardId = 0, $topicId = 0, $msg_id = 0) {
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

/*
 * Underlying DB implementation of LP_getMessageLikeInfo
*/
function LP_DB_getMessageLikeInfo($msg_id = 0) {
	global $smcFunc, $scripturl, $settings, $modSettings;

	if (empty($msg_id)) {
		return false;
	}

	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, lp.id_member_gave, lp.rating, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type,
			mem.gender, mem.avatar, mem.member_name, mem.real_name, mem.icq, mem.aim, mem.yim, mem.msn, mem.karma_good, mem.id_post_group, mem.karma_bad, mem.lngfile, mem.id_group, mem.time_offset, mem.show_online
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
				'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
			),
		);
	}
	$smcFunc['db_free_result']($request);
	return $memberData;
}

/*
 * Underlying DB implementation of LP_getAllTopicsInfo
*/
function LP_DB_getAllTopicsInfo($topicsArr = array(), $boardId = 0) {
	global $smcFunc, $scripturl;

	$topicsLikeInfo = array();
	if (count($topicsArr) == 0 || empty($boardId)) {
		return $topicsLikeInfo;
	}

	$request = $smcFunc['db_query']('', '
		SELECT t.id_topic, lp.id_msg, lp.id_member_gave, lp.rating, mem.real_name
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

/*
 * To get posts liked by user
 * add permissions to this
*/
function LP_DB_getOwnLikes($user_id = 0, $start_limit = 0) {
	global $smcFunc, $scripturl, $modSettings;

	if (empty($user_id)) {
		return false;
	}

	$end_limit = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

	$request = $smcFunc['db_query']('', '
		SELECT m.id_msg, m.subject, m.id_topic, m.poster_time, m.body, m.smileys_enabled
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
		WHERE lp.id_member_gave = {int:id_member}
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
function LP_DB_getOthersLikes($user_id = 0, $start_limit = 0) {
	global $smcFunc, $scripturl, $modSettings;

	if (empty($user_id)) {
		return false;
	}

	$end_limit = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

	$request = $smcFunc['db_query']('', '
		SELECT m.id_msg, m.subject, m.id_topic, m.poster_time, m.body, m.smileys_enabled, GROUP_CONCAT(CONVERT(lp.id_member_gave, CHAR(8)) SEPARATOR ",") AS member_count
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
		WHERE m.id_member = {int:id_member}
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

function LP_DB_getTotalResults($select, $where) {
	global $context, $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT '. $select .' as total_results
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
		WHERE ' . $where
	);

	if ($smcFunc['db_num_rows']($request) == 0)
		return 'nothing found';

	list ($total_results) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $total_results;
}

function LP_DB_updatePermissions($replaceArray) {
	global $smcFunc;

	$smcFunc['db_insert']('replace',
		'{db_prefix}settings',
		array('variable' => 'string-255', 'value' => 'string-65534'),
		$replaceArray,
		array('variable')
	);

	cache_put_data('modSettings', null, 90);
}

function LP_DB_getAllNotification() {
	global $smcFunc, $scripturl, $settings, $user_info;

	$notificationData = array();
	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, lp.id_topic, m.subject, mem.real_name, lp.id_member_gave, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
		INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
		ORDER BY lp.id_like DESC
		LIMIT {int:limit}',
		array(
			'limit' => 10,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$notificationData['all'][$row['id_msg'] . '-' . $row['id_member_gave']] = array(
			'id' => $row['id_msg'],
			'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
			'subject' => $row['subject'],
			'total_likes' => 1,
			'member' => array(
				'name' => $row['real_name'],
				'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
				'avatar' => array(
					'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
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
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member_gave)
		WHERE lp.id_member_received = {int:id_member_received}
		ORDER BY lp.id_like DESC
		LIMIT {int:limit}',
		array(
			'id_member_received' => $user_info['id'],
			'limit' => 10,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$notificationData['mine'][$row['id_msg'] . '-' . $row['id_member_gave']] = array(
			'id' => $row['id_msg'],
			'href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['id_msg'] . '#msg' . $row['id_msg'],
			'subject' => $row['subject'],
			'total_likes' => 1,
			'member' => array(
				'name' => $row['real_name'],
				'href' => $row['real_name'] != '' && !empty($row['id_member_gave']) ? $scripturl . '?action=profile;u=' . $row['id_member_gave'] : '',
				'avatar' => array(
					'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
				),
			),
		);
	}
	$smcFunc['db_free_result']($request);
	return $notificationData;
}

?>