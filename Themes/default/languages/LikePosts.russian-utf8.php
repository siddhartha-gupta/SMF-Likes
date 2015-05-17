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
$txt['lp_total_likes'] = 'Лайков';
$txt['like_show_notifications'] = 'Уведомления о новых лайках';

//Admin panel strings
$txt['lp_menu'] = 'Лайки к сообщениям';
$txt['lp_admin_panel'] = 'Административная панель к Like Posts';
$txt['lp_general_settings'] = 'Общие установки';
$txt['lp_general_settings_desc'] = 'Здесь вы можете выбрать глобальные настройки мода лайков к сообщениям.';
$txt['lp_permission_settings'] = 'Установки прав пользователей';
$txt['lp_permission_settings_desc'] = 'Здесь вы можете назначить права группам по раздаче и просмотру лайков к сообщениям в этом форуме.';
$txt['lp_recount_stats'] = 'Обнулить статистику по лайкам';
$txt['lp_recount_stats_desc'] = 'Обнулить статистику по лайкам для форума.';
$txt['lp_mod_enable'] = 'Включить мод лайков к сообщениям';
$txt['lp_mod_enable_desc'] = 'Глобальная установка включения/выключения мода';
$txt['lp_stats_enable'] = 'Включить показ статистки по лайкам';
$txt['lp_stats_enable_desc'] = 'Глобальная установка прав доступа к статистике мода лайков';
$txt['lp_notification_enable'] = 'Показывать уведомления о новых лайках';
$txt['lp_notification_enable_desc'] = 'Глобальная установка прав на просмотр уведомлений о новых лайках';
$txt['lp_per_profile_page'] = 'Количество лайков, показываемых в профиле пользователя';
$txt['lp_per_profile_page_desc'] = 'Количество лайков, показываемых на странице профиля пользователя';
$txt['lp_in_notification'] = 'Количество лайков в уведомлении';
$txt['lp_in_notification_desc'] = 'Количество лайков в уведомлении о новых лайках';
$txt['lp_show_like_on_boards'] = 'Показывать кнопку лайк в списке тем';
$txt['lp_show_like_on_boards_desc'] = 'Глобальная установка показа кнопки лайк в списке тем';
$txt['lp_show_total_like_in_posts'] = 'Показывать общее количество лайков в сообщении';
$txt['lp_show_total_like_in_posts_desc'] = 'Показывать общее количество лайков в сообщении под аватой пользователя';
$txt['lp_regular_members'] = 'Зарегистрированные пользователи';
$txt['lp_perm_lp_can_like_posts'] = 'Могут раздавать лайки';
$txt['lp_perm_lp_can_view_likes'] = 'Могут смотреть кто лайкнул';
$txt['lp_perm_lp_can_view_others_likes_profile'] = 'Могут просматривать информацию о лайках в профилях других пользователей';
$txt['lp_perm_lp_can_view_likes_stats'] = 'Могут просматривать статистику по лайкам';
$txt['lp_perm_lp_can_view_likes_notification'] = 'Могут просматривать уведомления о новых лайках';
$txt['lp_board_settings'] = 'Установки для разделов';
$txt['lp_board_settings_desc'] = 'Выберите разделы форума, в которых хотите активировать мод';
$txt['lp_recount_likes'] = 'Проверить таблицу с лайками';
$txt['lp_check_likes'] = 'Оптимизирова и удалить лайки от удалённых сообщений';


$txt['lp_remove_duplicate_likes'] = 'Удалить дубликаты лайков';
$txt['lp_remove_duplicate_likes_desc'] = 'Удалить дубликаты лайков из таблицы лайков';

$txt['lp_recount_total_likes'] = 'Обнулить общее количество лайков у пользователей';
$txt['lp_reset_total_likes_received'] = 'Обнулить количество лайков, полученных пользователями';

// For our beloved guests
$txt['lp_guest_permissions'] = 'Права доступа для гостей';
$txt['lp_perm_lp_guest_can_view_likes_in_posts'] = 'Могут просматривать лайки к сообщениям в темах';
$txt['lp_perm_lp_guest_can_view_likes_in_boards'] = 'Могут просматривать лайки к сообщениям в разделах';
$txt['lp_perm_lp_guest_can_view_likes_in_profiles'] = 'Могут просматривать информациф о лайках в профилях пользователей';
$txt['lp_perm_lp_guests_can_view_likes_stats'] = 'Могут просматривать статистику по лайкам';

