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

if (!defined('SMF'))
	die('Hacking attempt...');

function LP_insertLikePost($data) {
    global $smcFunc;

    if(!is_array($data)) {
        return false;
    }

    if($user_info['is_guest']) {
		return false;
	}

	//$replaceArray[] = array($user_info['id'], $timezoneID);
	$smcFunc['db_insert']('replace',
		'{db_prefix}live_clock_user_zone',
		array('id_msg' => 'int', 'id_topic' => 'int', '	id_board' => 'int', 'id_member' => 'int'),
		$data,
		array()
	);
	return true;
}

?>