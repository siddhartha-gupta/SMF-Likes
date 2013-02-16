<?php

/**
* @package manifest file for Like Posts
* @version 1.0 Alpha
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

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as DIALOGO\'s index.php.');

global $smcFunc, $db_prefix, $sourcedir;

if (!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$table = array(
	'table_name' => 'like_post',
	'columns' => array(
		array(
			'name' => 'id_msg',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'default' => '0',
		),
		array(
			'name' => 'id_topic',
			'type' => 'mediumint',
			'size' => 8,
			'unsigned' => true,
			'default' => '0',
		),
		array(
			'name' => 'id_board',
			'type' => 'smallint',
			'size' => 5,
			'unsigned' => true,
			'default' => '0',
		),
		array(
			'name' => 'id_member',
			'type' => 'mediumint',
			'size' => 8,
			'unsigned' => true,
			'default' => '0',
		),
		array(
			'name' => 'rating',
			'type' => 'smallint',
			'size' => 1,
			'unsigned' => true,
			'default' => '0',
		),
	),
	'indexes' => array(
        array(
            'type' => 'primary',
            'columns' => array('id_msg', 'id_member'),
        ),
    ),
);
$smcFunc['db_create_table']('{db_prefix}' . $table['table_name'], $table['columns'], $table['indexes']);

// For all general settings add 'like_post_' as prefix
updateSettings(array('like_post_enable' => 0));

add_integration_function('integrate_pre_include', '$sourcedir/LikePostsHooks.php');
add_integration_function('integrate_pre_include', '$sourcedir/LikePosts.php');
add_integration_function('integrate_admin_areas', 'LP_addAdminPanel');
add_integration_function('integrate_actions', 'LP_addAction', true);

//For 2.1
//add_integration_function('integrate_admin_areas', 'LP_addAdminPanel:$sourcedir/LikePostsHooks.php');
//add_integration_function('integrate_actions', 'LP_addAction:$sourcedir/LikePostsHooks.php');

if (SMF == 'SSI')
echo 'Database adaptation successful!';

?>