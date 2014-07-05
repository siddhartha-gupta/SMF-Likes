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

function template_lp_stats() {
	global $context, $txt, $sourcedir, $settings, $user_info, $options, $scripturl;

	echo '
	<div class="like_post_stats">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft">', $txt['like_post_stats'] ,'</span>
			</h3>
		</div>
		<p class="windowbg description">', $context['like_posts']['tab_desc'] ,'</p>';

	if(empty($context['like_post_stats_error'])) {
		echo '
			<div id="adm_submenus" class="like_post_stats_menu">
				<ul class="dropmenu">';
			
				// Print out all the items in this tab.
				foreach ($context['lp_stats_tabs'] as $sa => $tab) {
					echo '
					<li>
						<a class="firstlevel" href="" id="', $tab['id'],'"><span class="firstlevel">', $tab['label'], '</span></a>
					</li>';
				}

		echo '
				</ul>
			</div><br class="clear" />';
	}

	if(empty($context['like_post_stats_error'])) {
		echo '
			<div class="cat_bar">
				<h3 class="catbg" id="like_post_current_tab"></h3>
			</div>';

		echo '
			<div class="like_post_stats_data">
				<div class="individual_data like_post_message_data"></div>
				<div class="individual_data like_post_topic_data"></div>
				<div class="individual_data like_post_board_data"></div>
				<div class="individual_data like_post_most_liked_user_data"></div>
				<div class="individual_data like_post_most_likes_given_user_data"></div>
			</div>';

		echo '
			<div id="like_post_stats_overlay"></div>
			<div id="lp_preloader"></div>';
	} else {
		echo '
		<div id="admincenter">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
					<div class="content">', $context['like_post_stats_error'] ,'</div>
				<span class="botslice"><span></span></span>
			</div>
		</div>';
	}

	echo '
	</div>';

	echo '<script type="text/javascript"><!-- // --><![CDATA[
		window.onload = function() {
			likePostStats.prototype.init({
				onError: ' . JavaScriptEscape($context['like_post_stats_error']) . ',
				txtStrings: {
					mostLikedMessage: ' . JavaScriptEscape($txt['like_post_tab_mlm']) . ',
					mostLikedTopic: ' . JavaScriptEscape($txt['like_post_tab_mlt']) . ',
					mostLikedBoard: ' . JavaScriptEscape($txt['like_post_tab_mlb']) . ',
					topic: ' . JavaScriptEscape($txt['like_post_topic']) . ',
					message: ' . JavaScriptEscape($txt['like_post_message']) . ',
					board: ' . JavaScriptEscape($txt['like_post_board']) . ',
					totalPosts: ' . JavaScriptEscape($txt['like_post_total_posts']) . ',
					postedAt: ' . JavaScriptEscape($txt['like_post_posted_at']) . ',
					readMore: ' . JavaScriptEscape($txt['like_post_read_more']) . ',
					genricHeading1: ' . JavaScriptEscape($txt['like_post_generic_heading1']) . ',

					usersWhoLiked: ' . JavaScriptEscape($txt['like_post_users_who_liked']) . ',
					mostPopularTopicHeading1: ' . JavaScriptEscape($txt['like_post_most_popular_topic_heading1']) . ',
					mostPopularTopicSubHeading1: ' . JavaScriptEscape($txt['like_post_most_popular_topic_sub_heading1']) . ',
					mostPopularTopicSubHeading2: ' . JavaScriptEscape($txt['like_post_most_popular_topic_sub_heading2']) . ',
					mostPopularBoardHeading1: ' . JavaScriptEscape($txt['like_post_most_popular_board_heading1']) . ',
					mostPopularBoardSubHeading1: ' . JavaScriptEscape($txt['like_post_most_popular_board_sub_heading1']) . ',
					mostPopularBoardSubHeading2: ' . JavaScriptEscape($txt['like_post_most_popular_board_sub_heading2']) . ',
					mostPopularBoardSubHeading3: ' . JavaScriptEscape($txt['like_post_most_popular_board_sub_heading3']) . ',
					mostPopularBoardSubHeading4: ' . JavaScriptEscape($txt['like_post_most_popular_board_sub_heading4']) . ',
					mostPopularBoardSubHeading5: ' . JavaScriptEscape($txt['like_post_most_popular_board_sub_heading5']) . ',
					mostPopularBoardSubHeading6: ' . JavaScriptEscape($txt['like_post_most_popular_board_sub_heading6']) . ',
				}
			});
		}
	// ]]></script>';
}

?>
