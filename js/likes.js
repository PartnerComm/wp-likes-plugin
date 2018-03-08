if (typeof PComm === "undefined") {
    var PComm = {};
}
(function($){
    PCLikes = function(){
        var likeUrl = like_ajax.ajaxurl;
        var likeDomain = like_ajax.domain;
        return {
            init: function() {
                var self = this;
                $('.pcLikes').each(function(){
                    if(self.hasLike($(this).data('postId'))) {
                        $('.status', $(this)).removeClass('fa-heart-o').addClass('fa-heart liked');
                    }
                }).unbind('click').click(this.doLike);
            },
            saveLike: function(postId, like) {
                var likes = this.getCookie();
                var exists = false;
                for(i = 0; i < likes.length; i++) {
                    if(likes[i].postId == postId) {
                        exists = true;
                        likes[i].like = like;
                    }
                }
                if(!exists) {
                    likes.push({
                       postId: postId,
                        like: like
                    });
                }
                var data = JSON.stringify(likes);
                document.cookie = "pclikes="+data+"; expires=Fri, 31 Dec 9999 23:59:59 GMT;domain=."+likeDomain+";path=/";
            },
            hasLike: function(postId) {
                var likes = this.getCookie();
                for(i = 0; i < likes.length; i++) {
                    if(likes[i].postId == postId && likes[i].like == 1) {
                        return true;
                    }
                }
                return false;
            },
            getCookie: function() {
                var nameEQ = 'pclikes=';
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return JSON.parse(c.substring(nameEQ.length, c.length));
                }
                return JSON.parse('[]');
            },
            doLike: function(ele) {
                ele.preventDefault();
                ele.stopPropagation();
                var self = this;
                var postId = $(this).data('postId');
                var objectType = $(this).data('type');
                var target = $(this);
                var like = (PComm.likes.hasLike(postId)) ? -1 : 1;
                $.ajax({
                    type: "POST",
                    url: likeUrl,
                    data: ({
                        action: 'like',
                        post: postId,
                        like: like,
                        type: objectType
                    }),
                    success: function (data) {
                        $('span.count', target).html(data);
                        PComm.likes.saveLike(postId, like);
                        var faClass = (like == 1) ? 'fa-heart liked' : 'fa-heart-o';
                        var likesText = (data == 1) ? 'like' : 'likes';
                        $('.status', $(self)).removeClass('fa-heart')
                            .removeClass('fa-heart-o')
                            .addClass(faClass);
                        $('.likes-text', $(self)).text(likesText);

                    }
                });
            }
        }
    };
    PComm.likes = new PCLikes();
    PComm.likes.init();
})(jQuery);