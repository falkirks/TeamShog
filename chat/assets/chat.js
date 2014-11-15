ws = new WebSocket("ws://localhost:8080/chat");
ws.onopen = function(){
    ws.send("Hello world!");
}