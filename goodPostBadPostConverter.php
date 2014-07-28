<?php

/**
* @package manifest file for Like Posts
* @version 1.5.2
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

/** 
* Note - I don't recommend using this file anywhere
* Use this at your risk. I don't hold any responsibility if anything happens to you website by using this file.
* This file is completely intended to be use by mod developer as a utility for developing the mod
*/

require_once('SSI.php');

function initGPBPConverter() {
	global $sourcedir;

	require_once($sourcedir . '/Security.php');
	isAllowedTo('admin_forum');

	$subActions = array(
		'convert' => 'convertToLike',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['action']) && isset($subActions[$_REQUEST['action']]) && function_exists($subActions[$_REQUEST['action']]))
		return $subActions[$_REQUEST['action']]();
	else
		echo 'check the url';
}

function convertToLike() {
	global $smcFunc;

	checkTableExists();
	checkVersionofLP();
}

function checkTableExists() {
	global $smcFunc, $db_prefix;

	db_extend();
	$tables = $smcFunc['db_list_tables']();
	$real_prefix = preg_match('~^(`?)(.+?)\\1\\.(.*?)$~', $db_prefix, $match) === 1 ? $match[3] : $db_prefix;

	if(!in_array($real_prefix . 'log_gpbp', $tables)) {
		echo 'Oops! It looks like good post bad post table doesn\'t exist';
		die();
	} elseif(!in_array($real_prefix . 'like_post', $tables)) {
		echo 'Oops! It looks like \'Like posts mod\' isn\'t installed';
		die();
	}
}

function checkVersionofLP() {
	$latestVersion = false;
	$request = $smcFunc['db_query']('', '
		SHOW COLUMNS
		FROM {db_prefix}like_post',
		array(
		)
	);

	if ($request !== false) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			if ($row['Field'] == 'liked_timestamp' && ($row['Type'] == 'int(10) unsigned' || $row['Type'] == 'int(10)'))
				$latestVersion = true;
		}
		$smcFunc['db_free_result']($request);
	}

	if($latestVersion === false) {
		echo 'Please upgrade like posts mod to latest version';
		die();
	}
}

function test() {
	@set_time_limit(300);

	$startLimit = !isset($_REQUEST['startLimit']) || empty($_REQUEST['startLimit']) ? 0 : (int) $_REQUEST['startLimit'];
	$endLimit = (int) $_REQUEST['endLimit'];

	if(!isset($_REQUEST['totalWork']) || empty($_REQUEST['totalWork'])) {
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id_member)
			FROM {db_prefix}members'
		);
		list($totalWork) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
	} else {
		$totalWork = (int) $_REQUEST['totalWork'];
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

	$resp = array('totalWork' => (int) $totalWork, 'endLimit' => (int) $endLimit);
	echo json_encode($resp);
	die();
}

initGPBPConverter();

?>
