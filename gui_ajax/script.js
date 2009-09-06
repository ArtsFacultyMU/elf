// record msg IDs

YAHOO.namespace('moodle.chat');
YAHOO.moodle.chat.api = moodle_cfg.wwwroot+'/mod/chat/chat_ajax.php';
YAHOO.moodle.chat.interval = null;
YAHOO.moodle.chat.chat_input_element = null;
YAHOO.moodle.chat.msgs = [];
YAHOO.moodle.chat.scrollable = true;

(function() {
var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event;
// window.onload
Event.onDOMReady(function() {
    // build layout
    var layout = new YAHOO.widget.Layout({
        units: [
        { position: 'top', height: 50, body: 'chat-header', gutter: '5px', resize: false },
        { position: 'right', width: 180, resize: true, gutter: '5px', scroll: true, body: 'chat-userlist', animate: false },
        { position: 'bottom', height: 42, resize: false, body: 'chat-input', gutter: '5px', collapse: false, resize: false },
        //{ position: 'left', header: 'Options', width: 200, resize: true, body: 'chat_options', gutter: '5px', collapse: true, close: true, collapseSize: 50, scroll: true, animate: false },
        { position: 'center', body: 'chat-messages', gutter: '5px', scroll: true }
        ]
    });
    layout.on('render', function() {
        layout.getUnitByPosition('left').on('close', function() {
            closeLeft();
        });
    });
    layout.render();
    Event.on('btn_send', 'click', function(ev) {
        Event.stopEvent(ev);
        YAHOO.moodle.chat.send_message();
    });
    Event.on('chat-messages', 'mouseover', function(ev) {
        Event.stopEvent(ev);
        YAHOO.moodle.chat.scrollable = false;
    });
    Event.on('chat-messages', 'mouseout', function(ev) {
        Event.stopEvent(ev);
        YAHOO.moodle.chat.scrollable = true;
    });
    YAHOO.moodle.chat.chat_input_element = document.getElementById('input_msgbox');
    YAHOO.moodle.chat.chat_input_element.onkeypress = function(ev) {
        var e = (ev)?ev:event;
        if (e.keyCode == 13) {
            YAHOO.moodle.chat.send_message();
        }
    }
    document.title = chat_cfg.chatroom_name;

    this.cb = {
        success: function(o){
            YAHOO.moodle.chat.chat_input_element.focus();
            if(o.responseText){
                var data = YAHOO.lang.JSON.parse(o.responseText);
            } else {
                return;
            }
            YAHOO.moodle.chat.update_users(data.users);
        }
    }
    var params = {};
    params.action = 'init';
    params.chat_init = 1;
    params.chat_sid = chat_cfg.sid;
    var trans = YAHOO.util.Connect.asyncRequest('POST', YAHOO.moodle.chat.api, this.cb, build_querystring(params));
    YAHOO.moodle.chat.interval = setInterval(function(){
        YAHOO.moodle.chat.update_messages();
    }, chat_cfg.timer);
});
})();

function in_array(f, t){
    var a = false;
    for( var i = 0; i<t.length; i++){
        if(f==t[i]){
            a=true;
            break;
        }
    }
    return a;
}

YAHOO.moodle.chat.talkto = function(name) {
    var msg = document.getElementById('input_msgbox');
    msg.value = "To "+name+": ";
    msg.focus();
}

YAHOO.moodle.chat.send_callback = {
    success: function(o) {
        if(o.responseText == 200){
            document.getElementById('btn_send').value = mstr.chat.send;
            document.getElementById('input_msgbox').value = '';
        }

        clearInterval(YAHOO.moodle.chat.interval)
        YAHOO.moodle.chat.update_messages();
        YAHOO.moodle.chat.interval = setInterval(function(){
            YAHOO.moodle.chat.update_messages();
        }, chat_cfg.timer);
        //document.getElementById('input_msgbox').focus();
    }
}
YAHOO.moodle.chat.send_message = function(ev) {
    var msg = document.getElementById('input_msgbox').value;
    var el_send = document.getElementById('btn_send');
    if (!msg) {
        alert('Empty message not allowed');
        return;
    }
    el_send.value = mstr.chat.sending;
    var params = {};
    params.chat_message=msg;
    params.chat_sid=chat_cfg.sid;
    var trans = YAHOO.util.Connect.asyncRequest('POST', YAHOO.moodle.chat.api+"?action=chat", YAHOO.moodle.chat.send_callback, build_querystring(params));
}

