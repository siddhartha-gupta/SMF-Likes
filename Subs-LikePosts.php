<?php

/**
* @package manifest file for Like Posts
* @version 1.0 Alpha
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
		array('id_msg' => 'int', 'id_topic' => 'int', 'id_board' => 'int', 'id_member' => 'int', 'rating' => 'int'),
		$data,
		array()
	);
	return true;
}

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
			AND id_member = {int:id_member}',
		array(
			'id_msg' => $data['id_msg'],
			'id_topic' => $data['id_topic'],
			'id_board' => $data['id_board'],
			'id_member' => $data['id_member'],
		)
	);
	return true;
}

function LP_DB_getAllMessagesInfo($msgsArr, $boardId = '', $topicId = '') {
	global $smcFunc, $user_info, $scripturl;

	$topicsLikeInfo = array();
	if (count($msgsArr) == 0 || empty($boardId) || empty($topicId)) {
		return $topicsLikeInfo;
	}

	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, lp.id_member, lp.rating, mem.real_name
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member)
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
        $memberData[$row['id_msg'] . '_' .$row['id_member']] = array(
            'id' => $row['id_member'],
            'name' => $row['real_name'],
            'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
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

function LP_DB_getLikeTopicCount($boardId = 0, $topicId = 0, $msg_id = 0) {
	global $smcFunc, $user_info;

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

function LP_DB_getMessageLikeInfo($msg_id = 0) {
	global $smcFunc, $user_info, $scripturl, $settings, $modSettings;

	if (empty($msg_id)) {
		return false;
	}

	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, lp.id_member, lp.rating, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type,
			mem.gender, mem.avatar, mem.member_name, mem.real_name, mem.icq, mem.aim, mem.yim, mem.msn, mem.karma_good, mem.id_post_group, mem.karma_bad, mem.lngfile, mem.id_group, mem.time_offset, mem.show_online
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member)
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = lp.id_member)
		WHERE lp.id_msg = {int:id_msg}
		ORDER BY lp.id_member',
		array(
			'id_msg' => $msg_id,
			'blank_string' => ''
		)
	);

	$memberData = array();
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
        $memberData[] = array(
            'id' => $row['id_member'],
            'name' => $row['real_name'],
            'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
			'avatar' => array(
				'href' => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : $settings['default_theme_url'] . '/images/no_avatar.png') : (stristr($row['avatar'], 'http://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
			),
        );
	}
	$smcFunc['db_free_result']($request);
	return $memberData;
}

function LP_DB_getAllTopicsInfo($topicsArr = array(), $boardId = 0) {
	global $smcFunc, $user_info, $scripturl;

	$topicsLikeInfo = array();
	if (count($topicsArr) == 0 || empty($boardId)) {
		return $topicsLikeInfo;
	}

	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, lp.id_member, lp.rating, mem.real_name
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member)
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
		$memberData[$row['id_msg'] . '_' .$row['id_member']] = array(
            'id' => $row['id_member']
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

?>