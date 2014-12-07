$(document).ready(function(){
    WordCloud(document.getElementById('wordCloud'), { list: cloudData, gridSize: 16, weightFactor: 10} );
    var generateRatingBoxes = function(){
        $('.rating-holder .rating-bars').each(function(index){
            alert(index);
        });
    };
    generateRatingBoxes();
});
