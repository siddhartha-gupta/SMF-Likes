/**
 * @package manifest file for Like Posts
 * @version 2.0.5
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

// Root JS object of the mod
(function(win) {
	win.lpObj = win.lpObj || {};

	lpObj.timeoutTimer = null;
	if (typeof(lpObj.jQRef) !== 'function' && typeof(lpObj.jQRef) === 'undefined') {
		lpObj.jQRef = lp_jquery2_0_3;
	}

	lpObj.jQRef(document).ready(function() {
		lpObj.jQRef('.like_post_box').show();

		lpObj.jQRef(".subject_heading").on('mouseenter', function(e) {
			e.preventDefault();
			var currText = lpObj.jQRef(this).next().html();

			lpObj.jQRef("<div class=\'subject_details\'></div>").html(currText).appendTo("body").fadeIn("slow");
		}).on('mouseleave', function(e) {
			e.preventDefault();
			lpObj.jQRef(".subject_details").fadeOut("slow");
			lpObj.jQRef(".subject_details").remove();
		}).on('mousemove', function(e) {
			e.preventDefault();
			var mousex = e.pageX + 20,
				mousey = e.pageY + 10,
				width = lpObj.jQRef("#wrapper").width() - mousex - 50;

			lpObj.jQRef(".subject_details").css({
				top: mousey,
				left: mousex,
				width: width + "px"
			});
		});


		lpObj.jQRef(".like_post_stats_menu a").on("click", function(event) {
			if (!lpObj.likePostsUtils.isNullUndefined(event)) {
				event.preventDefault();
				event.stopPropagation();
			}
			lpObj.likePostStats.checkUrl(this.id);
		});
	});
})(window);

(function() {
	function likePostsUtils() {}

	likePostsUtils.prototype = function() {
		var removeOverlay = function(e) {
				if (isNullUndefined(e) && lpObj.timeoutTimer === null) {
					return false;
				} else if (lpObj.timeoutTimer !== null ||
					((e.type == 'keyup' && e.keyCode == 27) || e.type == 'click' || e.type == 'touchstart')) {
					clearTimeout(lpObj.timeoutTimer);
					lpObj.timeoutTimer = null;
					lpObj.jQRef('.like_posts_overlay').remove();
					lpObj.jQRef('.like_posts_overlay').off('click');
					lpObj.jQRef(document).off('click', lpObj.likePostsUtils.removeOverlay);
					lpObj.jQRef(document).off('keyup', lpObj.likePostsUtils.removeOverlay);
					lpObj.jQRef(document).off('touchstart', lpObj.likePostsUtils.removeOverlay);
				}
			},

			bouncEffect = function(element, direction, times, distance, speed) {
				var dir = 'marginLeft',
					anim1 = {},
					anim2 = {},
					i = 0;

				switch (direction) {
					case 'rl':
						dir = 'marginRight';
						break;

					case 'tb':
						dir = 'marginTop';
						break;

					case 'bt':
						dir = 'marginBottom';
						break;

					default:
						break;
				}
				anim1[dir] = '+=' + distance;
				anim2[dir] = '-=' + distance;

				for (; i < times; i++) {
					element.animate(anim1, speed).animate(anim2, speed);
				}
			},

			selectInputByLegend = function(event, elem) {
				event.preventDefault();

				var elemRef = lpObj.jQRef(elem),
					parent = elemRef.parent();

				if (isNullUndefined(elemRef.data('allselected'))) {
					if (parent.find('input').length === parent.find('input:checked').length) {
						parent.find('input:checkbox').prop('checked', false);
						elemRef.data('allselected', false);
					} else {
						parent.find('input:checkbox').prop('checked', true);
						elemRef.data('allselected', true);
					}
				} else if (elemRef.data('allselected') === false) {
					parent.find('input:checkbox').prop('checked', true);
					elemRef.data('allselected', true);
				} else {
					parent.find('input:checkbox').prop('checked', false);
					elemRef.data('allselected', false);
				}
			},

			isMobileDevice = function() {
				if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i)) {
					return true;
				} else {
					return false;
				}
			},

			getType = function(obj) {
				return ({}).toString.call(obj).toLowerCase();
			},

			isNullUndefined = function(val, validateZeroNaN) {
				var isNull = false,
					type = getType(val);

				switch (type) {
					case '[object array]':
						if (val.length === 0) {
							isNull = true;
						}
						break;

					case '[object object]':
						if (Object.keys(val).length === 0) {
							isNull = true;
						}
						break;

					default:
						if (typeof(val) === "undefined" || val === null || val === "" || val === "null" || val === "undefined") {
							isNull = true;
						} else if (validateZeroNaN && (val === 0 || isNaN(val))) {
							isNull = true;
						}
				}
				return isNull;
			};

		return {
			'removeOverlay': removeOverlay,
			'selectInputByLegend': selectInputByLegend,
			'isMobileDevice': isMobileDevice,
			'isNullUndefined': isNullUndefined
		};
	}();
	lpObj.likePostsUtils = likePostsUtils.prototype;
})();

(function() {
	function likeHandler() {}

	likeHandler.prototype = function() {
		var isLikeAjaxInProgress = false,

			likeUnlikePosts = function(e, mId, aId) {
				if (isLikeAjaxInProgress === true) return false;

				var userRating = e.target.href.split('#')[1],
					msgId = (mId !== undefined) ? parseInt(mId, 10) : 0,
					authorId = (aId !== undefined) ? parseInt(aId, 10) : 0,
					rating = (userRating !== undefined) ? parseInt(userRating, 10) : 0;

				if (isNaN(msgId) || isNaN(authorId)) {
					return false;
				}

				isLikeAjaxInProgress = true;
				lpObj.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=likeposts;sa=like_post',
					dataType: "json",
					data: {
						msg: msgId,
						rating: rating,
						author: authorId
					}
				}).done(function(resp) {
					if (resp.response) {
						var params = {
							msgId: msgId,
							count: (resp.count !== undefined) ? resp.count : '',
							newText: resp.newText,
							likeText: resp.likeText,
							rating: rating
						};
						onLikeSuccess(params);
					} else {
						//NOTE: Make an error callback over here
					}
				}).fail(function(err) {
					console.log(err);
				}).always(function(resp) {
					console.log(resp);
				});
			},

			onLikeSuccess = function(params) {
				var count = parseInt(params.count, 10);

				if (isNaN(count)) {
					isLikeAjaxInProgress = false;
					return false;
				}

				var likeButtonRef = lpObj.jQRef('#like_' + params.msgId),
					likeText = params.likeText,
					newLink = (params.rating === 1) ? '#0' : '#1';

				if (parseInt(likeButtonRef.attr('href').split('#')[1], 10) === 0) {
					likeButtonRef.removeClass('unlike_link').addClass('like_link');
				} else {
					likeButtonRef.removeClass('like_link').addClass('unlike_link');
				}

				lpObj.jQRef(likeButtonRef).attr('href', newLink);
				lpObj.jQRef(likeButtonRef).animate({
					left: '-40px',
					opacity: 'toggle'
				}, 1000, '', function() {
					lpObj.jQRef(likeButtonRef).html(params.newText);

					lpObj.jQRef(likeButtonRef).animate({
						left: '0px',
						opacity: 'toggle'
					}, 1000);
				});

				if (lpObj.jQRef('#like_count_' + params.msgId).length) {
					if (likeText === '') {
						lpObj.jQRef('#like_count_' + params.msgId).fadeOut(2000).remove();
					} else {
						lpObj.jQRef('#like_count_' + params.msgId).fadeOut(1000, function() {
							lpObj.jQRef(this).html('(' + likeText + ')').fadeIn(1000);
						});
					}
				} else {
					lpObj.jQRef('<span class="display_inline" id="like_count_' + params.msgId + '">(' + likeText + ')</span>').hide().appendTo('#like_post_info_' + params.msgId).fadeIn(2000);
				}

				lpObj.timeoutTimer = setTimeout(function() {
					isLikeAjaxInProgress = false;
					lpObj.likePostsUtils.removeOverlay();
				}, 2000);
			},

			showMessageLikedInfo = function(messageId) {
				if (isNaN(messageId)) {
					return false;
				}

				lpObj.jQRef.ajax({
					type: "GET",
					url: smf_scripturl + '?action=likepostsdata;sa=get_message_like_info',
					dataType: "json",
					data: {
						msg_id: messageId
					}
				}).done(function(resp) {
					if (resp.response) {
						if (resp.data.length <= 0) {
							return false;
						}

						var data = resp.data,
							i,
							height = 0,
							completeString = '<div class="like_posts_overlay"><div class="like_posts_member_info_box">';

						for (i in data) {
							if (data.hasOwnProperty(i)) {
								completeString += '<div class="like_posts_member_info"><img class="avatar" src="' + data[i].avatar.href + '" /><div class="like_posts_member_info_details"><a href="' + data[i].href + '">' + data[i].name + '</a></div></div>';
							}
						}
						completeString += '</div></div>';
						lpObj.jQRef('body').append(completeString);

						setTimeout(function() {
							lpObj.jQRef('.like_posts_member_info').each(function() {
								height += lpObj.jQRef(this).outerHeight();
							});

							if (height >= (window.innerHeight - 100)) {
								height = window.innerHeight - 200;
							}
							lpObj.jQRef('.like_posts_member_info_box').css({
								'height': height,
								'visibility': 'visible'
							});
						}, 50);

						lpObj.jQRef(document).one('click keyup touchstart', lpObj.likePostsUtils.removeOverlay);
						lpObj.jQRef('.like_posts_member_info_box').click(function(e) {
							e.stopPropagation();
						});
					} else {
						//NOTE: Make an error callback over here
						return false;
					}
				}).fail(function(err) {
					console.log(err);
				}).always(function(resp) {
					console.log(resp);
				});
			};

		return {
			'likeUnlikePosts': likeUnlikePosts,
			'showMessageLikedInfo': showMessageLikedInfo
		};
	}();

	lpObj.likeHandler = likeHandler.prototype;
})();

(function() {
	function likePostsNotification() {}

	likePostsNotification.prototype = function() {
		var textStrings = {},

			init = function(params) {
				textStrings = lpObj.jQRef.extend({}, params.txtStrings);
			},

			showLikeNotification = function() {
				lpObj.jQRef.ajax({
					type: "GET",
					url: smf_scripturl + '?action=likepostsdata;sa=like_posts_notification',
					dataType: "json",
				}).done(function(resp) {
					if (resp.response) {
						if (resp.data.length <= 0) {
							return false;
						}

						var data = resp.data,
							notificationInfo = '',
							i, j, k,
							dataLengthAll = 0,
							dataLengthMine = 0,
							completeString = '';

						notificationInfo += '<div class="lp_notification_header"><div class="lp_notification_tabs" id="lp_all_notifications">' + textStrings.lpAllNotification + '</div><div class="lp_notification_tabs" id="lp_my_notifications">' + textStrings.lpMyPosts + '</div><div class="lp_notification_tabs close_btn" id="close_notifications">x</div></div>';

						for (i in data) {
							var len = 0;

							if (data.hasOwnProperty(i)) {
								if (i === 'all') {
									notificationInfo += '<div class="lp_notification_body lp_all_notifications_data">';

									if (data[i].length === 0) {
										notificationInfo += '<div class="single_notify">' + textStrings.lpNoNotification + '</div>';
									} else {
										for (j in data[i]) {
											if (data[i].hasOwnProperty(j)) {
												len++;
												notificationInfo += '<div class="single_notify"><div class="avatar" style="background-image: url(' + data[i][j].member.avatar.href + ')"></div><div class="like_post_notify_data"><a href="' + data[i][j].member.href + '"><strong>' + data[i][j].member.name + '</strong></a> liked ' + '<a href="' + data[i][j].href + '">' + data[i][j].subject + '</a></div></div>';
											}
										}
									}
									dataLengthAll = len;
									notificationInfo += '</div>';
								} else if (i === 'mine') {
									notificationInfo += '<div class="lp_notification_body lp_my_notifications_data hide_elem">';
									if (data[i].length === 0) {
										notificationInfo += '<div class="single_notify">' + textStrings.lpNoNotification + '</div>';
									} else {
										for (k in data[i]) {
											if (data[i].hasOwnProperty(k)) {
												len++;
												notificationInfo += '<div class="single_notify"><div class="avatar" style="background-image: url(' + data[i][k].member.avatar.href + ')"></div><div class="like_post_notify_data"><a href="' + data[i][k].member.href + '"><strong>' + data[i][k].member.name + '</strong></a> liked ' + '<a href="' + data[i][k].href + '">' + data[i][k].subject + '</a></div></div>';
											}
										}
									}
									dataLengthMine = len;
									notificationInfo += '</div>';
								}
							}
						}
						completeString = '<div class="like_posts_notification">' + notificationInfo + '</div>';

						dataLengthAll = dataLengthAll * 50;
						if (dataLengthAll > 200) {
							dataLengthAll = 200;
						} else if (dataLengthAll < 100) {
							dataLengthAll = 100;
						}

						dataLengthMine = dataLengthMine * 50;
						if (dataLengthMine > 200) {
							dataLengthMine = 200;
						} else if (dataLengthMine < 100) {
							dataLengthMine = 100;
						}
						lpObj.jQRef('body').append(completeString);

						var leftOffset = lpObj.jQRef('.showLikeNotification').offset().left + lpObj.jQRef('.showLikeNotification').width() + 20,
							checkFloat = leftOffset + lpObj.jQRef('.like_posts_notification').outerWidth();

						// changed from window.innerWidth for mobile devices
						if (lpObj.likePostsUtils.isMobileDevice()) {
							if (checkFloat > document.documentElement.clientWidth) {
								leftOffset = lpObj.jQRef('.showLikeNotification').offset().left - lpObj.jQRef('.like_posts_notification').outerWidth() - 20;
							}
						} else {
							if (checkFloat > window.innerWidth) {
								leftOffset = lpObj.jQRef('.showLikeNotification').offset().left - lpObj.jQRef('.like_posts_notification').outerWidth() - 20;
							}
						}

						lpObj.jQRef('.like_posts_notification').css({
							'top': lpObj.jQRef('.showLikeNotification').offset().top,
							'left': leftOffset
						});
						lpObj.jQRef('.lp_all_notifications_data').css({
							'height': dataLengthAll + 'px'
						});
						lpObj.jQRef('.lp_my_notifications_data').css({
							'height': dataLengthMine + 'px'
						});

						lpObj.jQRef('#lp_all_notifications').css({
							'font-weight': 'bold'
						});

						lpObj.jQRef('.lp_notification_header').on('click', function(e) {
							e.preventDefault();
							switch (e.target.id) {
								case 'lp_all_notifications':
									lpObj.jQRef('#lp_all_notifications').css({
										'font-weight': 'bold'
									});
									lpObj.jQRef('#lp_my_notifications').css({
										'font-weight': 'normal'
									});
									lpObj.jQRef('.lp_my_notifications_data').hide();
									lpObj.jQRef('.lp_all_notifications_data').show();
									break;

								case 'lp_my_notifications':
									lpObj.jQRef('#lp_all_notifications').css({
										'font-weight': 'normal'
									});
									lpObj.jQRef('#lp_my_notifications').css({
										'font-weight': 'bold'
									});
									lpObj.jQRef('.lp_all_notifications_data').hide();
									lpObj.jQRef('.lp_my_notifications_data').show();
									break;

								case 'close_notifications':
									removeNotification(e);
									break;

								default:
									break;
							}
						});
						lpObj.jQRef(document).on('click keyup', removeNotification);
					} else {
						//NOTE: Make an error callback over here
						return false;
					}
				}).fail(function(err) {
					console.log(err);
				}).always(function(resp) {
					console.log(resp);
				});
			},

			removeNotification = function(e) {
				if ((e.type == 'keyup' && e.keyCode == 27) || e.type == 'click') {
					var container = lpObj.jQRef('#lp_all_notifications, #lp_my_notifications');
					if (!container.is(e.target) && container.has(e.target).length === 0) {
						lpObj.jQRef('.like_posts_notification').unbind('click');
						lpObj.jQRef('.like_posts_notification').unbind('keyup');
						lpObj.jQRef('.lp_notification_header').unbind('click');
						lpObj.jQRef(document).unbind('click', removeNotification);
						lpObj.jQRef(document).unbind('keyup', removeNotification);
						lpObj.jQRef('.like_posts_notification').remove();
					}
				}
			};

		return {
			'init': init,
			'showLikeNotification': showLikeNotification
		};
	}();
	lpObj.likePostsNotification = likePostsNotification.prototype;
})();

// some admin related functions
(function() {
	function likePostsAdmin() {}

	likePostsAdmin.prototype = function() {
		var optimizeLikes = function(event) {
				if (!lpObj.likePostsUtils.isNullUndefined(event)) {
					event.preventDefault();
				}

				lpObj.jQRef('.like_posts_overlay').removeClass('hide_elem');
				lpObj.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=admin;area=likeposts;sa=optimizelikes',
					dataType: "json",
					data: {}
				}).done(function() {
					lpObj.jQRef('.like_posts_overlay').addClass('hide_elem');
					recountStats(null, {});
				}).fail(function(err) {
					console.log(err);
				}).always(function() {
					console.log('optimizeLikes always called');
				});
			},

			removeDupLikes = function(event) {
				if (!lpObj.likePostsUtils.isNullUndefined(event)) {
					event.preventDefault();
				}

				lpObj.jQRef('.like_posts_overlay').removeClass('hide_elem');
				lpObj.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=admin;area=likeposts;sa=removeduplikes',
					dataType: "json",
					data: {}
				}).done(function() {
					lpObj.jQRef('.like_posts_overlay').addClass('hide_elem');
					recountStats(null, {});
				}).fail(function(err) {
					console.log(err);
				}).always(function() {
					console.log('removeDupLikes always called');
				});
			},

			recountStats = function(event, options) {
				if (!lpObj.likePostsUtils.isNullUndefined(event)) {
					event.preventDefault();
				}

				var totalWork = options.totalWork || 0,
					startLimit = options.startLimit || 0,
					increment = options.increment || 100,
					endLimit = options.endLimit || 100;

				lpObj.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=admin;area=likeposts;sa=recountlikestotal',
					dataType: "json",
					data: {
						'totalWork': totalWork,
						'startLimit': startLimit,
						'endLimit': endLimit
					}
				}).done(function(resp) {
					resp.increment = increment;
					resp.startLimit = startLimit;
					checkRecount(resp);
				}).fail(function(err) {
					console.log(err);
				}).always(function() {
					console.log('recountStats always called');
				});
			},

			checkRecount = function(obj) {
				var startLimit,
					percentage = 0,
					percentageText = '0%';

				if (obj.startLimit === 0) {
					var completeString = '<div class="like_posts_overlay"><div class="recount_stats"><div></div></div></div>';

					lpObj.jQRef('body').append(completeString);

					var screenWidth = lpObj.jQRef(window).width(),
						screenHeight = lpObj.jQRef(window).height(),
						popupHeight = lpObj.jQRef('.recount_stats').outerHeight(),
						popupWidth = lpObj.jQRef('.recount_stats').outerWidth(),
						topPopUpOffset = (screenHeight - popupHeight) / 2,
						leftPopUpOffset = (screenWidth - popupWidth) / 2;

					lpObj.jQRef('.recount_stats').css({
						top: topPopUpOffset + 'px',
						left: leftPopUpOffset + 'px'
					});
					startLimit = obj.startLimit + obj.increment + 1;
				} else {
					startLimit = obj.startLimit + obj.increment;
				}

				if (startLimit < obj.totalWork) {
					var endLimit = obj.endLimit + obj.increment;
					if (endLimit > obj.totalWork) {
						endLimit = Math.abs(obj.endLimit - obj.totalWork) + obj.endLimit;
					}
					percentage = Math.floor((obj.endLimit / obj.totalWork) * 100);
					percentageText = percentage + '%';
				} else {
					percentage = 100;
					percentageText = 'Done';
					lpObj.jQRef(document).one('click keyup touchstart', lpObj.likePostsUtils.removeOverlay);
				}

				lpObj.jQRef('.recount_stats').find('div').animate({
					width: percentage + '%'
				}, 1000, function() {
					if (percentage < 100) {
						lpObj.likePostsAdmin.recountStats(null, {
							'totalWork': obj.totalWork,
							'startLimit': startLimit,
							'endLimit': endLimit
						});
					}
				}).html(percentageText + '&nbsp;');
			},

			selectAllBoards = function(event) {
				var elemRef = lpObj.jQRef('#lp_board_settings fieldset');

				if (lpObj.jQRef(event.target).is(':checked')) {
					elemRef.find('input:checkbox').prop('checked', true);
				} else {
					elemRef.find('input:checkbox').prop('checked', false);
				}
			};

		return {
			'optimizeLikes': optimizeLikes,
			'removeDupLikes': removeDupLikes,
			'recountStats': recountStats,
			'selectAllBoards': selectAllBoards
		};
	}();

	lpObj.likePostsAdmin = likePostsAdmin.prototype;
})();

(function() {
	function likePostStats() {}

	likePostStats.prototype = function() {
		var currentUrlFrag = null,
			allowedUrls = {},
			tabsVisitedCurrentSession = {},
			defaultHash = 'messagestats',
			txtStrings = {},

			init = function(params) {
				txtStrings = lpObj.jQRef.extend({}, params.txtStrings);
				if (params.onError === "") {
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
				}
			},

			showSpinnerOverlay = function() {
				lpObj.jQRef('#like_post_stats_overlay').show();
				lpObj.jQRef('#lp_preloader').show();
			},

			hideSpinnerOverlay = function() {
				lpObj.jQRef('#lp_preloader').hide();
				lpObj.jQRef('#like_post_stats_overlay').hide();
			},

			highlightActiveTab = function() {
				lpObj.jQRef('.like_post_stats_menu a').removeClass('active');
				lpObj.jQRef('.like_post_stats_menu #' + currentUrlFrag).addClass('active');
			},

			checkUrl = function(url) {
				showSpinnerOverlay();

				lpObj.jQRef(".message_title").off('mouseenter mousemove mouseout');
				if (lpObj.likePostsUtils.isNullUndefined(url)) {
					var currentHref = window.location.href.split('#');
					currentUrlFrag = (!lpObj.likePostsUtils.isNullUndefined(currentHref[1])) ? currentHref[1] : defaultHash;
				} else {
					currentUrlFrag = url;
				}

				if (allowedUrls.hasOwnProperty(currentUrlFrag) === false) {
					currentUrlFrag = defaultHash;
				}

				lpObj.jQRef('.like_post_stats_data').children().hide();
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

			getDataFromServer = function(params) {
				lpObj.jQRef('.like_post_stats_error').hide().html('');
				lpObj.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=likepostsstatsajax',
					context: document.body,
					dataType: "json",
					data: {
						'sa': params.url
					}
				}).done(function(resp) {
					if (!lpObj.likePostsUtils.isNullUndefined(resp.error) && resp.error !== '') {
						genericErrorMessage({
							errorMsg: resp.error
						});
					} else if (!lpObj.likePostsUtils.isNullUndefined(resp.data) && !lpObj.likePostsUtils.isNullUndefined(resp.data.noDataMessage) && resp.data.noDataMessage !== '') {
						genericErrorMessage({
							errorMsg: resp.data.noDataMessage
						});
					} else if (resp.response) {
						tabsVisitedCurrentSession[currentUrlFrag] = resp.data;
						params.uiFunc();
					} else {
						hideSpinnerOverlay();
					}
				}).fail(function(err) {
					console.log(err);
				}).always(function() {
					console.log('getDataFromServer always called');
				});
			},

			showMessageStats = function() {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '',
					messageUrl = smf_scripturl + '?topic=' + data.id_topic + '.msg' + data.id_msg;

				lpObj.jQRef('.like_post_message_data').html('');
				htmlContent += '<a class="message_title" href="' + messageUrl + '">' + txtStrings.topic + ': ' + data.subject + '</a>' + '<span class="hide_elem">' + data.body + '</span>';

				htmlContent += '<div class="poster_avatar"><div class="avatar" style="background-image: url(' + encodeURI(data.member_received.avatar) + ')"></div></div>' + '<div class="poster_data">' + '<a class="poster_details big_font" href="' + data.member_received.href + '">' + data.member_received.name + '</a>' + '<div class="poster_details">' + txtStrings.totalPosts + ': ' + data.member_received.total_posts + '</div>' + '</div>';

				htmlContent += '<div class="users_liked">';
				htmlContent += '<p class="title">' + data.member_liked_data.length + ' ' + txtStrings.usersWhoLiked + '</p>';
				for (var i = 0, len = data.member_liked_data.length; i < len; i++) {
					htmlContent += '<a class="poster_details" href="' + data.member_liked_data[i].href + '"><div class="poster_avatar" style="background-image: url(' + encodeURI(data.member_liked_data[i].avatar) + ')" title="' + data.member_liked_data[i].real_name + '"></div></a>';
				}
				htmlContent += '</div>';

				lpObj.jQRef('#like_post_current_tab').text(txtStrings.mostLikedMessage);
				lpObj.jQRef('.like_post_message_data').append(htmlContent).show();

				lpObj.jQRef(".message_title").on('mouseenter', function(e) {
					e.preventDefault();
					var currText = lpObj.jQRef(this).next().html();

					lpObj.jQRef("<div class=\'subject_details\'></div>").html(currText).appendTo("body").fadeIn("slow");
				}).on('mouseleave', function(e) {
					e.preventDefault();

					lpObj.jQRef(".subject_details").fadeOut("slow");
					lpObj.jQRef(".subject_details").remove();
				}).on('mousemove', function(e) {
					e.preventDefault();

					var mousex = e.pageX + 20,
						mousey = e.pageY + 10,
						width = lpObj.jQRef("#wrapper").width() - mousex - 50;

					lpObj.jQRef(".subject_details").css({
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
					topicUrl = smf_scripturl + '?topic=' + data.id_topic;

				lpObj.jQRef('.like_post_topic_data').html('');
				htmlContent += '<a class="topic_title" href="' + topicUrl + '">' + txtStrings.mostPopularTopicHeading1 + ' ' + data.like_count + ' ' + txtStrings.genricHeading1 + '</a>';
				htmlContent += '<p class="topic_info">' + txtStrings.mostPopularTopicSubHeading1 + ' ' + data.msg_data.length + ' ' + txtStrings.mostPopularTopicSubHeading2 + '</p>';

				for (var i = 0, len = data.msg_data.length; i < len; i++) {
					var msgUrl = topicUrl + '.msg' + data.msg_data[i].id_msg;

					htmlContent += '<div class="message_body">' + '<div class="posted_at">' + data.msg_data[i].member.name + ' : ' + txtStrings.postedAt + ' ' + data.msg_data[i].poster_time + '</div> ' + '<a class="poster_details" href="' + data.msg_data[i].member.href + '"><div class="poster_avatar" style="background-image: url(' + encodeURI(data.msg_data[i].member.avatar) + ')"></div></a><div class="content_encapsulate">' + data.msg_data[i].body + '</div><a class="read_more" href="' + msgUrl + '">' + txtStrings.readMore + '</a>' + '</div>';
				}
				lpObj.jQRef('#like_post_current_tab').text(txtStrings.mostLikedTopic);
				lpObj.jQRef('.like_post_topic_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showBoardStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '',
					boardUrl = smf_scripturl + '?board=' + data.id_board;

				lpObj.jQRef('.like_post_board_data').html('');
				htmlContent += '<a class="board_title" href="' + boardUrl + '">' + data.name + ' ' + txtStrings.mostPopularBoardHeading1 + ' ' + data.like_count + ' ' + txtStrings.genricHeading1 + '</a>';
				htmlContent += '<p class="board_info">' + txtStrings.mostPopularBoardSubHeading1 + ' ' + data.num_topics + ' ' + txtStrings.mostPopularBoardSubHeading2 + ' ' + data.topics_liked + ' ' + txtStrings.mostPopularBoardSubHeading3 + '</p>';
				htmlContent += '<p class="board_info extra_margin">' + txtStrings.mostPopularBoardSubHeading4 + ' ' + data.num_posts + ' ' + txtStrings.mostPopularBoardSubHeading5 + ' ' + data.msgs_liked + ' ' + txtStrings.mostPopularBoardSubHeading6 + '</p>';

				for (var i = 0, len = data.topic_data.length; i < len; i++) {
					var topicUrl = smf_scripturl + '?topic=' + data.topic_data[i].id_topic;

					htmlContent += '<div class="message_body">' + '<div class="posted_at">' + data.topic_data[i].member.name + ' : ' + txtStrings.postedAt + ' ' + data.topic_data[i].poster_time + '</div> ' + '<a class="poster_details" href="' + data.topic_data[i].member.href + '"><div class="poster_avatar" style="background-image: url(' + encodeURI(data.topic_data[i].member.avatar) + ')"></div></a><div class="content_encapsulate">' + data.topic_data[i].body + '</div><a class="read_more" href="' + topicUrl + '">' + txtStrings.readMore + '</a></div>';
				}
				lpObj.jQRef('#like_post_current_tab').text(txtStrings.mostLikedBoard);
				lpObj.jQRef('.like_post_board_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showMostLikesReceivedUserStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '';

				lpObj.jQRef('.like_post_most_liked_user_data').html('');
				htmlContent += '<div class="poster_avatar"><div class="avatar" style="background-image: url(' + encodeURI(data.member_received.avatar) + ')"></div></div>' + '<div class="poster_data">' + '<a class="poster_details big_font" href="' + data.member_received.href + '">' + data.member_received.name + '</a>' + '<div class="poster_details">' + txtStrings.totalPosts + ': ' + data.member_received.total_posts + '</div>' + '<div class="poster_details">' + txtStrings.totalLikesReceived + ': ' + data.like_count + '</div>' + '</div>';

				htmlContent += '<p class="generic_text">' + txtStrings.mostPopularUserHeading1 + '</p>';
				for (var i = 0, len = data.topic_data.length; i < len; i++) {
					var msgUrl = smf_scripturl + '?topic=' + data.topic_data[i].id_topic + '.msg' + data.topic_data[i].id_msg;

					htmlContent += '<div class="message_body">' + '<div class="posted_at">' + txtStrings.postedAt + ' ' + data.topic_data[i].poster_time + ': ' + txtStrings.likesReceived + ' (' + data.topic_data[i].like_count + ')</div><div class="content_encapsulate">' + data.topic_data[i].body + '</div><a class="read_more" href="' + msgUrl + '">' + txtStrings.readMore + '</a></div>';
				}
				lpObj.jQRef('#like_post_current_tab').text(txtStrings.mostLikedMember);
				lpObj.jQRef('.like_post_most_liked_user_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			showMostLikesGivenUserStats = function(response) {
				var data = tabsVisitedCurrentSession[currentUrlFrag],
					htmlContent = '';

				lpObj.jQRef('.like_post_most_likes_given_user_data').html('');
				htmlContent += '<div class="poster_avatar"><div class="avatar" style="background-image: url(' + encodeURI(data.member_given.avatar) + ')"></div></div>' + '<div class="poster_data">' + '<a class="poster_details big_font" href="' + data.member_given.href + '">' + data.member_given.name + '</a>' + '<div class="poster_details">' + txtStrings.totalPosts + ': ' + data.member_given.total_posts + '</div>' + '<div class="poster_details">' + txtStrings.totalLikesGiven + ': ' + data.like_count + '</div>' + '</div>';

				htmlContent += '<p class="generic_text">' + txtStrings.mostLikeGivenUserHeading1 + '</p>';
				for (var i = 0, len = data.topic_data.length; i < len; i++) {
					var msgUrl = smf_scripturl + '?topic=' + data.topic_data[i].id_topic + '.msg' + data.topic_data[i].id_msg;

					htmlContent += '<div class="message_body">' + '<div class="posted_at">' + txtStrings.postedAt + ' ' + data.topic_data[i].poster_time + '</div><div class="content_encapsulate">' + data.topic_data[i].body + '</div><a class="read_more" href="' + msgUrl + '">' + txtStrings.readMore + '</a></div>';
				}
				lpObj.jQRef('#like_post_current_tab').text(txtStrings.mostLikeGivingMember);
				lpObj.jQRef('.like_post_most_likes_given_user_data').html(htmlContent).show();
				hideSpinnerOverlay();
			},

			genericErrorMessage = function(params) {
				lpObj.jQRef('.like_post_stats_error').html(params.errorMsg).show();
				hideSpinnerOverlay();
			};

		return {
			init: init,
			checkUrl: checkUrl
		};
	}();

	lpObj.likePostStats = likePostStats.prototype;
}());
