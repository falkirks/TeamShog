$(document).ready(function(){
    WordCloud(document.getElementById('wordCloud'), { list: cloudData, gridSize: 16, minSize: 6, weightFactor: 3} );
});