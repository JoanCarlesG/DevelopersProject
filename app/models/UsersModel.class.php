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
        $userName = $this->getUserName($data, $user);

        if (isset($userId)) {
            $_SESSION['userId'] = $userId;
            $_SESSION['email'] = $user;
            $_SESSION['password'] = $pwd;
            $_SESSION['name'] = $userName;
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
                if (password_verify($pwd, $dbUser->pwd)) {
                    return $dbUser->userId;
                }
            }
        }
        return null;
    }
    public function getUserName($data, $user)
    {
        foreach ($data as $dbUser) {
            if (($dbUser->email == $user) || ($dbUser->name == $user)) {
                return $dbUser->name;
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
        if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])) {
            if (!($this->validateLogin())) {
                $name = $_POST['username'];
                $user = $_POST['email'];
                $pwd = password_hash($_POST['password'],PASSWORD_DEFAULT);
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
