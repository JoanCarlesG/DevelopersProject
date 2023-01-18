<?php

/**
 * Users class for handling users data
 */
class Users extends Model implements UsersInterface
{
    public function __construct()
    {
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);

        $this->_dbh = mysqli_connect($settings['database']['host'], $settings['database']['user'], $settings['database']['password'], $settings['database']['dbname']);
        $this->_setTable("users");
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
        if (!$this->getDB()) {
            die("Connection failed");
        }

        session_start();

        $user = $_POST['email'];
        $pwd = $_POST['password'];

        $userData = mysqli_query($this->getDB(), "SELECT * FROM users WHERE (name = '$user' OR email = '$user')");

        if ($userData) {
            $row = mysqli_fetch_array($userData);
            if ($this->validateUser($row, $pwd)) {
                $_SESSION['userId'] = $row['userId'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['name'] = $row['name'];
                mysqli_close($this->getDB());
                return true;
            }
        }

        mysqli_close($this->getDB());
        return false;
    }

    /**
     * Check if the user data entered in the form are valid for the log in
     * @param mixed $data with users db
     * @param string $user user entered in the form
     * @param string $pwd pwd entered in the form
     * @return mixed userId if user is on db
     */
    public function validateUser($data, $pwd)
    {
        return password_verify($pwd, $data['pwd']);
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
            $pwd = password_hash($_POST['password'],PASSWORD_DEFAULT);
            

            if (!$this->getDB()) {
                die("Connection failed");
            }
            ;

            $query = "INSERT INTO users (name, email, pwd) VALUES ('$name', '$user', '$pwd')";
            $addQuery = mysqli_query($this->getDB(), $query);
            if (!$addQuery) {
                echo "Error: " . $query . "<br>" . mysqli_error($this->getDB());
            }
            mysqli_close($this->getDB());

            return true;
        }
    }
}