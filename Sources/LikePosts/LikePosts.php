<?php

/**
 *
 *
 * @package manifest file for Like Posts
 * @version 1.6.1
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

if (!defined('SMF'))
	die('Hacking attempt...');

class LikePosts {
	protected static $instance;

	public static $sourceFolder = '/LikePosts/';

	public static $LikePostsUtils;
	public static $LikePostsDB;
	public static $LikePostsData;
	public static $LikePostsDispatcher;
	public static $LikeUnlikePosts;
	public static $LikePostsStats;

	/**
	 * Singleton method
	 *
	 * @return void
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new LikePosts();
			loadLanguage('LikePosts');
			self::loadClass('LikePostsUtils');
			self::loadClass('LikePostsDB');
		}
		return self::$instance;

		// loadtemplate('LikePosts');
	}

	public function __construct() {}

	public static function loadClass($className) {
		global $sourcedir;

		switch($className) {
			case 'LikePostsUtils':
				if (self::$LikePostsUtils === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsUtils = new LikePostsUtils();
				}
				break;

			case 'LikePostsDB':
				if (self::$LikePostsDB === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsDB = new LikePostsDB();
				}
				break;

			case 'LikePostsData': 
				if (self::$LikePostsData === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsData = new LikePostsData();
				}
				break;

			case 'LikePostsDispatcher':
				if (self::$LikePostsDispatcher === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsDispatcher = new LikePostsDispatcher();
				}
				break;

			case 'LikeUnlikePosts':
				if (self::$LikeUnlikePosts === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikeUnlikePosts = new LikeUnlikePosts();
				}
				break;

			case 'LikePostsStats':
				if (self::$LikePostsStats === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsStats = new LikePostsStats();
				}
				break;

			default:
				break;
		}
	}

	public static function addActionContext(&$actions) {
		global $sourcedir;

		self::loadClass('LikePostsDispatcher');

		$actions['likeposts'] = array(self::$sourceFolder . 'LikePostsDispatcher.php', 'LikePostsDispatcher::dispatchLikes');
		$actions['likepostsstats'] = array(self::$sourceFolder . 'LikePostsDispatcher.php', 'LikePostsDispatcher::dispatchLikeStats');
	}

	public static function includeAssets() {
		global $context, $settings;

		$context['insert_after_template'] .= '
		<script type="text/javascript"><!-- // --><![CDATA[
			var lpLoaded = false,
			inConflict = false;

			function compareJQueryVersion(v1, v2, callback) {
				var v1parts = v1.split(' . ');
				var v2parts = v2.split(' . ');

				for (var i = 0; i < v1parts.length; ++i) {
					if (v2parts.length == i) {
						//v1 + " is larger"
						callback(1);
						return;
					}

					if (v1parts[i] == v2parts[i]) {
						continue;
					} else if (v1parts[i] > v2parts[i]) {
						//v1 + " is larger";
						callback(1);
						return;
					} else {
						//v2 + " is larger";
						callback(2);
						return;
					}
				}

				if (v1parts.length != v2parts.length) {
					//v2 + " is larger";
					callback(2);
					return;
				}
				callback(false);
				return;
			}

			function loadJquery(url, callback) {
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = url;

				var head = document.getElementsByTagName("head")[0],
					done = false;

				script.onload = script.onreadystatechange = function() {
					if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
						done = true;
						callback();
						script.onload = script.onreadystatechange = null;
						head.removeChild(script);
					};
				};
				head.appendChild(script);
			}

			// Only do anything if jQuery isn"t defined
			if (typeof(jQuery) == "undefined") {
				console.log("jquery not found");
				if (typeof($) == "function") {
					console.log("jquery but in conflict");
					inConflict = true;
				}

				loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
					if (typeof(jQuery) !=="undefined") {
						console.log("directly loaded with version: " + jQuery.fn.jquery);
						lp_jquery2_0_3 = jQuery.noConflict(true);
						loadLPScript();
					}
				});
			} else {
				// jQuery is already loaded
				console.log("jquery is already loaded with version: " + jQuery.fn.jquery);
				compareJQueryVersion(jQuery.fn.jquery, "2.0.3", function(result) {
					console.log("result of version check: " + result)
					switch(result) {
						case false:
						case 1:
							lp_jquery2_0_3 = jQuery.noConflict(true);
							loadLPScript();
							break;

						case 2:
							loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
								if (typeof(jQuery) !=="undefined") {
									console.log("after version check loaded with version: " + jQuery.fn.jquery);
									lp_jquery2_0_3 = jQuery.noConflict(true);
									loadLPScript();
								}
							});
							break;

						default:
							loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
								if (typeof(jQuery) !=="undefined") {
									console.log("default version check loaded with version: " + jQuery.fn.jquery);
									lp_jquery2_0_3 = jQuery.noConflict(true);
									loadLPScript();
								}
							});
							break;
					}
				})
			};

			function loadLPScript() {
				var js = document.createElement("script");
				js.type = "text/javascript";
				js.src = "' . $settings['default_theme_url'] . '/scripts/LikePosts/LikePosts.js";
				document.body.appendChild(js);

				var isLPStatsPage = (window.location.href.indexOf("likepostsstats") >= 0) ? true : false;

				if(isLPStatsPage) {
					var statsJS = document.createElement("script");
					statsJS.type = "text/javascript";
					statsJS.src = "' . $settings['default_theme_url'] . '/scripts/LikePosts/LikePostStats.js";
					document.body.appendChild(statsJS);
				}
			}
		// ]]></script>';

		self::$LikePostsUtils->checkJsonEncodeDecode();
	}

	public static function addMenuItems(&$menu_buttons) {
		global $scripturl, $txt, $user_info, $modSettings;

		$isAllowedToAccess = true;
		if (!isset($modSettings['like_post_enable']) || empty($modSettings['like_post_enable'])) {
			$isAllowedToAccess = false;
		}

		if ($user_info['is_guest'] && !self::$LikePostsUtils->isAllowedTo(array('guests_can_view_likes_stats'))) {
			$isAllowedToAccess = false;
		}
		if (!self::$LikePostsUtils->isAllowedTo(array('can_view_likes_stats'))) {
			$isAllowedToAccess = false;
		}

		if ($isAllowedToAccess) {
			// insert before logout
			$initPos = 0;
			reset($menu_buttons);
			while ((list($key, $val) = each($menu_buttons)) && $key != 'logout')
			$initPos++;

			$menu_buttons = array_merge(
				array_slice($menu_buttons, 0, $initPos),
				array(
					'like_post_stats' => array(
						'title' => $txt['lp_stats'],
						'href' => $scripturl . '?action=likepostsstats',
						'show' => true,
					),
				),
				array_slice($menu_buttons, $initPos, count($menu_buttons) - $initPos)
			);
		}
	}

	public static function addAdminPanel(&$admin_areas) {
		global $sourcedir, $txt;

		$admin_areas['config']['areas']['likeposts'] = array(
			'label' => $txt['lp_menu'],
			'file' => '/LikePosts/LikePostsAdmin.php',
			'function' => 'LikePostsAdminIndex',
			'icon' => 'administration.gif',
			'subsections' => array(),
		);
	}

	public static function addProfilePanel(&$profile_areas) {
		global $txt, $user_info, $modSettings;

		if ($user_info['is_guest'] && !LP_isAllowedTo(array('can_view_likes_in_profiles'))) {
			return false;
		}

		if (isset($_REQUEST['u']) && is_numeric($_REQUEST['u'])) {
			if ($user_info['id'] !== $_REQUEST['u']) {
				if (!(LP_isAllowedTo(array('can_view_others_likes_profile', 'can_view_likes_in_profiles')))) {
					return false;
				}
			}
		}

		$profile_areas['info']['areas']['likeposts'] = array(
			'label' => $txt['lp_menu'],
			'file' => LikePosts::$sourceFolder . 'LikePostsProfile.php',
			'function' => 'LP_showLikeProfile',
			'subsections' => array(
				'seeownlikes' => array($txt['lp_you_liked'], array('profile_view_own', 'profile_view_any')),
				'seeotherslikes' => array($txt['lp_liked_by_others'], array('profile_view_own', 'profile_view_any')),
			),
			'permission' => array(
				'own' => 'profile_view_own',
				'any' => 'profile_view_any',
			),
		);
	}
}
LikePosts::getInstance();

?>
