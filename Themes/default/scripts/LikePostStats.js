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

var likePostStats = function() {
	this.currentUrlFrag = null;
	this.allowedUrls = {
		'messagestats': {
			'uiFunc': likePostStats.prototype.getMessageStats
		},
		'topicsstats': {

		},
		'boardstats': {

		},
		'userstats': {

		}
	};
	this.tabsVisitedCurrentSession = [];
};

likePostStats.prototype.showSpinner = function() {
	lpStatsObj.jQRef('#lp_spinner').show();
};

likePostStats.prototype.hideSpinner = function() {
	lpStatsObj.jQRef('#lp_spinner').hide();
};

likePostStats.prototype.checkUrl = function() {
	var currentHref = window.location.href.split('#');
	lpStatsObj.currentUrlFrag = (typeof(currentHref[1]) !== 'undefined') ? currentHref[1] : 'messagestats';

	if (lpStatsObj.allowedUrls.hasOwnProperty(lpStatsObj.currentUrlFrag) === false) {
		lpStatsObj.currentUrlFrag = 'messagestats';
	}

	if(lpStatsObj.tabsVisitedCurrentSession.indexOf(lpStatsObj.currentUrlFrag) < 0) {
		lpStatsObj.tabsVisitedCurrentSession.push(lpStatsObj.currentUrlFrag);
		lpStatsObj.getDataFromServer({
			'url': lpStatsObj.currentUrlFrag,
			'uiFunc': lpStatsObj.allowedUrls[lpStatsObj.currentUrlFrag].uiFunc
		});
	}
};

likePostStats.prototype.getDataFromServer = function(params) {
	lpObj.jQRef.ajax({
		type: "POST",
		url: smf_scripturl + '?action=likepostsstatsajax;sa=' + params.url,
		context: document.body,
		dataType: "json",

		success: function(resp) {
			if (resp.response) {
				console.log(response);
			} else {
				//NOTE: Make an error callback over here
			}
		}
	});
};

likePostStats.prototype.getMessageStats = function() {
	console.log('test');
};

var lpStatsObj = window.lpStatsObj = new likePostStats();
if (typeof(lpStatsObj.jQRef) !== 'function' && typeof(lpStatsObj.jQRef) === 'undefined') {
	lpStatsObj.jQRef = jQuery.noConflict();
}

(function() {
	lpStatsObj.jQRef(document).ready(function() {
		console.log('stats is active');
		likePostStats.prototype.checkUrl();
	});
})();
