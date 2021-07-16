<?
\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->SQL_INJECTION, $_SERVER['REMOTE_ADDR']));
?>