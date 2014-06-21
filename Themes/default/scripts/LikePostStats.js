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
			tabsVisitedCurrentSession = {},
			// defaultHash = 'messagestats',
			defaultHash = 'topicstats',

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

			showSpinnerOverlay = function() {
				likePostStats.jQRef('#like_post_stats_overlay').show();
				likePostStats.jQRef('#lp_preloader').show();
			},

			hideSpinnerOverlay = function() {
				likePostStats.jQRef('#lp_preloader').hide();
				likePostStats.jQRef('#like_post_stats_overlay').hide();
			},

			highlightActiveTab = function() {
				likePostStats.jQRef('.like_post_stats_menu a').removeClass('active');
				likePostStats.jQRef('.like_post_stats_menu #' + currentUrlFrag).addClass('active');
			},

			checkUrl = function(url) {
				showSpinnerOverlay();

				likePostStats.jQRef(".message_title").off('mouseenter mousemove mouseout');
				if (typeof(url) === 'undefined' || url === '') {
					var currentHref = window.location.href.split('#');
					currentUrlFrag = (typeof(currentHref[1]) !== 'undefined') ? currentHref[1] : defaultHash;
				} else {
					currentUrlFrag = url;
				}

				if (allowedUrls.hasOwnProperty(currentUrlFrag) === false) {
					currentUrlFrag = defaultHash;
				}

				likePostStats.jQRef('.like_post_stats_data').children().hide();
				highlightActiveTab();
				if (tabsVisitedCurrentSession.hasOwnProperty(currentUrlFrag) === false) {
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
							tabsVisitedCurrentSession[currentUrlFrag] = resp.data;
							params.uiFunc();
						} else {
							//NOTE: Make an error callback over here
						}
					}
				});
			},

			showMessageStats = function() {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '',
					messageUrl = smf_scripturl +'?topic=' + data.id_topic + '.msg' + data.id_msg;

				likePostStats.jQRef('.like_post_message_data').html('');
				htmlContent += '<a class="message_title" href="'+ messageUrl +'"> Topic: ' + data.subject + '</a>'
							+ '<span class="display_none">' + data.body + '</span>';

				htmlContent += '<div class="poster_avatar"><div class="avatar" style="background-image: url('+ data.member_received.avatar +')"></div></div>'
							+ '<div class="poster_data">'
							+ '<a class="poster_details" href="'+ data.member_received.href +'" style="font-size: 20px;">'+ data.member_received.name +'</a>'
							+ '<div class="poster_details">Total posts: '+ data.member_received.total_posts +'</div>'
							+ '</div>';

				htmlContent += '<div class="users_liked">'
				htmlContent += '<p class="title">'+ data.member_liked_data.length +' users who liked this post</p>';
				for(var i = 0, len = data.member_liked_data.length; i < len; i++) {
					htmlContent += '<a class="poster_details" href="'+ data.member_liked_data[i].href +'"><div class="poster_avatar" style="background-image: url('+ data.member_liked_data[i].avatar +')" title="'+ data.member_liked_data[i].real_name +'"></div></a>';
				}
				htmlContent += '</div>';

				likePostStats.jQRef('#like_post_current_tab').text('Most Liked Message');
				likePostStats.jQRef('.like_post_message_data').append(htmlContent).show();
				likePostStats.jQRef(".message_title").on('mouseenter', function(e) {
					e.preventDefault();
					var currText = likePostStats.jQRef(this).next().html();

					likePostStats.jQRef("<div class=\'subject_details\'></div>").html(currText).appendTo("body").fadeIn("slow");
				}).on('mouseout', function() {
					likePostStats.jQRef(".subject_details").fadeOut("slow");
					likePostStats.jQRef(".subject_details").remove();
				}).on('mousemove', function(e) {
					var mousex = e.pageX + 20,
						mousey = e.pageY + 10,
						width = likePostStats.jQRef("#wrapper").width() - mousex - 50;

					likePostStats.jQRef(".subject_details").css({
						top: mousey,
						left: mousex,
						width: width + "px"
					});
				});
				hideSpinnerOverlay();
			},

			showTopicStats = function() {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '',
					topicUrl = smf_scripturl +'?topic=' + data.id_topic;

				console.log(data);
				likePostStats.jQRef('.like_post_topic_data').html('');
				htmlContent += '<a class="topic_title" href="'+ topicUrl +'">The most popular topic has received ' + data.like_count + ' like(s) so far<a/>';
				htmlContent += '<p class="topic_info">The topic contains ' + data.msg_data.length + ' different posts. Few of the liked posts from it</p>';

				for(var i = 0, len = data.msg_data.length; i < len; i++) {
					var msgUrl = topicUrl + '.msg' + data.msg_data[i].id_msg;

					console.log(data.msg_data[i].body.length);
					htmlContent += '<div class="message_body"> ' + data.msg_data[i].body + '<a class="read_more" href="'+ msgUrl +'">read more...</a></div>';
				}
				likePostStats.jQRef('#like_post_current_tab').text('Most Liked Topic');
				likePostStats.jQRef('.like_post_topic_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showBoardStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '';

				console.log(data);
			},

			showUserStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '';

				console.log(data);
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
