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

if (!defined('SMF'))
	die('Hacking attempt...');

class LikePosts {
	protected static $instance;

	public static $sourceFolder = '/LikePosts/';

	public static $LikePostsUtils;
	public static $LikePostsDB;
	public static $LikePostsData;
	public static $LikePostsRouter;
	public static $LikeUnlikePosts;
	public static $LikePostsStats;
	public static $LikePostsAdmin;
	public static $LikePostsProfile;

	/**
	 * Singleton method
	 *
	 * @return LikePosts
	 * @return LikePosts
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new LikePosts();
			loadLanguage('LikePosts');
			self::loadClass('LikePostsUtils');
			self::loadClass('LikePostsDB');
		}
		return self::$instance;
	}

	public function __construct() {}

	/**
	 * @param string $className
	 */
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
					require_once ($sourcedir . self::$sourceFolder . '/DB/' . $className . '.php');
					self::$LikePostsDB = new LikePostsDB();
				}
				break;

			case 'LikePostsAdminDB':
				require_once ($sourcedir . self::$sourceFolder . '/DB/' . $className . '.php');
				break;

			case 'LikePostsProfileDB':
				require_once ($sourcedir . self::$sourceFolder . '/DB/' . $className . '.php');
				break;

			case 'LikePostsStatsDB':
				require_once ($sourcedir . self::$sourceFolder . '/DB/' . $className . '.php');
				break;

			case 'LikePostsData': 
				if (self::$LikePostsData === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsData = new LikePostsData();
				}
				break;

			case 'LikePostsRouter':
				if (self::$LikePostsRouter === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsRouter = new LikePostsRouter();
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

			case 'LikePostsAdmin':
				if (self::$LikePostsAdmin === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsAdmin = new LikePostsAdmin();
				}
				break;

			case 'LikePostsProfile':
				if (self::$LikePostsProfile === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$LikePostsProfile = new LikePostsProfile();
				}
				break;

			default:
				break;
		}
	}

	public static function addActionContext(&$actions) {
		self::loadClass('LikePostsRouter');

		$actions['likeposts'] = array(self::$sourceFolder . 'LikePostsRouter.php', 'LikePostsRouter::routeLikes');
		$actions['likepostsdata'] = array(self::$sourceFolder . 'LikePostsRouter.php', 'LikePostsRouter::routeLikesData');
		$actions['likepostsstats'] = array(self::$sourceFolder . 'LikePostsRouter.php', 'LikePostsRouter::routeLikeStats');
		$actions['likepostsstatsajax'] = array(self::$sourceFolder . 'LikePostsRouter.php', 'LikePostsRouter::routeLikeStatsAjax');
	}

	public static function includeAssets() {
		global $context, $settings, $txt;

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
				js.src = "' . $settings['default_theme_url'] . '/scripts/LikePosts/LikePosts.min.js";
				js.onload = function() {
					lpObj.likePostsNotification.init({
						txtStrings: {
							"lpAllNotification": "'. $txt['lp_all_notification'] .'",
							"lpMyPosts": "'. $txt['lp_my_posts'] .'",
							"lpNoNotification": "'. $txt['lp_no_notification'] .'"
						}
					});
				}
				document.body.appendChild(js);
			}
		// ]]></script>';

		self::$LikePostsUtils->checkJsonEncodeDecode();
	}

	public static function addMenuItems(&$menu_buttons) {
		global $scripturl, $txt, $user_info, $modSettings;

		if (empty($modSettings['lp_mod_enable']) || empty($modSettings['lp_stats_enable']) || 
			!self::$LikePostsUtils->isAllowedTo(array('lp_guests_can_view_likes_stats', 'lp_can_view_likes_stats'))) {
			return false;
		}

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

	public static function addAdminPanel(&$admin_areas) {
		global $txt;

		$admin_areas['config']['areas']['likeposts'] = array(
			'label' => $txt['lp_menu'],
			'file' => '/LikePosts/LikePostsRouter.php',
			'function' => 'routeLikePostsAdmin',
			'icon' => 'administration.gif',
			'subsections' => array(),
		);
	}

	public static function addProfilePanel(&$profile_areas) {
		global $txt, $user_info;

		if(isset($_REQUEST['u']) && is_numeric($_REQUEST['u']) && 
			$user_info['id'] !== $_REQUEST['u'] && 
			!self::$LikePostsUtils->isAllowedTo(array('lp_guest_can_view_likes_in_profiles', 'lp_can_view_others_likes_profile'))) {
					return false;
		}

		$profile_areas['info']['areas']['likeposts'] = array(
			'label' => $txt['lp_menu'],
			'file' => '/LikePosts/LikePostsRouter.php',
			'function' => 'routeLikePostsProfile',
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
