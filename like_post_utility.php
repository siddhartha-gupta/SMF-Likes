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

/** 
* Note - I don't recommend using this file anywhere
* Use this at your risk. I don't hold any responsibility if anything happens to you wensite by using this file.
* This file is completely intended to be use by mod developer as a utility for developing the mod
*/

require_once('SSI.php');

function addMembers() {
	global $smcFunc;

	for($i = 0; $i < 600; $i++) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz' .'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$name = '';
		foreach (array_rand($seed, 5) as $k) $name .= $seed[$k];
		$email = $name . '@a.com';
		$passwd = '12345678';

		$regOptions = array(
			'member_name' => $name,
			'email_address' => $email,
			'passwd' => sha1(strtolower($name) . $passwd),
			'password_salt' => substr(md5(mt_rand()), 0, 4) ,
			'posts' => 0,
			'date_registered' => time(),
			'member_ip' => '127.0.0.1',
			'member_ip2' => '127.0.0.1',
			'validation_code' => '',
			'real_name' => $name,
			'personal_text' => '',
			'pm_email_notify' => 1,
			'id_theme' => 0,
			'id_post_group' => 4,
			'lngfile' => '',
			'buddy_list' => '',
			'pm_ignore_list' => '',
			'message_labels' => '',
			'website_title' => '',
			'website_url' => '',
			'location' => '',
			'icq' => '',
			'aim' => '',
			'yim' => '',
			'msn' => '',
			'time_format' => '',
			'signature' => '',
			'avatar' => '',
			'usertitle' => '',
			'secret_question' => '',
			'secret_answer' => '',
			'additional_groups' => '',
			'ignore_boards' => '',
			'smiley_set' => '',
			'openid_uri' => '',
			'is_activated' => 1
		);

		// Right, now let's prepare for insertion.
		$knownInts = array(
			'date_registered', 'posts', 'id_group', 'last_login', 'instant_messages', 'unread_messages',
			'new_pm', 'pm_prefs', 'gender', 'hide_email', 'show_online', 'pm_email_notify', 'karma_good', 'karma_bad',
			'notify_announcements', 'notify_send_body', 'notify_regularity', 'notify_types',
			'id_theme', 'is_activated', 'id_msg_last_visit', 'id_post_group', 'total_time_logged_in', 'warning',
		);
		$knownFloats = array(
			'time_offset',
		);

		$column_names = array();
		$values = array();
		foreach ($regOptions as $var => $val)
		{
			$type = 'string';
			if (in_array($var, $knownInts))
				$type = 'int';
			elseif (in_array($var, $knownFloats))
				$type = 'float';
			elseif ($var == 'birthdate')
				$type = 'date';

			$column_names[$var] = $type;
			$values[$var] = $val;
		}

		// Register them into the database.
		$smcFunc['db_insert']('',
			'{db_prefix}members',
			$column_names,
			$values,
			array('id_member')
		);
		echo 'count: ' . $i . '<br />';
	}
}

function addPosts() {
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_member, member_name, email_address
		FROM {db_prefix}members
		ORDER BY id_member'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$seed = str_split('abcdefghijklmnopqrstuvwxyz' .'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$subject = '';
		$body = '';
		foreach (array_rand($seed, 5) as $k) {
			$subject .= $seed[$k];
		}
		foreach (array_rand($seed, 50) as $k) {
			$body .= $seed[$k];
		}

		$request1 = $smcFunc['db_query']('', '
			SELECT MAX(id_msg)
			FROM {db_prefix}messages'
		);
		list ($id_msg) = $smcFunc['db_fetch_row']($request1);
		$smcFunc['db_free_result']($request1);
		$current_msg_id = $id_msg + 1;

		$smcFunc['db_insert']('',
			'{db_prefix}topics',
			array(
				'id_board' => 'int', 'id_member_started' => 'int', 'id_member_updated' => 'int', 'id_first_msg' => 'int',
				'id_last_msg' => 'int', 'locked' => 'int', 'is_sticky' => 'int', 'num_views' => 'int',
				'id_poll' => 'int', 'unapproved_posts' => 'int', 'approved' => 'int',
			),
			array(
				1, $row['id_member'], $row['id_member'], $current_msg_id,
				$current_msg_id, 0, 0, 0,
				0, 0, 1,
			),
			array('id_topic')
		);
		$topic_id = $smcFunc['db_insert_id']('{db_prefix}topics', 'id_topic');

        $smcFunc['db_insert']('',
			'{db_prefix}messages',
			array(
				'id_board' => 'int', 'id_topic' => 'int', 'id_member' => 'int', 'subject' => 'string-255', 'body' => (!empty($modSettings['max_messageLength']) && $modSettings['max_messageLength'] > 65534 ? 'string-' . $modSettings['max_messageLength'] : 'string-65534'),
				'poster_name' => 'string-255', 'poster_email' => 'string-255', 'poster_time' => 'int', 'poster_ip' => 'string-255',
				'smileys_enabled' => 'int', 'modified_name' => 'string', 'icon' => 'string-16', 'approved' => 'int',
			),
			array(
				1, $topic_id, $row['id_member'], $subject, $body,
				$row['member_name'], $row['email_address'], time(), '127.0.0.1',
				1, '', 'xx', 1,
			),
			array('id_msg')
		);
	}
	$smcFunc['db_free_result']($request);
	echo 'Completed';
}

function likePosts() {
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_member, member_name, email_address
		FROM {db_prefix}members
		ORDER BY id_member'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$request1 = $smcFunc['db_query']('', '
			SELECT id_msg, id_topic, id_board
			FROM {db_prefix}messages
			ORDER BY RAND()
			LIMIT 4'
		);
		while ($row1 = $smcFunc['db_fetch_assoc']($request1)) {
			$smcFunc['db_insert']('replace',
				'{db_prefix}like_post',
				array('id_msg' => 'int', 'id_topic' => 'int', 'id_board' => 'int', 'id_member' => 'int', 'rating' => 'int'),
				array(
					$row1['id_msg'], $row1['id_topic'], $row1['id_board'], $row['id_member'], 1
				),
				array()
			);
		}
		$smcFunc['db_free_result']($request1);
	}
	$smcFunc['db_free_result']($request);
	echo 'Completed';
}

function rebuildGave() {
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT lp.id_msg, m.id_member
        FROM {db_prefix}like_post as lp
        INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
        GROUP BY id_msg
        ORDER BY id_msg'
	);

	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$result = $smcFunc['db_query']('', '
			UPDATE {db_prefix}like_post
			SET id_member_received = {int:id_member_received}
			WHERE id_msg = {int:id_msg}',
			array(
				'id_member_received' => (int) $row['id_member'],
				'id_msg' => $row['id_msg']
			)
		);
	}
	$smcFunc['db_free_result']($request);
	echo 'Completed';
}

$subActions = array(
	'addposts' => 'addPosts',
	'addmembers' => 'addMembers',
	'likeposts' => 'likePosts',
	'rebuildgave' => 'rebuildGave',
);

//wakey wakey, call the func you lazy
if (isset($_REQUEST['action']) && isset($subActions[$_REQUEST['action']]) && function_exists($subActions[$_REQUEST['action']]))
	return $subActions[$_REQUEST['action']]();
else
	echo 'check the url';

?>