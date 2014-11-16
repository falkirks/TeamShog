ws = new WebSocket("ws://localhost:8080/chat");
var opts = {
    lines: 11, // The number of lines to draw
    length: 18, // The length of each line
    width: 10, // The line thickness
    radius: 30, // The radius of the inner circle
    corners: 1, // Corner roundness (0..1)
    rotate: 0, // The rotation offset
    direction: 1, // 1: clockwise, -1: counterclockwise
    color: '#fff', // #rgb or #rrggbb or array of colors
    speed: 1, // Rounds per second
    trail: 60, // Afterglow percentage
    shadow: false, // Whether to render a shadow
    hwaccel: false, // Whether to use hardware acceleration
    className: 'spinner', // The CSS class to assign to the spinner
    zIndex: 2e9, // The z-index (defaults to 2000000000)
    top: '50%', // Top position relative to parent
    left: '50%' // Left position relative to parent
};
var target = document.getElementById('spinner');
var spinner = new Spinner(opts).spin(target);
var connected = false;
var channelManager = function(){
    this.chans = {};
    this.currentChannel = false;
    this.renderChannelList = function() {
        var out = '<table class="table table-bordered">';
        for (var chanName in this.chans) {
            out += '<tr class="channelName"><td>' + chanName + '</td></tr>';
        }
        out += "</table>";
        $("#channelHolder").html(out);
    };
    this.renderMessageList = function(){
        if(this.currentChannel != false){
            var out = '<table class="table">';
            for(var i = 0; i < this.chans[this.currentChannel].messages.length; i++){
                out += '<tr class="channelName">' + '<td style="width: 85px"><b>' + this.chans[this.currentChannel].messages[i].sender + "</b></td> <td>" + this.chans[this.currentChannel].messages[i].content + "</td>";
            }
            out += "</table>";
            $("#messageHolder").html(out);
        }
    };
    this.addChannel = function(name){
        if(this.chans[name] == null){
            this.chans[name] = {
                messages: []
            };
        }
    };
    this.setCurrentChan = function(name) {
        this.currentChannel = name;
        this.renderMessageList();
        $("#nameHolder").html(name);
    }
    this.addMessage = function(chan, message){
        this.chans[chan].messages.push(message);
    }
};
var channels = new channelManager();

ws.onopen = function(){
    $(target).hide();
    connected = true;
};
ws.onclose = function(){
    connected = false;
};
ws.onmessage = function(){

};
$("#sendButton").on("click", function(){
    channels.addMessage(channels.currentChannel, {
        content: $("#messageInput").val(),
        sender: "You"
    });
    $("#messageInput").val('');
    console.log(channels.chans);
    channels.renderMessageList();
});
$("#addChannelButton").on("click", function(){
    channels.addChannel($("#channelInput").val());
    $("#channelInput").val('');
    channels.renderChannelList();
});
$('#channelHolder').on('click', 'td', function(){
   channels.setCurrentChan($(this).html());
});