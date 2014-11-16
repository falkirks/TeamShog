ws = new WebSocket("ws://localhost:8080");
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
var authed = false;
var channelManager = function(){
    this.chans = {};
    this.currentChannel = false;
    this.renderChannelList = function() {
        var out = '<table class="table table-bordered">';
        for (var chanName in this.chans) {
            if(this.chans.hasOwnProperty(chanName)) {
                if (chanName == this.currentChannel) {
                    out += '<tr class="channelName active"><td>' + chanName + '</td></tr>';
                }
                else if (this.chans[chanName].hasNewMessage) {
                    out += '<tr class="channelName warning"><td>' + chanName + '</td></tr>';
                }
                else {
                    out += '<tr class="channelName"><td>' + chanName + '</td></tr>';
                }
            }
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
                messages: [],
                hasNewMessage: false
            };
        }
    };
    this.setCurrentChan = function(name) {
        this.currentChannel = name;
        this.chans[name].hasNewMessage = false;
        this.renderMessageList();
        this.renderChannelList();
        $("#nameHolder").html(name);
    }
    this.addMessage = function(chan, message){
        this.chans[chan].messages.push(message);
        if(chan == this.currentChannel) {
            this.renderMessageList();
        }
        else{
            this.chans[chan].hasNewMessage = true;
            this.renderChannelList();
        }
    }
};
var channels = new channelManager();

ws.onopen = function(){
    connected = true;
    ws.send(JSON.stringify({
        type: "auth",
        payload: {
            key: session
        }
    }));
};
ws.onclose = function(){
    connected = false;
};
ws.onmessage = function(evt){
    var json = JSON.parse(evt.data);
    switch(json.type){
        case 'message':
            channels.addMessage(json.payload.channel, json.payload.message);
            break;
        case 'authreply':
            if(json.payload.done == true){
                $(target).hide();
                authed = true;
            }
            break;
        case 'channel':
            if(json.payload.verb == "add") {
                channels.addChannel(json.payload.channel);
                channels.renderChannelList();
            }
            else{
                alert("Could not add channel.");
            }
            break;
        default:
            alert("Unrecognized message received from server.");
            break;
    }
};
$("#sendButton").on("click", function(){
    if(channels.currentChannel != false) {
        channels.addMessage(channels.currentChannel, {
            content: $("#messageInput").val(),
            sender: "<mark>" + session.split("$$")[0] + "</mark>"
        });
        ws.send(JSON.stringify({
            type: "message",
            payload: {
                channel: channels.currentChannel,
                message: $("#messageInput").val()
            }
        }));
        $("#messageInput").val('');
        channels.renderMessageList();
    }
});
$("#addChannelButton").on("click", function(){
    ws.send(JSON.stringify({
        type: "channel",
        payload: {
            channel: $("#channelInput").val(),
            verb: "add"
        }
    }));
    $("#channelInput").val('');
});
$('#channelHolder').on('click', 'td', function(){
   channels.setCurrentChan($(this).html());
});