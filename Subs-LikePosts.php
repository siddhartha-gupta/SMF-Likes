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

    if($user_info['is_guest']) {
		return false;
	}

    if(!is_array($data)) {
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

function LP_DB_getLikePostsInfo($topicIds) {
	global $smcFunc, $user_info;
}

function LP_DB_getLikeTopicsInfo($msgsArr, $boardId = '', $topicId = '') {
	global $smcFunc, $user_info;

	if(count($msgsArr) == 0 || empty($boardId) || empty($topicId)) {
		return false;
	}

	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, lp.id_member, lp.rating, mem.real_name
		FROM {db_prefix}like_post as lp
		INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member)
		WHERE lp.id_board = {int:id_board}
		AND lp.id_topic = {int:id_topic}
		AND id_msg IN ({array_int:message_list})',
		array(
			'id_board' => $boardId,
			'id_topic' => $topicId,
			'message_list' => $msgsArr,
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		return false;

	$topicsLikeInfo = array();
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$topicsLikeInfo[$row['id_msg']] = array(
			'id_msg' => $row['id_msg'],
			'id_member' => $row['id_member'],
			'rating' => $row['rating'],
			'real_name' => $row['real_name'],
		);
	}
	$smcFunc['db_free_result']($request);
	return $topicsLikeInfo;
}

?>