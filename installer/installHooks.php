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

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF')) {
	require_once (dirname(__FILE__) . '/SSI.php');
} elseif (!defined('SMF')) {
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
}

// Add hooks and plugin the mod
add_integration_function('integrate_pre_include', '$sourcedir/LikePosts/LikePosts.php');
add_integration_function('integrate_actions', 'LikePosts::addActionContext', true);
add_integration_function('integrate_load_theme', 'LikePosts::includeAssets', true);
add_integration_function('integrate_menu_buttons', 'LikePosts::addMenuItems');
add_integration_function('integrate_admin_areas', 'LikePosts::addAdminPanel');
add_integration_function('integrate_profile_areas', 'LikePosts::addProfilePanel');

?>