YAHOO.moodle.chat.send_beep = function(user_id) {
    var url = 'post.php?chat_sid='+chat_cfg.sid;
    var params = {};
    params.chat_sid = chat_cfg.sid;
    params.beep=user_id;
    var trans = YAHOO.util.Connect.asyncRequest('POST', YAHOO.moodle.chat.api+"?action=chat", YAHOO.moodle.chat.send_callback, build_querystring(params));
}

YAHOO.moodle.chat.update_users = function(users) {
    if(!users){
        return;
    }
    var list = document.getElementById('users-list');
    list.innerHTML = '';
    for(var i in users){
        var el = document.createElement('li');
        var html = '';
        html += '<table>';
        html += '<tr>';
        html += '<td>' + users[i].picture + '</td>';
        html += '<td>';
        html += ' <a target="_blank" href="'+users[i].url+'">'+ users[i].name+'<br/>';
        html += ' <a href="###" onclick="YAHOO.moodle.chat.talkto(\''+users[i].name+'\')">'+mstr.chat.talk+'</a> ';
        html += ' <a href="###" onclick="YAHOO.moodle.chat.send_beep('+users[i].id+')">'+mstr.chat.beep+'</a>';
        html += '</td>';
        html += '</tr>';
        html += '</table>';
        el.innerHTML = html;
        list.appendChild(el);
    }
}

YAHOO.moodle.chat.update_messages = function() {
    if(!chat_cfg.req_count){
        chat_cfg.req_count = 1;
    } else {
        chat_cfg.req_count++;
    }
    var params = {};
    if(chat_cfg.chat_lastrow != null){
        params.chat_lastrow = chat_cfg.chat_lastrow;
    }
    params.chat_lasttime = chat_cfg.chat_lasttime;
    params.chat_sid = chat_cfg.sid;
    var trans = YAHOO.util.Connect.asyncRequest('POST', YAHOO.moodle.chat.api+"?action=update", YAHOO.moodle.chat.update_cb, build_querystring(params));
}

YAHOO.moodle.chat.mymsg_cfg = {
    color: { to: '#06e' },
    backgroundColor: { to: '#e06' }
};
YAHOO.moodle.chat.oddmsg_cfg = {
    color: { to: 'red' },
    backgroundColor: { to: '#FFFFCC' }
};
YAHOO.moodle.chat.evenmsg_cfg = {
    color: { to: 'blue' }
};

YAHOO.moodle.chat.append_msg = function(key, msg, row) {
    var list = document.getElementById('messages-list');
    var item = document.createElement('li');
    item.id="mdl-chat-entry-"+key;
    if (msg.mymessage) {
        item.className = 'mdl-chat-my-entry';
    } else {
        item.className = 'mdl-chat-entry';
    }
    item.innerHTML = msg.msg;
    if(msg.type && msg.type == 'beep'){
        document.getElementById('notify').innerHTML = '<embed src="../beep.wav" autostart="true" hidden="true" name="beep" />';
    }
    list.appendChild(item);
    if (!row) {
        var anim = new YAHOO.util.ColorAnim(item.id, YAHOO.moodle.chat.oddmsg_cfg);
        anim.animate(); 
    }
    if (msg.mymessage) {
        //var anim = new YAHOO.util.ColorAnim(item.id, YAHOO.moodle.chat.mymsg_cfg);
        //anim.animate(); 
    }
}

YAHOO.moodle.chat.update_cb = {
    success: function(o){
        var data = json_decode(o.responseText);
        if (!data) {
            return;
        }
        if(data.error) {
            if(data.error.level == 'ERROR'){
                clearInterval(YAHOO.moodle.chat.interval);
                window.location = chat_cfg.home;
            }
        }
        chat_cfg.chat_lasttime = data['lasttime'];
        chat_cfg.chat_lastrow  = data['lastrow'];
        // update messages
        for (key in data['msgs']){
            if(!in_array(key, YAHOO.moodle.chat.msgs)){
                YAHOO.moodle.chat.msgs.push(key);
                YAHOO.moodle.chat.append_msg(key, data['msgs'][key], data.lastrow);
            }
        }
        // update users
        YAHOO.moodle.chat.update_users(data['users']);
        // scroll to the bottom of the message list
        if(YAHOO.moodle.chat.scrollable){
            document.getElementById('chat-messages').parentNode.scrollTop+=500;
        }
        YAHOO.moodle.chat.chat_input_element.focus();
    }
}
