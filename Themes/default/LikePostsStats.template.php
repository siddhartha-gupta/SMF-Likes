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
	<div class="cat_bar">
		<h3 class="catbg">
			<span class="ie6_header floatleft">', $txt['like_post_stats'] ,'</span>
		</h3>
	</div>
	<p class="windowbg description">', $context['like_posts']['tab_desc'] ,'</p>';

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

	echo '
	<div class="cat_bar">
		<h3 class="catbg" id="like_post_current_tab"></h3>
	</div>';

	echo '
	<div class="like_post_stats_data">
	<div class="like_post_message_data"></div>
	<div class="like_post_topic_data"></div>
	<div class="like_post_board_data"></div>
	<div class="like_post_user_data"></div>
	</div>';
}

?>
