$(document).ready(function(){
    WordCloud(document.getElementById('wordCloud'), { list: cloudData, gridSize: 16, weightFactor: 10} );
    var generateRatingBoxes = function(){
        $('.rating-holder .rating-bars').each(function(index){
            var up = votes[index][0].length;
            var down = votes[index][1].length;
            var total = up+down;
            $(this).html('<div class="progress-bar progress-bar-success" style="width:'+ (up/(total))*100 + '%">'+ up + '</div><div class="progress-bar progress-bar-danger" style="width:' + (down/(total))*100 + '%">' + down + '</div>');
        });
    };
    var generateButtons = function(){
        $('.rating-holder .rating-buttons').each(function(index){
            $(this).html('<a class="btn ' + (inArray(username, votes[index][1]) ? 'btn-default' : 'btn-danger') + ' downvote" sid="' + index + '">Downvote</a><a class="btn ' + (inArray(username, votes[index][0]) ? 'btn-default' : 'btn-success') + ' upvote" sid="' + index + '">Upvote</a>');
        });
    };
    var inArray = function(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    };
    $('.upvote').on("click", function(){
        alert($(this).html());
    });
    $('.downvote').on("click", function(){
        alert($(this).html());
    });
    generateRatingBoxes();
    generateButtons();
});
