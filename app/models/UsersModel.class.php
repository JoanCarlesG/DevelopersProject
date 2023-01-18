<?php

/**
 * Users class for handling users data
 */
class Users extends Model implements UsersInterface
{
    public function __construct()
    {
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);

        $this->_dbh = mysqli_connect($settings['database']['host'], $settings['database']['user'], $settings['database']['password'],$settings['database']['dbname']);
    }

    public function getDB()
    {
        return $this->_dbh;
    }

    /**
     * Get data from db json file
     * @return array of stdObject that contains db data
     */
    public function getData()
    {
        return (array) json_decode(file_get_contents($this->_dbh, true));
    }

    /**
     * Save data to a db json file
     * @param mixed $data array with stdObject that contains data
     */
    public function setData($data)
    {
        //encode the new array
        $encodedMerge = json_encode($data, JSON_PRETTY_PRINT);

        //put content in DB
        file_put_contents($this->_dbh, $encodedMerge);
    }

    /**
     * Initiates a session if user is on db
     * @return bool if user is on db
     */
    public function validateLogin()
    {
        session_start();

        $user = $_POST['email'];
        $pwd = $_POST['password'];

        $query = mysqli_query($this->_dbh, "SELECT * FROM users WHERE (name = '$user' OR email = '$user') AND pwd = '$pwd'");
        
        if ($query) {
            $row = mysqli_fetch_array($query);
            $_SESSION['userId'] = $row['userId'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['password'] = $row['pwd'];
            $_SESSION['name'] = $row['name'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the user data entered in the form are valid for the log in
     * @param mixed $data with users db
     * @param string $user user entered in the form
     * @param string $pwd pwd entered in the form
     * @return mixed userId if user is on db
     */
    public function validateUser($data, $user, $pwd)
    {
        foreach ($data as $dbUser) {
            if (($dbUser->email == $user) || ($dbUser->name == $user)) {
                if ($dbUser->pwd == $pwd) {
                    return $dbUser->userId;
                }
            }
        }
        return null;
    }

    public function getUserId()
    {
        return $_SESSION['userId'];
    }

    public function addUser()
    {
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
            $name = $_POST['username'];
            $user = $_POST['email'];
            $pwd = $_POST['password'];
            
            if (!$this->getDB()){
                die("Connection failed");
              };

            $query = "INSERT INTO users (name, email, pwd) VALUES ('$name', '$user', '$pwd')";
            $addQuery = mysqli_query($this->getDB(), $query);
            if (!$addQuery){
                echo "Error: " . $query . "<br>" . mysqli_error($this->getDB());
            } 
            mysqli_close($this->getDB());
            
            return true;
        }
    }
}