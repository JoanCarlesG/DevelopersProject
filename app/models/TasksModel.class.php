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

    public function addTask($newData)
    {
        //DB decode to array, merge the 2 arrays, encode the new array, put contents in DB. Returns DB updated and decoded.
        if (isset($_POST["title"]) && isset($_POST["desc"]) && isset($_POST["status"])) {

            $id = $this->getLastTaskID();
            //++task_id to set new "task_id" value
            $newData = array(
                array_key_last($this->getData()) => array(
                    "userId" => $this->getUserId(),
                    "taskId" => ++$id,
                    "title" => $_POST["title"],
                    "desc" => $_POST["desc"],
                    "status" => $_POST["status"],
                    "startDate" => $this->setDate(),
                    "modDate" => "",
                    "endDate" => ""
                )
            );
            $dbData = $this->getData();
            $mergedData = array_merge($dbData, $newData);
            $this->setData($mergedData);
            return $this->getData();
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

    public function updateTask($taskId)
    {
        //modify taskId task on $data
        $task = $this->getTask($taskId);
        if (isset($task)) {
            $title = $_POST['title'];
            $desc = $_POST['description'];
            $endDate = "NULL";
            if (($task->status != 'DONE') && ($_POST['status'] == 'DONE')) {
                $endDate = $this->setDate();
            }
            $status = $_POST['status'];
            if (($task->status != 'DONE') && isset($task->endDate)) {
                $endDate = "NULL";
            }
            $modDate = $this->setDate();
        }

        $query = "UPDATE $this->_table SET title = '$title', description = '$desc', status = '$status', modDate = STR_TO_DATE('$modDate','%h:%i:%s %p %d/%m/%Y'), endDate = STR_TO_DATE('$endDate','%h:%i:%s %p %d/%m/%Y') WHERE taskId = $taskId";
        mysqli_query($this->getDB(), $query);
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
        $query = "SELECT * FROM " . $this->_table . " WHERE taskId = " . $taskId;
        $task = mysqli_query($this->getDB(), $query);
        if (mysqli_num_rows($task) == 1) {
            return mysqli_fetch_object($task);
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