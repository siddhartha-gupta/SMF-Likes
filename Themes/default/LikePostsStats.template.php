<?php

/**
* @package manifest file for Like Posts
* @version 2.0.5
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

function template_lp_stats() {
	global $context, $txt, $sourcedir, $settings, $user_info, $options, $scripturl;

	echo '
	<div class="like_post_stats">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft">', $txt['lp_stats'] ,'</span>
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
				<div class="individual_data like_post_stats_error"></div>
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
			lpObj.likePostStats.init({
				onError: ' . JavaScriptEscape($context['like_post_stats_error']) . ',
				txtStrings: {
					topic: ' . JavaScriptEscape($txt['lp_topic']) . ',
					message: ' . JavaScriptEscape($txt['lp_message']) . ',
					board: ' . JavaScriptEscape($txt['lp_board']) . ',
					totalPosts: ' . JavaScriptEscape($txt['lp_total_posts']) . ',
					postedAt: ' . JavaScriptEscape($txt['lp_posted_at']) . ',
					readMore: ' . JavaScriptEscape($txt['lp_read_more']) . ',
					genricHeading1: ' . JavaScriptEscape($txt['lp_generic_heading1']) . ',
					totalLikesReceived: ' . JavaScriptEscape($txt['lp_total_likes_received']) . ',
					mostLikedMessage: ' . JavaScriptEscape($txt['lp_tab_mlm']) . ',
					mostLikedTopic: ' . JavaScriptEscape($txt['lp_tab_mlt']) . ',
					mostLikedBoard: ' . JavaScriptEscape($txt['lp_tab_mlb']) . ',
					mostLikedMember: ' . JavaScriptEscape($txt['lp_tab_mlmember']) . ',
					mostLikeGivingMember: ' . JavaScriptEscape($txt['lp_tab_mlgmember']) . ',
					usersWhoLiked: ' . JavaScriptEscape($txt['lp_users_who_liked']) . ',
					mostPopularTopicHeading1: ' . JavaScriptEscape($txt['lp_most_popular_topic_heading1']) . ',
					mostPopularTopicSubHeading1: ' . JavaScriptEscape($txt['lp_most_popular_topic_sub_heading1']) . ',
					mostPopularTopicSubHeading2: ' . JavaScriptEscape($txt['lp_most_popular_topic_sub_heading2']) . ',
					mostPopularBoardHeading1: ' . JavaScriptEscape($txt['lp_most_popular_board_heading1']) . ',
					mostPopularBoardSubHeading1: ' . JavaScriptEscape($txt['lp_most_popular_board_sub_heading1']) . ',
					mostPopularBoardSubHeading2: ' . JavaScriptEscape($txt['lp_most_popular_board_sub_heading2']) . ',
					mostPopularBoardSubHeading3: ' . JavaScriptEscape($txt['lp_most_popular_board_sub_heading3']) . ',
					mostPopularBoardSubHeading4: ' . JavaScriptEscape($txt['lp_most_popular_board_sub_heading4']) . ',
					mostPopularBoardSubHeading5: ' . JavaScriptEscape($txt['lp_most_popular_board_sub_heading5']) . ',
					mostPopularBoardSubHeading6: ' . JavaScriptEscape($txt['lp_most_popular_board_sub_heading6']) . ',
					mostPopularUserHeading1: ' . JavaScriptEscape($txt['lp_most_popular_user_heading1']) . ',
					likesReceived: ' . JavaScriptEscape($txt['lp_liked_by_others']) . ',
					totalLikesGiven: ' . JavaScriptEscape($txt['lp_total_likes_given']) . ',
					mostLikeGivenUserHeading1: ' . JavaScriptEscape($txt['lp_most_like_given_user_heading1']) . ',
				}
			});
		}
	// ]]></script>';
}

?>
