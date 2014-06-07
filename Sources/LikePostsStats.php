<?php

/**
* @package manifest file for Like Posts
* @version 1.4
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

function LP_statsMainIndex() {
	global $context, $txt, $sourcedir, $settings, $user_info;

	loadtemplate('LikePostsStats');
	require_once($sourcedir . '/Subs-LikePosts.php');

	$context['like_stats_most_liked_message'] = LP_DB_getStatsMostLikedMessage();
	$context['like_stats_most_liked_topic'] = LP_DB_getStatsMostLikedTopic();
	$context['like_stats_most_liked_board'] = LP_DB_getStatsMostLikedBoard();

	$context['sub_template'] = 'lp_stats';
}

?>
