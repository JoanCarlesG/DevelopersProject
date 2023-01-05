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

    // parse json db into an array
    public function getData() {
        return (array) json_decode(file_get_contents($this->_dbh));
    }

    public function saveData($data) {
        //to do
    }

    public function listTasks()
    {
        return getData();
    }

    public function addTask()
    {
    }

    public function deleteTask()
    {
    }

    public function updateTask()
    {
    }


}