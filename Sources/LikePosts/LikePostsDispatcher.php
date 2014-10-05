<?php

// /**
//  * @package manifest file for Like Posts
//  * @version 1.6.1
//  * @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
//  * @copyright Copyright (c) 2014, Siddhartha Gupta
//  * @license http://www.mozilla.org/MPL/MPL-1.1.html
//  */


//  * Version: MPL 1.1
//  *
//  * The contents of this file are subject to the Mozilla Public License Version
//  * 1.1 (the "License"); you may not use this file except in compliance with
//  * the License. You may obtain a copy of the License at
//  * http://www.mozilla.org/MPL/
//  *
//  * Software distributed under the License is distributed on an "AS IS" basis,
//  * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
//  * for the specific language governing rights and limitations under the
//  * License.
//  *
//  * The Initial Developer of the Original Code is
//  *  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
//  * Portions created by the Initial Developer are Copyright (C) 2012
//  * the Initial Developer. All Rights Reserved.
//  *
//  * Contributor(s): Big thanks to all contributor(s)
//  * emanuele45 (https://github.com/emanuele45)
//  *

if (!defined('SMF')) {
	die('Hacking attempt...');
}

class LikePostsDispatcher {
	public function __construct() {}

	public function dispatchLikes() {
		LikePosts::loadClass('LikeUnlikePosts');

		$subActions = array(
			'like_post' => 'likeUnlikePostsHandler'
		);

		if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikeUnlikePosts, $subActions[$_REQUEST['sa']]))
			return LikePosts::$LikeUnlikePosts->$subActions[$_REQUEST['sa']]();
	}

	public function dispatchLikesData() {
		LikePosts::loadClass('LikePostsData');

		$subActions = array(
			'get_message_like_info' => 'getMessageLikeInfo',
			'get_all_messages_info' => 'LP_getAllMessagesInfo',
			'get_all_topics_info' => 'LP_getAllTopicsInfo',
			'like_posts_notification'=> 'LP_getAllNotification'
		);

		if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && method_exists(LikePosts::$LikePostsData, $subActions[$_REQUEST['sa']]))
			return LikePosts::$LikePostsData->$subActions[$_REQUEST['sa']]();
	}

	public function dispatchLikeStats() {

	}
}

?>
