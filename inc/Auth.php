<?php
require "DB.php";
class Auth
{
    public static function check()
    {
        if (!isset($_SESSION['token'])) {
            return false;
        }

        $db = new DB();

        $session = $db
            ->select('*')
            ->from('sessions')
            ->where('token', '=', $_SESSION['token'])
            ->where('is_valid', '=', 1)
            ->first();

        if (!$session) {
            return false;
        }

        if (strtotime($session['expires_at']) < time()) {
            return false;
        }

        return true;
    }
}
