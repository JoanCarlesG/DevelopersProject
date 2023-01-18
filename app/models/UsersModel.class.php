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
        $query = ("SELECT * FROM" . $this->_table);
        $data = mysqli_query($this->getDB(), $query);

        return mysqli_fetch_array($data);
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

        $query = mysqli_query($this->getDB(), "SELECT * FROM users WHERE (name = '$user' OR email = '$user')");

        if ($query) {
            $row = mysqli_fetch_array($query);
            if ($this->validateUser($row, $pwd)) {
                $_SESSION['userId'] = $row['userId'];
                $_SESSION['email'] = $row['email'];
                return true;
            }
        }
            
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
        $dbPwd = password_hash($data['pwd'],PASSWORD_DEFAULT);

        return password_verify($pwd, $dbPwd);
    }

    public function getUserId()
    {
        return $_SESSION['userId'];
    }

    public function addUser()
    {
        if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])) {
            if (!($this->validateLogin())) {
                $name = $_POST['username'];
                $user = $_POST['email'];
                $pwd = $_POST['password'];
                $lastID = $this->getLastUserID();

                $newData = array(
                    array_key_last($this->getData()) => array(
                        "userId" => ++$lastID,
                        "email" => $user,
                        "pwd" => $pwd,
                        "name" => $name,
                    )
                );
                $dbData = $this->getData();
                $mergedData = array_merge($dbData, $newData);
                $this->setData($mergedData);
                return $this->getData();
            }
        }
    }
    public function getLastUserID()
    {
        //Gets last item from the DB to get the "userId" value
        $dbData = $this->getData();
        $lastItem = end($dbData);
        $lastItemId = $lastItem->{"userId"};
        return $lastItemId;
    }
}