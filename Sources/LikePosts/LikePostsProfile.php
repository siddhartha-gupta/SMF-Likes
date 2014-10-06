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
 

if (!defined('SMF'))
	die('Hacking attempt...');

class LikePostsProfile {
	public function __construct() {}

	public function getOwnLikes($memID) {
		global $context, $scripturl, $modSettings;

		$select = 'COUNT(*)';
		$where = 'lp.id_member_gave = ' . $memID;
		$context['total_visible_likes'] = isset($_REQUEST['total']) && !empty($_REQUEST['total']) ? (int) $_REQUEST['total'] : LikePosts::$LikePostsDB->getTotalResults($select, $where);

		$context['start'] = isset($_REQUEST['start']) && !empty($_REQUEST['start']) ? $_REQUEST['start']: 0;
		$context['start'] = !is_numeric($context['start']) ? 0 : $context['start'];

		// Give admin options for these
		$context['likes_per_page'] = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

		$context['like_post']['own_like_data'] = LikePosts::$LikePostsDB->getOwnLikes($memID, $context['start']);
		$context['page_index'] = constructPageIndex($scripturl . '?action=profile;area=likeposts;sa=seeownlikes;u=' . $memID .';total=' . $context['total_visible_likes'], $context['start'], $context['total_visible_likes'], $context['likes_per_page']);

		$context['sub_template'] = 'lp_show_own_likes';
		$context['page_title'] = $txt['lp_you_liked'];
	}

	public function getOthersLikes($memID) {
		global $context, $scripturl, $modSettings;

		$select = 'COUNT(DISTINCT(lp.id_msg))';
		$where = 'm.id_member = ' . $memID;
		$context['total_visible_likes'] = isset($_REQUEST['total']) && !empty($_REQUEST['total']) ? (int) $_REQUEST['total'] : LikePosts::$LikePostsDB->getTotalResults($select, $where);

		$context['start'] = isset($_REQUEST['start']) && !empty($_REQUEST['start']) ? $_REQUEST['start']: 0;
		$context['start'] = !is_numeric($context['start']) ? 0 : $context['start'];

		// Give admin options for these
		$context['likes_per_page'] = isset($modSettings['like_per_profile_page']) && !empty($modSettings['like_per_profile_page']) ? (int) $modSettings['like_per_profile_page'] : 10;

		$context['like_post']['others_like_data'] = LikePosts::$LikePostsDB->getOthersLikes($memID, $context['start']);
		$context['page_index'] = constructPageIndex($scripturl . '?action=profile;area=likeposts;sa=seeotherslikes;u=' . $memID .';total=' . $context['total_visible_likes'], $context['start'], $context['total_visible_likes'], $context['likes_per_page']);

		$context['sub_template'] = 'lp_show_others_likes';
		$context['page_title'] = $txt['lp_liked_by_others'];
	}
}

?>
