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

(function() {
	function likePostStats() {}

	likePostStats.prototype = function() {
		var currentUrlFrag = null,
			allowedUrls = {},
			tabsVisitedCurrentSession = [],

			init = function() {
				allowedUrls = {
					'messagestats': {
						'uiFunc': showMessageStats
					},
					'topicstats': {
						'uiFunc': showTopicStats
					},
					'boardstats': {
						'uiFunc': showBoardStats
					},
					'userstats': {
						'uiFunc': showUserStats
					}
				};
				checkUrl();
			},

			showSpinner = function() {
				likePostStats.jQRef('#lp_spinner').show();
			},

			hideSpinner = function() {
				likePostStats.jQRef('#lp_spinner').hide();
			},

			highlightActiveTab = function() {
				likePostStats.jQRef('.like_post_stats_menu a').removeClass('active');
				likePostStats.jQRef('.like_post_stats_menu #' + currentUrlFrag).addClass('active');
			},

			checkUrl = function(url) {
				if (typeof(url) === 'undefined' || url === '') {
					var currentHref = window.location.href.split('#');
					currentUrlFrag = (typeof(currentHref[1]) !== 'undefined') ? currentHref[1] : 'messagestats';
				} else {
					currentUrlFrag = url;
				}

				if (allowedUrls.hasOwnProperty(currentUrlFrag) === false) {
					currentUrlFrag = 'messagestats';
				}

				if (tabsVisitedCurrentSession.indexOf(currentUrlFrag) < 0) {
					tabsVisitedCurrentSession.push(currentUrlFrag);
					getDataFromServer({
						'url': currentUrlFrag,
						'uiFunc': allowedUrls[currentUrlFrag].uiFunc
					});
				} else {
					allowedUrls[currentUrlFrag].uiFunc();
				}
			},

			// Data/ajax functions from here
			getDataFromServer = function(params) {
				highlightActiveTab();

				likePostStats.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=likepostsstats',
					context: document.body,
					dataType: "json",
					data: {
						'area': 'ajaxdata',
						'sa': params.url
					},
					success: function(resp) {
						if (resp.response) {
							params.uiFunc(resp.data);
						} else {
							//NOTE: Make an error callback over here
						}
					}
				});
			},

			showMessageStats = function(response) {
				console.log(response);
			},

			showTopicStats = function(response) {
				console.log(response);
			},

			showBoardStats = function(response) {
				console.log(response);
			},

			showUserStats = function(response) {
				console.log(response);
			};

		return {
			init: init,
			checkUrl: checkUrl
		};
	}();

	this.likePostStats = likePostStats;
	if (typeof(likePostStats.jQRef) !== 'function' && typeof(likePostStats.jQRef) === 'undefined') {
		likePostStats.jQRef = jQuery.noConflict();
	}

	likePostStats.jQRef(document).ready(function() {
		likePostStats.prototype.init();
	});

	likePostStats.jQRef('.like_post_stats_menu a').on('click', function(e) {
		if (e) {
			e.preventDefault();
			e.stopPropagation();
		}
		likePostStats.prototype.checkUrl(this.id);
	});
}());
