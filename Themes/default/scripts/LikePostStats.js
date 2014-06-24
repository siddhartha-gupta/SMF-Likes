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
			defaultHash = 'messagestats',

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
					'mostlikesreceiveduserstats': {
						'uiFunc': showMostLikesReceivedUserStats
					},
					'mostlikesgivenuserstats': {
						'uiFunc': showMostLikesGivenUserStats
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

				likePostStats.jQRef('.like_post_topic_data').html('');
				htmlContent += '<a class="topic_title" href="'+ topicUrl +'">The most popular topic has received ' + data.like_count + ' like(s) so far</a>';
				htmlContent += '<p class="topic_info">The topic contains ' + data.msg_data.length + ' different posts. Few of the liked posts from it</p>';

				for(var i = 0, len = data.msg_data.length; i < len; i++) {
					var msgUrl = topicUrl + '.msg' + data.msg_data[i].id_msg;

					htmlContent += '<div class="message_body">'
								+ '<div class="posted_at">'+ data.msg_data[i].member.name +' : Posted at '+ data.msg_data[i].poster_time +'</div> '
								+ '<a class="poster_details" href="'+ data.msg_data[i].member.href +'"><div class="poster_avatar" style="background-image: url('+ data.msg_data[i].member.avatar +')"></div></a>'
								+ data.msg_data[i].body
								+ '<a class="read_more" href="'+ msgUrl +'">read more...</a>'
								+'</div>';
				}
				likePostStats.jQRef('#like_post_current_tab').text('Most Liked Topic');
				likePostStats.jQRef('.like_post_topic_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showBoardStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '',
					boardUrl = smf_scripturl +'?board=' + data.id_board;

				likePostStats.jQRef('.like_post_board_data').html('');
				htmlContent += '<a class="board_title" href="'+ boardUrl +'">'+ data.name +' has received ' + data.like_count + ' like(s) so far</a>';
				htmlContent += '<p class="board_info">The board contains ' + data.num_topics + ' different topics, out which ' + data.topics_liked +' topics are liked.</p>';
				htmlContent += '<p class="board_info" style="margin: 5px 0 20px;">Furthermore, these topic contains '+ data.num_posts +' different posts, out of which '+ data.msgs_liked +' posts are liked so far. Few of the liked topics from it</p>';

				for(var i = 0, len = data.topic_data.length; i < len; i++) {
					var topicUrl = smf_scripturl +'?topic=' + data.topic_data[i].id_topic;

					htmlContent += '<div class="message_body">'
								+ '<div class="posted_at">'+ data.topic_data[i].member.name +' : Posted at '+ data.topic_data[i].poster_time +'</div> '
								+ '<a class="poster_details" href="'+ data.topic_data[i].member.href +'"><div class="poster_avatar" style="background-image: url('+ data.topic_data[i].member.avatar +')"></div></a>'
								+ data.topic_data[i].body
								+ '<a class="read_more" href="'+ topicUrl +'">read more...</a></div>';
				}
				likePostStats.jQRef('#like_post_current_tab').text('Most Liked Board');
				likePostStats.jQRef('.like_post_board_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showMostLikesReceivedUserStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '';

				likePostStats.jQRef('.like_post_most_liked_user_data').html('');
				htmlContent += '<div class="poster_avatar"><div class="avatar" style="background-image: url('+ data.member_received.avatar +')"></div></div>'
							+ '<div class="poster_data">'
							+ '<a class="poster_details" href="'+ data.member_received.href +'" style="font-size: 20px;">'+ data.member_received.name +'</a>'
							+ '<div class="poster_details">Total posts: '+ data.member_received.total_posts +'</div>'
							+ '<div class="poster_details">Total Likes: '+ data.like_count +'</div>'
							+ '</div>';

				htmlContent += '<p class="generic_text">Few of users most liked posts</p>';
				for(var i = 0, len = data.topic_data.length; i < len; i++) {
					var msgUrl = smf_scripturl +'?topic=' + data.topic_data[i].id_topic + '.msg' + data.topic_data[i].id_msg;

					htmlContent += '<div class="message_body">'
								+ '<div class="posted_at">Posted at '+ data.topic_data[i].poster_time +': Likes received ('+ data.topic_data[i].like_count +')</div> '
								+ data.topic_data[i].body
								+ '<a class="read_more" href="'+ msgUrl +'">read more...</a></div>';
				}
				likePostStats.jQRef('#like_post_current_tab').text('Most Liked Member');
				likePostStats.jQRef('.like_post_most_liked_user_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showMostLikesGivenUserStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '';

				likePostStats.jQRef('.like_post_most_likes_given_user_data').html('');
				htmlContent += '<div class="poster_avatar"><div class="avatar" style="background-image: url('+ data.member_given.avatar +')"></div></div>'
							+ '<div class="poster_data">'
							+ '<a class="poster_details" href="'+ data.member_given.href +'" style="font-size: 20px;">'+ data.member_given.name +'</a>'
							+ '<div class="poster_details">Total posts: '+ data.member_given.total_posts +'</div>'
							+ '<div class="poster_details">Total Likes Given: '+ data.like_count +'</div>'
							+ '</div>';

				htmlContent += '<p class="generic_text">Few of users recently liked posts</p>';
				for(var i = 0, len = data.topic_data.length; i < len; i++) {
					var msgUrl = smf_scripturl +'?topic=' + data.topic_data[i].id_topic + '.msg' + data.topic_data[i].id_msg;

					htmlContent += '<div class="message_body">'
								+ '<div class="posted_at">Posted at '+ data.topic_data[i].poster_time +'</div> '
								+ data.topic_data[i].body
								+ '<a class="read_more" href="'+ msgUrl +'">read more...</a></div>';
				}
				likePostStats.jQRef('#like_post_current_tab').text('Most Like Giving user');
				likePostStats.jQRef('.like_post_most_likes_given_user_data').html(htmlContent).show();
				hideSpinnerOverlay();
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
