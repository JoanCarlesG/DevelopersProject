<?php

/**
 * Users class for handling users data
 */
class Users extends Model
{
    public function __construct()
    {
        // parse the settings file
        $this->_dbh = ROOT_PATH . '/web/' . 'db_users.json';
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
        $data = $this->getData();
        $user = $_POST['email'];
        $pwd = $_POST['password'];

        $userId = $this->validateUser($data, $user, $pwd);

        if (isset($userId)) {
            session_start();
            $_SESSION['userId'] = $userId;
            $_SESSION['email'] = $user;
            $_SESSION['password'] = $pwd;
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
}