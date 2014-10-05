/**
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

// Root JS object of the mod
(function(win) {
	win.lpObj = win.lpObj || {};

	lpObj.timeoutTimer = null;
	if (typeof(lpObj.jQRef) !== 'function' && typeof(lpObj.jQRef) === 'undefined') {
		lpObj.jQRef = lp_jquery2_0_3;
	}

	lpObj.jQRef(document).ready(function() {
		lpObj.jQRef(".some_data").on('mouseenter', function(e) {
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
	});
})(window);


(function() {
	function likePostsUtils() {}

	likePostsUtils.prototype = function() {
		var removeOverlay = function(e) {
				if (typeof(e) === 'undefined' && lpObj.timeoutTimer === null) return false;

				else if (lpObj.timeoutTimer !== null || ((e.type == 'keyup' && e.keyCode == 27) || e.type == 'click')) {
					clearTimeout(lpObj.timeoutTimer);
					lpObj.timeoutTimer = null;
					lpObj.jQRef('.like_posts_overlay').remove();
					lpObj.jQRef('.like_posts_overlay').unbind('click');
					lpObj.jQRef(document).unbind('click', lpObj.removeOverlay);
					lpObj.jQRef(document).unbind('keyup', lpObj.removeOverlay);
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

			likeUnlikePosts = function(e, mId, tId, bId, aId) {
				if (isLikeAjaxInProgress === true) return false;

				var userRating = e.target.href.split('#')[1],
					msgId = (mId !== undefined) ? parseInt(mId, 10) : 0,
					topicId = (tId !== undefined) ? parseInt(tId, 10) : 0,
					boardId = (bId !== undefined) ? parseInt(bId, 10) : 0,
					authorId = (aId !== undefined) ? parseInt(aId, 10) : 0,
					rating = (userRating !== undefined) ? parseInt(userRating, 10) : 0;

				if (isNaN(msgId) || isNaN(topicId) || isNaN(boardId) || isNaN(authorId)) {
					return false;
				}

				isLikeAjaxInProgress = true;
				lpObj.jQRef.ajax({
					type: "POST",
					url: smf_scripturl + '?action=likeposts;sa=like_post',
					dataType: "json",
					data: {
						msg: msgId,
						topic: topicId,
						board: boardId,
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

				if (likeText.indexOf('&amp;') > 0) {
					likeText = likeText.replace(/&amp;/g, '&');
				}

				lpObj.jQRef(likeButtonRef).attr('href', newLink);
				lpObj.jQRef(likeButtonRef).animate({
					left: '-40px',
					opacity: 'toggle'
				}, 1000, '', function() {
					lpObj.jQRef(likeButtonRef).text(params.newText);

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
							lpObj.jQRef(this).text('(' + likeText + ')').fadeIn(1000);
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

						lpObj.jQRef(document).one('click keyup', lpObj.likePostsUtils.removeOverlay);
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
		var showLikeNotification = function() {
				lpObj.jQRef.ajax({
					type: "GET",
					url: smf_scripturl + '?action=likepostsdata;sa=like_posts_notification',
					context: document.body,
					dataType: "json",

					success: function(resp) {
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

							notificationInfo += '<div class="lp_notification_header"><div class="lp_notification_tabs" id="lp_all_notifications">All Notification</div><div class="lp_notification_tabs" id="lp_my_notifications">My Posts</div><div class="lp_notification_tabs close_btn" id="close_notifications">X</div></div>';

							for (i in data) {
								if (data.hasOwnProperty(i)) {
									if (i === 'all') {
										notificationInfo += '<div class="lp_notification_body lp_all_notifications_data">';
										var len = 0;
										if (data[i].length === 0) {
											notificationInfo += '<div class="single_notify">Nothing to show at the moment</div>';
										} else {
											for (j in data[i]) {
												if (data[i].hasOwnProperty(j)) {
													len++;
													notificationInfo += '<div class="single_notify"><img class="avatar" src="' + data[i][j].member.avatar.href + '" /><div class="like_post_notify_data"><a href="' + data[i][j].member.href + '"><strong>' + data[i][j].member.name + '</strong></a> liked ' + '<a href="' + data[i][j].href + '">' + data[i][j].subject + '</a></div></div>';
												}
											}
										}
										dataLengthAll = len;
										notificationInfo += '</div>';
									} else if (i === 'mine') {
										notificationInfo += '<div class="lp_notification_body lp_my_notifications_data" style="display: none">';
										var len = 0;
										if (data[i].length === 0) {
											notificationInfo += '<div class="single_notify">Nothing to show at the moment</div>';
										} else {
											for (k in data[i]) {
												if (data[i].hasOwnProperty(k)) {
													len++;
													notificationInfo += '<div class="single_notify"><img class="avatar" src="' + data[i][k].member.avatar.href + '" /><div class="like_post_notify_data"><a href="' + data[i][k].member.href + '"><strong>' + data[i][k].member.name + '</strong></a> liked ' + '<a href="' + data[i][k].href + '">' + data[i][k].subject + '</a></div></div>';
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
										lpObj.removeNotification(e);
										break;

									default:
										break;
								}
							});
							lpObj.jQRef(document).on('click keyup', lpObj.removeNotification);
						} else {
							//NOTE: Make an error callback over here
							return false;
						}
					}
				});
			},

			removeNotification = function(e) {
				if ((e.type == 'keyup' && e.keyCode == 27) || e.type == 'click') {
					var container = lpObj.jQRef('#lp_all_notifications, #lp_my_notifications');
					if (!container.is(e.target) && container.has(e.target).length === 0) {
						lpObj.jQRef('.like_posts_notification').unbind('click');
						lpObj.jQRef('.like_posts_notification').unbind('keyup');
						lpObj.jQRef('.lp_notification_header').unbind('click');
						lpObj.jQRef(document).unbind('click', lpObj.removeNotification);
						lpObj.jQRef(document).unbind('keyup', lpObj.removeNotification);
						lpObj.jQRef('.like_posts_notification').remove();
					}
				}
			};

		return {
			'showLikeNotification': showLikeNotification,
			'removeNotification': removeNotification
		};
	}();
	lpObj.likePostsNotification = likePostsNotification.prototype;
})();

// // some admin related functions
// likePosts.prototype.recountStats = function(options) {
// 	if (!options.activity) return false;

// 	var activity = options.activity,
// 		totalWork = options.totalWork || 0,
// 		startLimit = options.startLimit || 0,
// 		increment = options.increment || 100,
// 		endLimit = options.endLimit || 100;

// 	lpObj.jQRef.ajax({
// 		type: "POST",
// 		url: smf_scripturl + '?action=admin;area=likeposts;sa=recountlikestats',
// 		dataType: "json",
// 		data: {
// 			'activity': activity,
// 			'totalWork': totalWork,
// 			'startLimit': startLimit,
// 			'endLimit': endLimit
// 		},

// 		success: function(resp) {
// 			resp.activity = activity;
// 			resp.increment = increment;
// 			resp.startLimit = startLimit;
// 			lpObj.checkRecount(resp);
// 		},
// 		error: function(err) {
// 			console.log(err);
// 		}
// 	});
// };

// likePosts.prototype.checkRecount = function(obj) {
// 	var startLimit,
// 		percentage = 0,
// 		percentageText = '0%';

// 	if (obj.startLimit === 0) {
// 		var completeString = '<div class="like_posts_overlay"><div class="recount_stats"><div></div></div></div>';

// 		lpObj.jQRef('body').append(completeString);

// 		var screenWidth = lpObj.jQRef(window).width(),
// 			screenHeight = lpObj.jQRef(window).height(),
// 			popupHeight = lpObj.jQRef('.recount_stats').outerHeight(),
// 			popupWidth = lpObj.jQRef('.recount_stats').outerWidth(),
// 			topPopUpOffset = (screenHeight - popupHeight) / 2,
// 			leftPopUpOffset = (screenWidth - popupWidth) / 2;

// 		lpObj.jQRef('.recount_stats').css({
// 			top: topPopUpOffset + 'px',
// 			left: leftPopUpOffset + 'px'
// 		});
// 		startLimit = obj.startLimit + obj.increment + 1;
// 	} else {
// 		startLimit = obj.startLimit + obj.increment;
// 	}

// 	if (startLimit < obj.totalWork) {
// 		var endLimit = obj.endLimit + obj.increment;
// 		if (endLimit > obj.totalWork) {
// 			endLimit = Math.abs(obj.endLimit - obj.totalWork) + obj.endLimit;
// 		}
// 		percentage = Math.floor((obj.endLimit / obj.totalWork) * 100);
// 		percentageText = percentage + '%';
// 	} else {
// 		percentage = 100;
// 		percentageText = 'Done';
// 		lpObj.jQRef(document).one('click keyup', lpObj.removeOverlay);
// 	}

// 	lpObj.jQRef('.recount_stats').find('div').animate({
// 		width: percentage + '%'
// 	}, 1000, function() {
// 		if (percentage < 100) {
// 			lpObj.recountStats({
// 				'activity': obj.activity,
// 				'totalWork': obj.totalWork,
// 				'startLimit': startLimit,
// 				'endLimit': endLimit
// 			});
// 		}
// 	}).html(percentageText + '&nbsp;');
// };

// likePosts.prototype.selectInputByLegend = function(event, elem) {
// 	event.preventDefault();

// 	var elemRef = lpObj.jQRef(elem),
// 		parent = elemRef.parent();

// 	if (elemRef.data('allselected') === false) {
// 		parent.find('input:checkbox').prop('checked', true);
// 		elemRef.data('allselected', true);
// 	} else {
// 		parent.find('input:checkbox').prop('checked', false);
// 		elemRef.data('allselected', false);
// 	}
// };

// likePosts.prototype.selectAllBoards = function(event) {
// 	var elemRef = lpObj.jQRef('#lp_board_settings fieldset');

// 	if (lpObj.jQRef(event.target).is(':checked')) {
// 		elemRef.find('input:checkbox').prop('checked', true);
// 	} else {
// 		elemRef.find('input:checkbox').prop('checked', false);
// 	}
// };
