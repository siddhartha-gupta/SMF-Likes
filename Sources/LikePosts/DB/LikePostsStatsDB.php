<?php

/**
* @package manifest file for Like Posts
* @version 2.0.1
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

class LikePostsStatsDB {
	public function __construct() {}

	public function getStatsMostLikedMessage() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked Message
		$mostLikedMessage = array();
		$request = $smcFunc['db_query']('', '
			SELECT mem.real_name as member_received_name, lp.id_msg, m.id_topic, m.id_board, lp.id_member_received, GROUP_CONCAT(CONVERT(lp.id_member_gave, CHAR(8)) SEPARATOR ",") AS id_member_gave, COUNT(lp.id_msg) AS like_count, m.subject, m.body, m.poster_time, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar, mem.posts, m.smileys_enabled
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_received)
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
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
					'avatar' => LikePosts::$LikePostsUtils->obtainAvatar(array(
						'avatar' => $row['avatar'],
						'id_attach' => $row['id_attach'],
						'attachment_type' => $row['attachment_type'],
						'filename' => $row['filename']
					)),
				),
			);
			$id_member_gave = $row['id_member_gave'];
		}
		$smcFunc['db_free_result']($request);

		if(!isset($id_member_gave) || empty($id_member_gave)) {
			$mostLikedMessage = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		} else {
			// Lets fetch info of users who liked the message
			$mostLikedMessage['member_liked_data'] = $this->fetchMembers(array('id_member_gave' => $id_member_gave));
		}
		return $mostLikedMessage;
	}

	private function fetchMembers($data = array('id_member_gave' => '')) {
		global $smcFunc, $scripturl, $modSettings, $settings;

		$request = $smcFunc['db_query']('', '
			SELECT mem.id_member, mem.real_name, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.avatar
			FROM {db_prefix}members as mem
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
			WHERE mem.id_member IN ({raw:id_member_gave})',
			array(
				'id_member_gave' => $data['id_member_gave']
			)
		);

		$members = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$members[] = array(
				'id_member' => $row['id_member'],
				'real_name' => $row['real_name'],
				'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'avatar' => LikePosts::$LikePostsUtils->obtainAvatar(array(
					'avatar' => $row['avatar'],
					'id_attach' => $row['id_attach'],
					'attachment_type' => $row['attachment_type'],
					'filename' => $row['filename']
				)),

			);
		}
		$smcFunc['db_free_result']($request);
		return $members;
	}

	public function getStatsMostLikedTopic() {
		global $smcFunc, $txt;

		// Most liked topic
		$mostLikedTopic = array();
		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, t.id_board, GROUP_CONCAT(DISTINCT(CONVERT(lp.id_msg, CHAR(8))) SEPARATOR ",") AS id_msg, COUNT(t.id_topic) AS like_count
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}topics AS t ON (t.id_first_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE {query_wanna_see_board}
			GROUP BY t.id_topic
			ORDER BY like_count DESC
			LIMIT 1',
			array()
		);
		list ($mostLikedTopic['id_topic'], $mostLikedTopic['id_board'], $id_msg, $mostLikedTopic['like_count']) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if(!isset($id_msg) || empty($id_msg)) {
			$mostLikedTopic = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		} else {
			// Lets fetch few messages in the topic
			$mostLikedTopic['msg_data'] = $this->fetchTopicMessages(array('id_msg' => $id_msg));
		}
		return $mostLikedTopic;
	}

	private function fetchTopicMessages($data = array('id_msg' => '')) {
		global $smcFunc, $scripturl, $modSettings, $settings;

		$request = $smcFunc['db_query']('', '
			SELECT m.id_msg, m.body, m.poster_time, m.smileys_enabled, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.id_member, mem.real_name, mem.avatar
			FROM {db_prefix}messages as m
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
			WHERE m.id_msg IN ({raw:id_msg})
			ORDER BY m.id_msg
			LIMIT 10',
			array(
				'id_msg' => $data['id_msg']
			)
		);

		$msgData = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$msgData[] = array(
				'id_msg' => $row['id_msg'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
				'member' => array(
					'id_member' => $row['id_member'],
					'name' => $row['real_name'],
					'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
					'avatar' => LikePosts::$LikePostsUtils->obtainAvatar(array(
						'avatar' => $row['avatar'],
						'id_attach' => $row['id_attach'],
						'attachment_type' => $row['attachment_type'],
						'filename' => $row['filename']
					)),
				),
			);
		}
		$smcFunc['db_free_result']($request);
		return $msgData;
	}

	public function getStatsMostLikedBoard() {
		global $smcFunc, $txt;

		// Most liked board
		$mostLikedBoard = array();
		$request = $smcFunc['db_query']('', '
			SELECT t.id_board, b.name, b.num_topics, b.num_posts, count(DISTINCT(t.id_topic)) AS topics_liked, count(DISTINCT(lp.id_msg)) AS msgs_liked, SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT(CONVERT(t.id_topic, CHAR(8))) ORDER BY t.id_topic DESC SEPARATOR ","), ",", 10) AS id_topic, COUNT(t.id_board) AS like_count
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}topics AS t ON (t.id_first_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE {query_wanna_see_board}
			GROUP BY t.id_board
			ORDER BY like_count DESC
			LIMIT 1',
			array()
		);
		list ($mostLikedBoard['id_board'], $mostLikedBoard['name'], $mostLikedBoard['num_topics'], $mostLikedBoard['num_posts'], $mostLikedBoard['topics_liked'], $mostLikedBoard['msgs_liked'], $id_topics, $mostLikedBoard['like_count']) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if(!isset($id_topics) || empty($id_topics)) {
			$mostLikedBoard = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		} else {
			$mostLikedBoard['topic_data'] = $this->fetchBoardTopics(array('id_topics' => $id_topics));
		}
		return $mostLikedBoard;
	}

	private function fetchBoardTopics($data = array('id_topics' => '')) {
		global $smcFunc, $scripturl, $modSettings, $settings;

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
				'id_topics' => $data['id_topics']
			)
		);

		$topic_data = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$topic_data[] = array(
				'id_topic' => $row['id_topic'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
				'member' => array(
					'id_member' => $row['id_member'],
					'name' => $row['real_name'],
					'href' => $row['real_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
					'avatar' => LikePosts::$LikePostsUtils->obtainAvatar(array(
						'avatar' => $row['avatar'],
						'id_attach' => $row['id_attach'],
						'attachment_type' => $row['attachment_type'],
						'filename' => $row['filename']
					)),
				),
			);
		}
		$smcFunc['db_free_result']($request);
		return $topic_data;
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
					'avatar' => LikePosts::$LikePostsUtils->obtainAvatar(array(
						'avatar' => $row['avatar'],
						'id_attach' => $row['id_attach'],
						'attachment_type' => $row['attachment_type'],
						'filename' => $row['filename']
					)),
				),
				'like_count' => $row['like_count'],
			);
			$id_member = $row['id_member'];
		}
		$smcFunc['db_free_result']($request);

		if(!isset($id_member) || empty($id_member)) {
			$mostLikedMember = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		} else {
			$mostLikedMember['topic_data'] = $this->fetchMostLikedUserPosts(array('id_member' => $id_member));
		}
		return $mostLikedMember;
	}

	private function fetchMostLikedUserPosts($data = array('id_member' => '')) {
		global $smcFunc;

		// Lets fetch highest posts of user like by others
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_msg, m.id_topic, COUNT(lp.id_msg) AS like_count, m.subject, m.body, m.poster_time, m.smileys_enabled
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
			WHERE {query_wanna_see_board}
			AND lp.id_member_received = {int:id_member}
			GROUP BY lp.id_msg
			ORDER BY like_count DESC
			LIMIT 10',
			array(
				'id_member' => $data['id_member']
			)
		);

		$topic_data = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$topic_data[] = array(
				'id_topic' => $row['id_topic'],
				'id_msg' => $row['id_msg'],
				'like_count' => $row['like_count'],
				'subject' => $row['subject'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
			);
		}
		$smcFunc['db_free_result']($request);
		return $topic_data;
	}

	public function getStatsMostLikesGivenUser() {
		global $smcFunc, $scripturl, $modSettings, $settings, $txt;

		// Most liked board
		$mostLikeGivingMember = array();
		$request = $smcFunc['db_query']('', '
			SELECT lp.id_member_gave, COUNT(lp.id_msg) AS like_count, GROUP_CONCAT(DISTINCT(CONVERT(lp.id_msg, CHAR(8))) ORDER BY m.id_topic DESC SEPARATOR ",") AS id_msgs, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mem.real_name, mem.avatar, mem.date_registered, mem.posts
			FROM {db_prefix}like_post as lp
			INNER JOIN {db_prefix}members as mem ON (mem.id_member = lp.id_member_gave)
			INNER JOIN {db_prefix}messages as m ON (m.id_msg = lp.id_msg)
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
					'avatar' => LikePosts::$LikePostsUtils->obtainAvatar(array(
						'avatar' => $row['avatar'],
						'id_attach' => $row['id_attach'],
						'attachment_type' => $row['attachment_type'],
						'filename' => $row['filename']
					)),
				),
				'like_count' => $row['like_count'],
			);
			$id_msgs = $row['id_msgs'];
		}
		$smcFunc['db_free_result']($request);

		if(!isset($id_msgs) || empty($id_msgs)) {
			$mostLikeGivingMember = array(
				'noDataMessage' => $txt['lp_error_no_data']
			);
		} else {
			$mostLikeGivingMember['topic_data'] = $this->fetchMostLikedGivenUserPosts(array('id_msgs' => $id_msgs));
		}
		return $mostLikeGivingMember;
	}

	private function fetchMostLikedGivenUserPosts($data = array('id_msgs' => '')) {
		global $smcFunc;

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
				'id_msgs' => $data['id_msgs']
			)
		);

		$topic_data = array();
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);
			$msgString = LikePosts::$LikePostsUtils->trimContent($row['body'], ' ', 255);

			$topic_data[] = array(
				'id_msg' => $row['id_msg'],
				'id_topic' => $row['id_topic'],
				'subject' => $row['subject'],
				'body' => parse_bbc($msgString, $row['smileys_enabled'], $row['id_msg']),
				'poster_time' => timeformat($row['poster_time']),
			);
		}
		$smcFunc['db_free_result']($request);
		return $topic_data;
	}
}

?>
