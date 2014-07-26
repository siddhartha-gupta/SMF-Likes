<?php

/**
* @package manifest file for Like Posts
* @version 1.5.2
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

/** 
* Note - I don't recommend using this file anywhere
* Use this at your risk. I don't hold any responsibility if anything happens to you website by using this file.
* This file is completely intended to be use by mod developer as a utility for developing the mod
*/

require_once('SSI.php');

function initGPBPConverter() {
	global $sourcedir;

	require_once($sourcedir . '/Security.php');
	isAllowedTo('admin_forum');

	$subActions = array(
		'convert' => 'convertToLike',
	);

	//wakey wakey, call the func you lazy
	if (isset($_REQUEST['action']) && isset($subActions[$_REQUEST['action']]) && function_exists($subActions[$_REQUEST['action']]))
		return $subActions[$_REQUEST['action']]();
	else
		echo 'check the url';
}

function convertToLike() {
	global $smcFunc;

}

initGPBPConverter();

?>
