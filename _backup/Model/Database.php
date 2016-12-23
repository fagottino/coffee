<?php
define('BOT_TOKEN', 'YOUR_TOKEN');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN);
/**
 * Description of Database
 *
 * @author Admin
 */
class Database {
    private $host;
    private $username;
    private $password;
    private $db;
    
    public static function getConnection() {

        $whitelist = array('127.0.0.1', "::1");

        if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            // $connection = new mysqli("your local parameter");
        } else {
            // $connection = new mysqli("your remote connection");
        }
        return $connection;
    }
}

class DatabaseException extends Exception { }
