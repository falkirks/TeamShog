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
    $('.rating-buttons').on("click", function(){
        alert($(this).html());
    });
    generateRatingBoxes();
});
