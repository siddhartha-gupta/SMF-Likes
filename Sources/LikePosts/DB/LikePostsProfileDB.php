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

class LikePostsProfileDB {
	public function __construct() {}

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

	/*
	 * To get posts liked by user
	 * add permissions to this
	*/
	public function getOwnLikes($user_id = 0, $start_limit = 0) {
		global $smcFunc, $scripturl, $modSettings;

		if (empty($user_id)) {
			return false;
		}

		$end_limit = isset($modSettings['lp_per_profile_page']) && !empty($modSettings['lp_per_profile_page']) ? (int) $modSettings['lp_per_profile_page'] : 10;

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

		$end_limit = isset($modSettings['lp_per_profile_page']) && !empty($modSettings['lp_per_profile_page']) ? (int) $modSettings['lp_per_profile_page'] : 10;

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
}

?>
