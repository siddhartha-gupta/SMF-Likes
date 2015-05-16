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
 

if (!defined('SMF')) {
	die('Hacking attempt...');
}

class LikePostsAdminDB {
	public function __construct() {}

	// Functions for admin panel
	/*
	* to update permission settings from admin panel
	* @param array $replaceArray
	*/
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

	/**
	 * To clean up the likes table from delete posts
	 */
	public function optimizeLikes() {
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg
			FROM {db_prefix}like_post AS lp
			LEFT JOIN {db_prefix}messages AS m ON (lp.id_msg = m.id_msg)
			WHERE m.id_msg is null',
			array()
		);

		$msg_id = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$msg_id[] = $row['id_msg'];
		}

		if (count($msg_id) > 0) {
			$msg_id_list = implode(",", $msg_id);

			$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}like_post
			WHERE id_msg IN ({raw:msg_id_list})',
				array(
					'msg_id_list' => $msg_id_list,
				)
			);
		}

		$smcFunc['db_free_result']($request);
		return true;
	}

	/**
	 * To clean up duplicate likes from like_post table
	 */
	public function removeDupLikes() {
		global $smcFunc;

		$smcFunc['db_query']('', '
			DELETE lp1
			FROM {db_prefix}like_post lp1, {db_prefix}like_post lp2
			WHERE lp1.id_msg = lp2.id_msg
			AND lp1.id_member_gave = lp2.id_member_gave
			AND lp1.id_like < lp2.id_like',
			array()
		);		
	}

	/**
	 * To recount the likes and update like_count table
	 * @param integer $startLimit
	 * @param integer $totalWork
	 */
	public function recountLikesTotal($startLimit, $totalWork) {
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
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}like_count
				SET like_count = CASE id_member '. $updateData .' END
				WHERE id_member IN ({array_int:updateIds})',
				array(
					'updateIds' => $updateIds
				)
			);
		}

		if(!empty($insertData)) {
			$smcFunc['db_insert']('replace',
				'{db_prefix}like_count',
				array('id_member' => 'int', 'like_count' => 'int'),
				$insertData,
				array('id_member')
			);
		}

		return $totalWorkCalc;
	}
}

?>
