<?php

/**
* @package manifest file for Like Posts
* @version 1.5
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

// If SSI.php is in the same place as this file, and SMF isn't defined...
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

// Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot uninstall - please verify you put this in the same place as DIALOGO\'s index.php.');

global $sourcedir, $smcFunc;

$like_post_permissions = array('like_post_enable', 'like_per_profile_page', 'like_in_notification',	'lp_show_like_on_boards');

$smcFunc['db_query']('', '
    DELETE FROM {db_prefix}settings
    WHERE variable IN ({array_string:like_post_permissions})',
    array(
        'like_post_permissions' => $like_post_permissions,
    )
);

remove_integration_function('integrate_pre_include', '$sourcedir/LikePostsHooks.php', true);
remove_integration_function('integrate_pre_include', '$sourcedir/LikePosts.php', true);
remove_integration_function('integrate_admin_areas', 'LP_addAdminPanel', true);
remove_integration_function('integrate_profile_areas', 'LP_addProfilePanel', true);
remove_integration_function('integrate_actions', 'LP_addAction', true);
remove_integration_function('integrate_load_theme', 'LP_includeAssets', true);
remove_integration_function('integrate_menu_buttons', 'LP_addMenu', true);

?>