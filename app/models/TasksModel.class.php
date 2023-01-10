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

    public function getDB(){
        return $this->_dbh;
    }
    // parse json db into an array
    public function getData() {
        return (array) json_decode(file_get_contents($this->_dbh, true));
    }

    public function addTask($newData) {
        //DB decode to array, merge the 2 arrays, encode the new array, put contents in DB. Returns DB updated and decoded.
        $dbData = $this->getData();
        $mergedData = array_merge($dbData, $newData);
        $encodedMerge = json_encode($mergedData, JSON_PRETTY_PRINT);
        file_put_contents($this->_dbh, $encodedMerge);
        return (array) json_decode(file_get_contents($this->_dbh));
    }

    public function listTasks()
    {
        //return getData();
    }

    public function deleteTask()
    {
    }

    public function updateTask()
    {
    }

    public function setDate(){
        //Sets timestamp in this format => Hour:Min:Sec Day/Month/Year
        return (date('h:i:s d/m/Y', time()));
    }
    public function getLastTaskID(){
        //Gets last item from the DB to get the "task_id" value
        $dbData = $this->getData();
        $lastItem = end($dbData);
        $lastItemId = $lastItem->{"task_id"};
        return $lastItemId;
    }
}