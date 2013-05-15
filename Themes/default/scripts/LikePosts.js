/**
* @package manifest file for Like Posts
* @version 1.0
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

var likePosts = function() {
}

likePosts.prototype.likeUnlikePosts = function(mId, tId, bId) {
    // Lets try JS validations
    msgId = (mId != undefined) ? parseInt(mId) : 0;
    topicId = (tId != undefined) ? parseInt(tId) : 0;
    boardId = (bId != undefined) ? parseInt(bId) : 0;
    var rating = ($('#like_' + msgId).text().toLowerCase() == 'like') ? 1 : 0;

    if (isNaN(msgId) || isNaN(topicId) || isNaN(boardId)) {
        return false;
    }

    $.ajax({
        type: "POST",
        url: smf_scripturl + '?action=likeposts;sa=like_post',
        context: document.body,
        dataType : "json",
        data: {
            msg: msgId,
            topic: topicId,
            board: boardId,
            rating: rating,
        },

        success: function(resp) {
            if (resp.response) {
                var params = {
                    msgId: msgId,
                    count: (resp.count !== undefined) ? resp.count : '',
                    newText: resp.newText,
                    likeText: resp.likeText
                };
                lpObj.onLikeSuccess(params);
            } else {
                //NOTE: Make an error callback over here
            }
        },
    });
    return true;
}

likePosts.prototype.onLikeSuccess = function(params) {
    var count = parseInt(params.count);
    if(isNaN(count)) return false;

    var likeText = params.likeText.replace(/&amp;/g, '&');
    $('#like_' + params.msgId).text(params.newText);

    if($('#like_count_' + params.msgId).length) {
        if(likeText === '') {
            $('#like_count_' + params.msgId).remove();
        } else {
            $('#like_count_' + params.msgId).text('(' + likeText + ')');
        }
    } else {
        $('#like_post_info_' + params.msgId).append('<span id="like_count_' + params.msgId +'">('+ likeText + ')</span>');
    }
    
}

likePosts.prototype.showMessageLikedInfo = function(messageId) {
    //How about we make a DB call ;)
    if(isNaN(messageId)) return false;

    $.ajax({
        type: "GET",
        url: smf_scripturl + '?action=likeposts;sa=get_message_like_info',
        context: document.body,
        dataType : "json",
        data: {
            msg_id: messageId,
        },

        success: function(resp) {
            if (resp.response) {
                if(resp.data.length <= 0) return false;

                var data = resp.data;
                var memberInfo = '';
                for(i in data) {
                    memberInfo += '<div class="like_posts_member_info"><img class="avatar" src="'+ data[i].avatar.href +'" /><div class="like_posts_member_info_details"><a href="'+ data[i].href +'">' + data[i].name + '</a></div></div>';
                }
                var completeString = '<div class="like_posts_overlay"><div class="like_posts_member_info_box">' + memberInfo + '</div></div>';
                $('body').append(completeString);

                var removeOverlay = function(e) {
                    if ((e.type == 'keyup' && e.keyCode == 27) || e.type == 'click') {
                        $('.like_posts_overlay').remove();
                        $(document).unbind('click', removeOverlay);
                        $(document).unbind('keyup', removeOverlay);
                        $('.like_posts_member_info_box').unbind('click');
                    }
                }
                $(document).one('click keyup', removeOverlay);

                $('.like_posts_member_info_box').click(function(e){
                    e.stopPropagation();
                });
            } else {
                //NOTE: Make an error callback over here
                return false;
            }
        },
    });
}

var lpObj = window.lpObj = new likePosts();