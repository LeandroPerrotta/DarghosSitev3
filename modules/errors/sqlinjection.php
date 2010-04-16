<?
Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_SQL_INJECTION, $_SERVER['REMOTE_ADDR']));
?>