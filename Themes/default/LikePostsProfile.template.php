<?php

/**
* @package manifest file for Like Posts
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

function template_lp_show_own_likes() {
	global $context, $settings, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['lp_like_you_gave'], '</h3>
		</div>';

		echo '
		<table class="table_grid" width="100%" cellspacing="0">
			<thead>
				<tr class="titlebg">
					<th class="lefttext first_th" scope="col" width="80%">', $txt['lp_post_info'], '</th>
					<th class="lefttext last_th" scope="col" width="20%">', $txt['lp_no_of_likes'], '</th>
				</tr>
			</thead>
			<tbody>';

	echo '
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		foreach ($context['like_post']['own_like_data'] as $key => $data) {
			echo '
				<tr>
					<td class="windowbg" title="', $data['id'], '">
						<a class="subject_heading" href="', $data['href'] ,'">
							', $data['subject'],'
						</a>
						<span class="hide_elem">
							', $data['body'],'
						</span>
						<br />
						<span class="smalltext">
							', $data['time'],'
						</span>
					</td>
					<td class="windowbg2 smalltext">';

				echo '
						<span>', $data['total_likes'],'</span>';

				echo '
					</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>
	</div>';
}

function template_lp_show_others_likes() {
	global $context, $settings, $scripturl, $txt;

	echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['lp_like_you_obtained'], '</h3>
		</div>';

		echo '
		<table class="table_grid" width="100%" cellspacing="0">
			<thead>
				<tr class="titlebg">
					<th class="lefttext first_th" scope="col" width="80%">', $txt['lp_post_info'], '</th>
					<th class="lefttext last_th" scope="col" width="20%">', $txt['lp_no_of_likes'], '</th>
				</tr>
			</thead>
			<tbody>';

		echo '
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		foreach ($context['like_post']['others_like_data'] as $key => $data) {
			echo '
				<tr>
					<td class="windowbg" title="', $data['id'], '">
						<a class="subject_heading" href="', $data['href'] ,'" title="">
							', $data['subject'],'
						</a>
						<span class="hide_elem">
							', $data['body'],'
						</span>
						<br />
						<span class="smalltext">', $data['time'], '</span>
					</td>
					<td class="windowbg2 smalltext" onclick="lpObj.showMessageLikedInfo(', $data['id'], ');">
						<span>
							', $data['total_likes'], '
						</span>
					</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>
	</div>';
}

?>
