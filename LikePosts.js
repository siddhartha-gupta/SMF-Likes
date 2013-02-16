var likePosts = function() {
}

likePosts.prototype.likeUnlikePosts = function(mId, tId, bId) {
    // Lets try JS validations
    msgId = (mId != undefined) ? parseInt(mId) : 0;
    topicId = (tId != undefined) ? parseInt(tId) : 0;
    boardId = (bId != undefined) ? parseInt(bId) : 0;
    var rating = ($('#like_16').text().toLowerCase() == 'like') ? 1 : 0;

    if(isNaN(msgId) || isNaN(topicId) || isNaN(boardId)) {
        return false;
    }

    //console.log(url);
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
        
        success: function(request){
            if(request.response) {
                console.log('success');
            } else {
                console.log('error');
            }
        },
    });
    return true;
}

var lpObj = window.lpObj = new likePosts();