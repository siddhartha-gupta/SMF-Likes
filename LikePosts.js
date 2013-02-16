var likePosts = function() {
}

likePosts.prototype.likeUnlikePosts = function(msgId, topicId, boardId) {
    // Lets try JS validations
    msgId = (msgId != undefined) ? parseInt(msgId) : 0;
    topicId = (topicId != undefined) ? parseInt(topicId) : 0;
    boardId = (boardId != undefined) ? parseInt(boardId) : 0;
    
    if(isNaN(msgId) || isNaN(topicId) || isNaN(boardId)) {
        return false;
    }

    //console.log(url);
    $.ajax({
        type: "POST",
        url: smf_scripturl + '?action=likeposts;sa=like_post;xml',
        context: document.body,
        data: {
            msg: msgId,
            topic: topicId,
            board: boardId
        },
        
        success: function(request){
            console.log(request);
        },
    });
}

var lpObj = new likePosts();