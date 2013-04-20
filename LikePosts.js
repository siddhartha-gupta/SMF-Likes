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
                    newText: resp.newText
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
    if(isNaN(count)) return;

    $('#like_count_' + params.msgId).text(count);
    $('#like_' + params.msgId).text(params.newText);
    return;
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
                    memberInfo += '<div class="member_info">' + data[i].name + '</div>';
                }
                var completeString = '<div class="like_posts_overlay"><div class="member_info_box">' + memberInfo + '</div></div>';
                $('body').append(completeString);
                $('html').click(function() {
                    $('.like_posts_overlay').remove();
                });
                
                $('.member_info_box').click(function(event){
                    event.stopPropagation();
                });
            } else {
                //NOTE: Make an error callback over here
                return false;
            }
        },
    });
}

var lpObj = window.lpObj = new likePosts();