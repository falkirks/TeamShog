$(document).ready(function(){
    WordCloud(document.getElementById('wordCloud'), { list: cloudData, gridSize: 16, weightFactor: 10} );
});