<?php

/**
 * Tasks class for handling tasks data
 */
class Tasks extends Model implements TasksInterface
{
    public function __construct()
    {
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);

        $this->_dbh = mysqli_connect($settings['database']['host'], $settings['database']['user'], $settings['database']['password'], $settings['database']['dbname']);
        $this->_setTable("tasks");
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
     * Save data to a json file
     * @param mixed $data array with stdObject that contains data
     */
    public function setData($data)
    {
        //encode the new array
        $encodedMerge = json_encode($data, JSON_PRETTY_PRINT);

        //put content in DB
        file_put_contents($this->_dbh, $encodedMerge);
    }

    public function addTask()
    {
        if (isset($_POST["title"]) && isset($_POST["desc"]) && isset($_POST["status"])) {
            $userId = $this->getUserId();
            $title = $_POST['title'];
            $description = $_POST['desc'];
            $status = $_POST['status'];
            $startDate = $this->setDate();
            
            if (!$this->getDB()){
                die("Connection failed");
              };

            $query = "INSERT INTO tasks (userId, title, description, status, startDate) 
                        VALUES ($userId, '$title', '$description', '$status', $startDate)";
            $addQuery = mysqli_query($this->getDB(), $query);
            if (!$addQuery){
                echo "Error: " . $query . "<br>" . mysqli_error($this->getDB());
            } 
            mysqli_close($this->getDB());
            
            return true;
        }
    }

    /**
     * List all tasks of a user
     * @return array array of tasks
     */
    public function listTasks()
    {
        $query = ("SELECT * FROM " . $this->_table . " WHERE userId = " . $_SESSION['userId']);
        $data = mysqli_query($this->getDB(), $query);
        $userData = array();
        while ($task = mysqli_fetch_object($data)) {
            array_push($userData, $task);
        }
        return $userData;
    }

    /**
     * It gets the list of all tasks for a user and returns only the ones that match with
     * the selected status by user.
     * @param array list of all tasks for a user
     * @param mixed $value status value to filter
     * @return array list of all tasks for a user filtered by status
     */
    public function statusFilter($userData, $value)
    {
        $filteredUserData = array();
        foreach ($userData as $task) {
            if (($task->status == $value)) {
                array_push($filteredUserData, $task);
            }
        }

        return $filteredUserData;
    }

    public function search($userData, $value)
    {

        if (!isset($_GET)) {
            $value = null;
        } else {
            $value = $_GET;
        }
        $showndata = $this->filterText($userData, $value['search']);
        return $showndata;
    }

    public function filterText($userData, $value)
    {
        $newUserData = array();
        foreach ($userData as $task) {
            if ((str_contains(strtolower($task->title), strtolower($value))) || (str_contains(strtolower($task->desc), strtolower($value)))) {
                array_push($newUserData, $task);
            }
        }
        return $newUserData;
    }
    public function showSearch($searchedData)
    {
        $shownData = array();
        foreach ($searchedData as $task) {
            if (($task->userId == $this->getUserId()) && (isset($task->taskId))) {
                array_push($shownData, $task);
            }
        }
        return $shownData;
    }

    public function updateTask($data, $taskId)
    {
        //modify taskId task on $data
        foreach ($data as $task) {
            if (($task->taskId == $taskId) && ($task->userId == $_SESSION['userId'])) {
                $task->title = $_POST['title'];
                $task->desc = $_POST['desc'];
                if (($task->status != 'DONE') && ($_POST['status'] == 'DONE'))
                    $task->endDate = $this->setDate();
                $task->status = $_POST['status'];
                if (($task->status != 'DONE') && isset($task->endDate))
                    $task->endDate = "";
                $task->modDate = $this->setDate();
            }
        }

        //save new data on db
        $this->setData($data);
    }

    public function deleteTask($data, $taskId)
    {
        foreach ($data as $key => $task) {
            if ($task->taskId == $taskId) {
                unset($data[$key]);
                array_values($data);
            }
        }
        $this->setData($data);
        return $this->getData();
    }

    public function getTask($taskId)
    {
        $data = $this->getData();
        foreach ($data as $task) {
            if ($task->taskId == $taskId)
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
        //Gets last item from the DB to get the "taskId" value
        $dbData = $this->getData();
        $lastItem = end($dbData);
        $lastItemId = $lastItem->{"taskId"};
        return $lastItemId;
    }

    public function getUserId()
    {
        return $_SESSION['userId'];
    }
}