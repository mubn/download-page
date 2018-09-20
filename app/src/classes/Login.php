<?php
namespace Tools;

use Mysqli;
use Tools\Constants;

//Config
//require_once("config/constants.php");

class Login
{
    //The database connection
    private $db_connection = null;

    public function __construct()
    {
        session_start();
    }

    public function doLoginWithPostData()
    {
        // check login form contents
        if (empty($_POST['user_name'])) {
            throw new Exception('Username field was empty.');
        } elseif (empty($_POST['user_password'])) {
            throw new Exception('Password field was empty.');
        } else {
            // create a database connection, using the constants
            $constants = new Constants();
            $this->db_connection = new mysqli($constants::DB_HOSTNAME, $constants::DB_USERNAME, $constants::DB_PASSWORD, $constants::DB_DATABASE);
            // if no connection errors
            if (!$this->db_connection->connect_errno) {
                // escape the POST stuff
                $user_name = $this->db_connection->real_escape_string($_POST['user_name']);
                // database query, getting all the info of the selected user (allows login via email address in the
                // username field)
                $sql = "SELECT user_name, user_email, user_password_hash
                    FROM users
                    WHERE user_name = '" . $user_name . "' OR user_email = '" . $user_name . "';";
                $result_of_login_check = $this->db_connection->query($sql);
                // if this user exists
                if ($result_of_login_check->num_rows === 1) {
                    // get result row (as an object)
                    $result_row = $result_of_login_check->fetch_object();
                    // using PHP 5.5's password_verify() function to check if the provided password fits
                    // the hash of that user's password
                    if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {
                        // write user data into PHP SESSION (a file on your server)
                        $_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['user_login_status'] = 1;

                        return "You have been logged in.";
                    } else {
                        throw new Exception('Wrong password. Try again.');
                    }
                } else {
                    throw new Exception('This user does not exist.');
                }
                // default return
                return false;
            } else {
                throw new Exception('Database connection problem.');
            }
        }
    }

    public function doLogout()
    {
        // delete the session of the user
        $_SESSION = array();
        session_destroy();
        // return a little feeedback message
        return "You have been logged out.";
    }

    public function isUserLoggedIn()
    {
        if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] === 1) {
            return 'Logged in.';
        }
        // default return
        return 'Not logged in.';
    }
}
