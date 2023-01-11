<?php

/**
 * Base model for the application.
 * Add general things in this model.
 */
class Tasks extends Model
{
    public function __construct()
    {
        // parse the settings file
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);


        $db = $settings['database']['dbname'];
        $this->_dbh = ROOT_PATH . '/web/' . $db . '.json';
    }

    public function getDB()
    {
        return $this->_dbh;
    }
    // parse json db into an array
    public function getData()
    {
        return (array) json_decode(file_get_contents($this->_dbh, true));
    }

    public function setData($data)
    {
        //encode the new array
        $encodedMerge = json_encode($data, JSON_PRETTY_PRINT);

        //put content in DB
        file_put_contents($this->_dbh, $encodedMerge);
    }

    public function addTask($newData)
    {
        //DB decode to array, merge the 2 arrays, encode the new array, put contents in DB. Returns DB updated and decoded.
        $dbData = $this->getData();
        $mergedData = array_merge($dbData, $newData);
        $encodedMerge = json_encode($mergedData, JSON_PRETTY_PRINT);
        file_put_contents($this->_dbh, $encodedMerge);
        return (array) json_decode(file_get_contents($this->_dbh));
    }

    public function listTasks($user_id)
    {
        $data = $this->getData();
        $user_data = array();
        foreach ($data as $task) {
            if ($task->user_id == $user_id) {
                array_push($user_data, $task);
            }
        }
        return $user_data;
    }

    public function deleteTask()
    {
    }

    public function updateTask($data, $task_id)
    {
        //modify task_id task on $data
        foreach ($data as $task) {
            if (($task->task_id == $task_id) && ($task->user_id == $_SESSION['user_id'])) {
                $task->title = $_POST['title'];
                $task->desc = $_POST['desc'];
                if (($task->status != 'DONE') && ($_POST['status'] == 'DONE'))
                    $task->end_date = $this->setDate();
                $task->status = $_POST['status'];
                if (($task->status != 'DONE') && isset($task->end_date)) 
                    $task->end_date = "";
                $task->mod_date = $this->setDate();
            }
        }

        //save new data on db
        $this->setData($data);
    }

    public function getTask($task_id)
    {
        $data = $this->getData();
        foreach ($data as $task) {
            if ($task->task_id == $task_id)
                return $task;
        }
        return null;
    }

    public function setDate()
    {
        //Sets timestamp in this format => Hour:Min:Sec Day/Month/Year
        return (date('h:i:s a d/m/Y', time()));
    }

    public function getLastTaskID()
    {
        //Gets last item from the DB to get the "task_id" value
        $dbData = $this->getData();
        $lastItem = end($dbData);
        $lastItemId = $lastItem->{"task_id"};
        return $lastItemId;
    }
    public function validate_login()
    {
        $data = $this->getData();
        $user = $_POST['email'];
        $pwd = $_POST['password'];

        $user_id = $this->validate_user($data, $user, $pwd);

        if (isset($user_id)) {
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $user;
            $_SESSION['password'] = $pwd;
            return true;
        } else {
            return false;
        }
    }

    public function validate_user($data, $user, $pwd)
    {
        foreach ($data as $task) {
            if (($task->email == $user) || ($task->name == $user)) {
                if ($task->pwd == $pwd) {
                    return $task->user_id;
                }
            }
        }

        return null;
    }

    public function get_user_id()
    {
        return $_SESSION['user_id'];
    }


}