// Profile area strings
$txt['lp_you_liked'] = 'Роздано лайков';
$txt['lp_liked_by_others'] = 'Получено лайков';
$txt['lp_like_you_gave'] = 'Сообщения, которые вы лайкнули';
$txt['lp_like_you_obtained'] = 'Ваши сообщения, лайкнутые другими';
$txt['lp_post_info']  = 'Информация о сообщении';
$txt['lp_no_of_likes'] = 'Число лайков';

$txt['lp_tab_title'] = 'Просмотр лайков';
$txt['lp_tab_description'] = 'Просмотр лайков полученных/розданных';

// Like posts stats strings
$txt['lp_stats'] = 'Статистика лайков';
$txt['like_posts_stats_desc'] = 'Статистика по лайкам сообщений';
$txt['lp_tab_mlm'] = 'Сообщение, получившее наибольшее число лайков ';
$txt['lp_tab_mlt'] = 'Тема, получившая наибольшее число лайков';
$txt['lp_tab_mlb'] = 'Раздел, получивший наибольшее число лайков';
$txt['lp_tab_mlmember'] = 'Пользователь, получивший наибольшее число лайков';
$txt['lp_tab_mlgmember'] = 'Пользователь, раздавший наибольшее число лайков';
$txt['lp_generic_heading1'] = 'лайков';

// For message
$txt['lp_users_who_liked'] = 'лайкнули';

// For topic
$txt['lp_most_popular_topic_heading1'] = 'Самая популярная тема получила';
$txt['lp_most_popular_topic_sub_heading1'] = 'Тема содержит';
$txt['lp_most_popular_topic_sub_heading2'] = 'сообщения с лайками. Часть из них приведены ниже';

// For board
$txt['lp_most_popular_board_heading1'] = 'получил';
$txt['lp_most_popular_board_sub_heading1'] = 'Раздел форума содержит';
$txt['lp_most_popular_board_sub_heading2'] = 'тем, из которых';
$txt['lp_most_popular_board_sub_heading3'] = 'тем было лайкнуто.';
$txt['lp_most_popular_board_sub_heading4'] = 'Кроме того, эти темы содержат';
$txt['lp_most_popular_board_sub_heading5'] = 'сообщений, из которых';
$txt['lp_most_popular_board_sub_heading6'] = 'сообщений были лайкнуты. Часть из них приведены ниже';

// Most liked user
$txt['lp_total_likes_received'] = 'Всего получено лайков';
$txt['lp_most_popular_user_heading1'] = 'Пользователь, получивший наибольшее количество лайков';

// Most like giving user
$txt['lp_total_likes_given'] = 'Всего роздано лайков';
$txt['lp_most_like_given_user_heading1'] = 'Пользователи, раздавшие наибольшее количество лайков';

// Like posts generic strings
$txt['lp_topic'] = 'Тема';
$txt['lp_message'] = 'Сообщение';
$txt['lp_board'] = 'Раздел';
$txt['lp_total_posts'] = 'Сообщений';
$txt['lp_posted_at'] = 'Опубликовано';
$txt['lp_read_more'] = 'читать больше...';
$txt['lp_submit'] = 'Сохранить';
$txt['lp_select_all_boards'] = 'Выбрать все разделы';

// Error msgs
$txt['lp_cannot_like_posts'] = 'У вас нет доступа к функции лайк.';
$txt['lp_no_access'] = 'Упс! Похоже у вас нет прав на доступ к этому разделу';
$txt['lp_error_something_wrong'] = 'Упс! Возможно произошла ошибка на сервере. Пожалуйста, обратитесь к администратору сайта.';
$txt['lp_error_no_data'] = 'Извините, пока нет ничего к показу!';

// Related to notification
$txt['lp_all_notification'] = 'Все уведомления';
$txt['lp_my_posts'] = 'К моим темам';
$txt['lp_no_notification'] = 'Нет на текущий момент';

?>
