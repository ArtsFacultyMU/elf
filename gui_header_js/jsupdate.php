<?php

require("../../../config.php");
require("../lib.php");

if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
    echo "Not logged in!";
    die;
}

if (!$chat = get_record("chat", "id", $chatuser->chatid)) {
    error("No chat found");
}

require_login($chat->course);


if ($message = chat_get_latest_message($chatuser->chatid)) {
    $chat_newlasttime = $message->timestamp;
} else {
    $chat_newlasttime = 0;
}

if (empty($chat_lasttime)) {
    $chat_lasttime = 0;
}


header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html");
header("Refresh: 4; URL=jsupdate.php?chat_sid=".$chat_sid."&chat_lasttime=".$chat_newlasttime);

?>
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
  <html>
   <head>
    <script language="Javascript">
    <!--
<?php
     if ($chat_lasttime) {
         if ($messages = get_records_select("chat_messages", 
                                            "chatid = '$chatuser->chatid' AND timestamp > '$chat_lasttime'", 
                                            "timestamp ASC")) {
             foreach ($messages as $message) {
                 $formatmessage = chat_format_message($message->userid, $message->chatid, 
                                                      $message->timestamp, $message->message, $message->system);
?>
                 parent.msg.document.write('<?php echo $formatmessage ?>\n');
<?php
             }
         }
     }

     $chatuser->lastping = time();
     update_record("chat_users", $chatuser);
     ?>
     parent.msg.scroll(1,5000000);
    // -->
    </script>
   </head>
   <body bgcolor="<?php echo $THEME->body ?>">
   </body>
  </html>
