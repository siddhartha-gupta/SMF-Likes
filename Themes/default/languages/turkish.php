<?php

/**
* @package manifest file for Restrict Boards per post
* @version 2.0.3
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
$txt['lp_string_you_liked'] = 'Bunu beğendiniz';
$txt['lp_string_you'] = 'Sen &amp;';
$txt['lp_string_other_people_liked'] = '%1$d başka kişi bunu beğendi';
$txt['lp_string_other_multiple_people_liked'] = '%1$d başka kişi bunu beğendi';
$txt['lp_string_people_liked'] = '%1$d kişi bunu beğendi';
$txt['lp_string_multiple_people_liked'] = '%1$d kişi bunu beğendi';

$txt['lp_like'] = 'Beğen';
$txt['lp_unlike'] = 'Beğenmekten vazgeç';
$txt['lp_total_likes'] = 'Toplam beğeniler';
$txt['like_show_notifications'] = 'Beğeni bildirimini göster';

//Admin panel strings
$txt['lp_menu'] = 'Like Posts';
$txt['lp_admin_panel'] = 'Like Posts admin paneli';
$txt['lp_general_settings'] = 'Genel Ayarlar';
$txt['lp_general_settings_desc'] = 'You can make all global settings for like posts from here.';
$txt['lp_permission_settings'] = 'İzin Ayarları';
$txt['lp_permission_settings_desc'] = 'You can make all group based permission settings for like posts from here.';
$txt['lp_recount_stats'] = 'Beğenileri Yeniden Say';
$txt['lp_recount_stats_desc'] = 'Recount stats of like for forum.';
$txt['lp_mod_enable'] = 'İleti beğen modunu etkinleştir';
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
$txt['lp_you_liked'] = 'Verilen beğeni';
$txt['lp_liked_by_others'] = 'Alınan beğeni';
$txt['lp_like_you_gave'] = 'Beğendiğiniz iletiler';
$txt['lp_like_you_obtained'] = 'İletileriniz beğenildi';
$txt['lp_post_info']  = 'İleti bilgisi';
$txt['lp_no_of_likes'] = 'Beğeni sayısı';

$txt['lp_tab_title'] = 'Beğenileri gör';
$txt['lp_tab_description'] = 'Verilen/alınan beğenileri gör';

// Like posts stats strings
$txt['lp_stats'] = 'Beğeniler';
$txt['like_posts_stats_desc'] = 'İstatistikler ileti beğenileriyle bağlantılıdır';
$txt['lp_tab_mlm'] = 'En çok beğenilen mesaj';
$txt['lp_tab_mlt'] = 'En çok beğenilen konu';
$txt['lp_tab_mlb'] = 'En çok beğenilen kategori';
$txt['lp_tab_mlmember'] = 'En çok beğenilen üye';
$txt['lp_tab_mlgmember'] = 'En çok beğenen kullanıcı';
$txt['lp_generic_heading1'] = 'beğeni aldı';

// For message
$txt['lp_users_who_liked'] = 'kullanıcı (bu iletiyi beğenen)';

// For topic
$txt['lp_most_popular_topic_heading1'] = 'En çok beğeniye sahip konu şimdiye kadar';
$txt['lp_most_popular_topic_sub_heading1'] = 'Konu';
$txt['lp_most_popular_topic_sub_heading2'] = 'farklı iletiden oluşuyor. Beğenilen birkaç ileti';

// For board
$txt['lp_most_popular_board_heading1'] = 'kategorisinin aldığı beğeni sayısı:';
$txt['lp_most_popular_board_sub_heading1'] = 'Kategori';
$txt['lp_most_popular_board_sub_heading2'] = 'farklı konu içeriyor, bunlardan';
$txt['lp_most_popular_board_sub_heading3'] = 'konu beğenildi.';
$txt['lp_most_popular_board_sub_heading4'] = 'Ayrıca, bu konu';
$txt['lp_most_popular_board_sub_heading5'] = 'farklı iletiden oluşuyor, bunlardan';
$txt['lp_most_popular_board_sub_heading6'] = 'ileti beğenildi. Beğenilen birkaç konu';

// Most liked user
$txt['lp_total_likes_received'] = 'Aldığı Beğeni Sayısı';
$txt['lp_most_popular_user_heading1'] = 'Birkaç kullanıcının en son beğendiği iletiler';

// Most like giving user
$txt['lp_total_likes_given'] = 'Verdiği Beğeni Sayısı';
$txt['lp_most_like_given_user_heading1'] = 'Birkaç kullanıcının son zamanlarda beğendiği iletiler';

// Like posts generic strings
$txt['lp_topic'] = 'Konu';
$txt['lp_message'] = 'Mesaj';
$txt['lp_board'] = 'Kategori';
$txt['lp_total_posts'] = 'Toplam ileti';
$txt['lp_posted_at'] = 'Gönderilme zamanı';
$txt['lp_read_more'] = 'daha fazla...';
$txt['lp_submit'] = 'Gönder';
$txt['lp_select_all_boards'] = 'Tüm kategorileri seç';

// Error msgs
$txt['lp_cannot_like_posts'] = 'İletileri beğenmek için izniniz yok.';
$txt['lp_no_access'] = 'Hey! Bu bölüme erişim izniniz yok';
$txt['lp_error_something_wrong'] = 'Hey! Sunucu hatası var gibi gözüküyor. Lütfen yöneticiyle iletişime geçin.';
$txt['lp_error_no_data'] = 'Üzgünüm, şu an gösterilecek bir şey yok!';

// Related to notification
$txt['lp_all_notification'] = 'Tüm Bildirimler';
$txt['lp_my_posts'] = 'İletilerim';
$txt['lp_no_notification'] = 'Şu an da gösterilecek bir şey yok';

?>
