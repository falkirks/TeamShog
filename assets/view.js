$(document).ready(function(){
    WordCloud(document.getElementById('wordCloud'), { list: cloudData, gridSize: 16, weightFactor: 10} );
    if(username !== false) {
        var generateRatingBoxes = function () {
            $('.rating-holder .rating-bars').each(function (index) {
                var up = votes[index][0].length;
                var down = votes[index][1].length;
                var total = up + down;
                $(this).html('<div class="progress-bar progress-bar-success" style="width:' + (up / (total)) * 100 + '%">' + up + '</div><div class="progress-bar progress-bar-danger" style="width:' + (down / (total)) * 100 + '%">' + down + '</div>');
            });
        };
        var generateButtons = function () {
            $('.rating-holder .rating-buttons').each(function (index) {
                $(this).html('<a class="btn ' + (inArray(username, votes[index][1]) ? 'btn-default' : 'btn-danger') + ' downvote" sid="' + index + '">Downvote</a><a class="btn ' + (inArray(username, votes[index][0]) ? 'btn-default' : 'btn-success') + ' upvote" sid="' + index + '">Upvote</a>');
            });
        };
        var inArray = function (needle, haystack) {
            var length = haystack.length;
            for (var i = 0; i < length; i++) {
                if (haystack[i] == needle) return true;
            }
            return false;
        };
        var fetchVotes = function(){
            $.ajax({
                url: "/vote/?domain=" + domain + "&doc=" + doc,
                success: function(data){
                    if (data != "false") {
                        votes = JSON.parse(data);
                        generateRatingBoxes();
                        generateButtons()

                    }
                    else{
                        alert("Error fetching votes.");
                    }
                }
            });
        };
        $(document).on("click", '.downvote', function () {
            $.ajax({
                url: "/vote/?dir=down&domain=" + domain + "&doc=" + doc + "&sentence=" + $(this).attr('sid'),
                success: function(data){
                    if (data != "false") {
                        votes = JSON.parse(data);
                        generateRatingBoxes();
                        generateButtons()

                    }
                    else{
                        alert("Vote error.");
                    }
                }
            });
        });
        $(document).on("click", '.upvote', function () {
            $.ajax({
                url: "/vote/?dir=up&domain=" + domain + "&doc=" + doc + "&sentence=" + $(this).attr('sid'),
                success: function(data){
                    if (data != "false") {
                        votes = JSON.parse(data);
                        generateRatingBoxes();
                        generateButtons()

                    }
                    else{
                        alert("Vote error.");
                    }
                }
            });
        });
        fetchVotes();
    }
});
