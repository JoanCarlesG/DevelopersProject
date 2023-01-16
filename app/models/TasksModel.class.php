<?php

/**
 * Tasks class for handling tasks data
 */
class Tasks extends Model
{
    public function __construct()
    {
        // parse the settings file
        $this->_dbh = ROOT_PATH . '/web/' . 'db_tasks.json';
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

    // list all tasks from a user
    public function listTasks($userId)
    {
        $data = $this->getData();
        $userData = array();
        foreach ($data as $task) {
            if (($task->userId == $userId) && (isset($task->taskId))) {
                array_push($userData, $task);
            }
        }
        return $userData;
    }

    public function filter($userId, $value)
    {
        $data = $this->listTasks($this->getUserId());
        $userData = array();
        foreach ($data as $task) {
            if (($task->status == $value)) {
                array_push($userData, $task);
            }
        }
        return $userData;
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

    public function search($value){
        
        if (!isset($_POST)) {
            $value = null;
        } else {
            $value = $_POST;
        }
        $showndata = $this->filterText($value['search']);
        return $showndata;
    }

    public function filterText($value)
    {
        $data = $this->listTasks($this->getUserId());
        $userData = array();
        foreach ($data as $task) {
            if ((str_contains(strtolower($task->title), strtolower($value))) || (str_contains(strtolower($task->desc), strtolower($value)))) {
                array_push($userData, $task);
            }
        }
        return $userData;
    }
    public function showSearch($searchedData){
        $shownData = array();
        foreach ($searchedData as $task) {
            if (($task->userId == $this->getUserId()) && (isset($task->taskId))) {
                array_push($shownData, $task);
            }
        }
        return $shownData;
    }
}
