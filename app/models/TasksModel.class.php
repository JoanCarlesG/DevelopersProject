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
        $dbData = $this->getData();
        $mergedData = array_merge($dbData, $newData);
        $encodedMerge = json_encode($mergedData, JSON_PRETTY_PRINT);
        file_put_contents($this->_dbh, $encodedMerge);
        return (array) json_decode(file_get_contents($this->_dbh));
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
            if ($task->taskId == $taskId["taskId"]) {
                unset($data[$key]);
                array_values($data);
            }
        }

        file_put_contents($this->_dbh, json_encode($data, JSON_PRETTY_PRINT));
        return (array) json_decode(file_get_contents($this->_dbh));
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
}