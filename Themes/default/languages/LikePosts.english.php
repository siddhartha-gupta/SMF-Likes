<?php

/**
* @package manifest file for Restrict Boards per post
* @version 2.0.2
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

global $txt;

//front end strings strings
$txt['lp_string_you_liked'] = 'You liked this';
$txt['lp_string_you'] = 'You &amp;';
$txt['lp_string_other_people_liked'] = '%1$d other person liked this';
$txt['lp_string_other_multiple_people_liked'] = '%1$d other people liked this';
$txt['lp_string_people_liked'] = '%1$d person liked this';
$txt['lp_string_multiple_people_liked'] = '%1$d people liked this';

$txt['lp_like'] = 'Like';
$txt['lp_unlike'] = 'Unlike';
$txt['lp_total_likes'] = 'Total likes';
$txt['like_show_notifications'] = 'Show Likes notification';

//Admin panel strings
$txt['lp_menu'] = 'Like Posts';
$txt['lp_admin_panel'] = 'Like Posts admin panel';
$txt['lp_general_settings'] = 'General Settings';
$txt['lp_general_settings_desc'] = 'You can make all global settings for like posts from here.';
$txt['lp_permission_settings'] = 'Permission Settings';
$txt['lp_permission_settings_desc'] = 'You can make all group based permission settings for like posts from here.';
$txt['lp_recount_stats'] = 'Recount Like Stats';
$txt['lp_recount_stats_desc'] = 'Recount stats of like for forum.';
$txt['lp_mod_enable'] = 'Enable Like Post mod';
$txt['lp_mod_enable_desc'] = 'Global permission to enable/disable mod';
$txt['lp_stats_enable'] = 'Enable stats for like posts';
$txt['lp_stats_enable_desc'] = 'Global permission to enable/disable mod stats';
$txt['lp_notification_enable'] = 'Enable notification for like posts';
$txt['lp_notification_enable_desc'] = 'Global permission to enable/disable mod notification';
$txt['lp_per_profile_page'] = 'No. of likes in profile';
$txt['lp_per_profile_page_desc'] = 'No. of likes to show in profile per page';
$txt['lp_in_notification'] = 'No. of likes in notification';
$txt['lp_in_notification_desc'] = 'No. of likes to show in notifications';
$txt['lp_show_like_on_boards'] = 'Show like button on board index';
$txt['lp_show_like_on_boards_desc'] = 'Global settings to show like button board index';
$txt['lp_show_total_like_in_posts'] = 'Show total likes in posts';
$txt['lp_show_total_like_in_posts_desc'] = 'Show total likes in posts under user avatar';
$txt['lp_regular_members'] = 'Regular Members';
$txt['lp_perm_lp_can_like_posts'] = 'Can like posts';
$txt['lp_perm_lp_can_view_likes'] = 'Can view likes';
$txt['lp_perm_lp_can_view_others_likes_profile'] = 'Can view likes of other users in their profiles';
$txt['lp_perm_lp_can_view_likes_stats'] = 'Can view like stats';
$txt['lp_perm_lp_can_view_likes_notification'] = 'Can view like posts notifications';
$txt['lp_board_settings'] = 'Board Settings';
$txt['lp_board_settings_desc'] = 'Select on which boards you want to activate the mod';
$txt['lp_recount_likes'] = 'Check likes table';
$txt['lp_check_likes'] = 'Optimize and remove likes from deleted topics';

$txt['lp_recount_likes'] = 'Check likes table';
$txt['lp_check_likes'] = 'Optimize and remove likes from deleted topics';

$txt['lp_remove_duplicate_likes'] = 'Remove duplicate likes';
$txt['lp_remove_duplicate_likes_desc'] = 'Removes duplicate entries from like table';

$txt['lp_recount_total_likes'] = 'Recount members total likes';
$txt['lp_reset_total_likes_received'] = 'Reset members total likes received';

// For our beloved guests
$txt['lp_guest_permissions'] = 'Permissions for guests';
$txt['lp_perm_lp_guest_can_view_likes_in_posts'] = 'Can view like posts in topics';
$txt['lp_perm_lp_guest_can_view_likes_in_boards'] = 'Can view like posts in boards';
$txt['lp_perm_lp_guest_can_view_likes_in_profiles'] = 'Can view like posts in profiles';
$txt['lp_perm_lp_guests_can_view_likes_stats'] = 'Can view like stats';

// Profile area strings
$txt['lp_you_liked'] = 'Likes given';
$txt['lp_liked_by_others'] = 'Likes received';
$txt['lp_like_you_gave'] = 'Posts you liked';
$txt['lp_like_you_obtained'] = 'Your posts liked by others';
$txt['lp_post_info']  = 'Post info';
$txt['lp_no_of_likes'] = 'No. of Likes';

$txt['lp_tab_title'] = 'See likes';
$txt['lp_tab_description'] = 'See likes given/taken';

// Like posts stats strings
$txt['lp_stats'] = 'Like stats';
$txt['like_posts_stats_desc'] = 'Stats related to like posts';
$txt['lp_tab_mlm'] = 'Most liked message';
$txt['lp_tab_mlt'] = 'Most liked topic';
$txt['lp_tab_mlb'] = 'Most liked board';
$txt['lp_tab_mlmember'] = 'Most Liked Member';
$txt['lp_tab_mlgmember'] = 'Most Like Giving user';
$txt['lp_generic_heading1'] = 'like(s) so far';

// For message
$txt['lp_users_who_liked'] = 'users who liked this post';

// For topic
$txt['lp_most_popular_topic_heading1'] = 'The most popular topic has received';
$txt['lp_most_popular_topic_sub_heading1'] = 'The topic contains';
$txt['lp_most_popular_topic_sub_heading2'] = 'different posts. Few of the liked posts from it';

// For board
$txt['lp_most_popular_board_heading1'] = 'has received';
$txt['lp_most_popular_board_sub_heading1'] = 'The board contains';
$txt['lp_most_popular_board_sub_heading2'] = 'different topics, out which';
$txt['lp_most_popular_board_sub_heading3'] = 'topics are liked.';
$txt['lp_most_popular_board_sub_heading4'] = 'Furthermore, these topic contains';
$txt['lp_most_popular_board_sub_heading5'] = 'different posts, out of which';
$txt['lp_most_popular_board_sub_heading6'] = 'posts are liked so far. Few of the liked topics from it';

// Most liked user
$txt['lp_total_likes_received'] = 'Total Likes Received';
$txt['lp_most_popular_user_heading1'] = 'Few of users most liked posts';

// Most like giving user
$txt['lp_total_likes_given'] = 'Total Likes Given';
$txt['lp_most_like_given_user_heading1'] = 'Few of users recently liked posts';

// Like posts generic strings
$txt['lp_topic'] = 'Topic';
$txt['lp_message'] = 'Message';
$txt['lp_board'] = 'Board';
$txt['lp_total_posts'] = 'Total posts';
$txt['lp_posted_at'] = 'Posted at';
$txt['lp_read_more'] = 'read more...';
$txt['lp_submit'] = 'Submit';
$txt['lp_select_all_boards'] = 'Select all boards';

// Error msgs
$txt['lp_cannot_like_posts'] = 'You do not have the permissions to like posts.';
$txt['lp_no_access'] = 'Oops! It looks like you are not allowed to access this section';
$txt['lp_error_something_wrong'] = 'Oops! There seems to be some server error. Please contact the site administrator.';
$txt['lp_error_no_data'] = 'Sorry, it looks like there is nothing to show yet!';

// Related to notification
$txt['lp_all_notification'] = 'All Notification';
$txt['lp_my_posts'] = 'My Posts';
$txt['lp_no_notification'] = 'Nothing to show at the moment';

?>
