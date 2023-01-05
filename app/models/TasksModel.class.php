<?php

/**
 * Base model for the application.
 * Add general things in this model.
 */
class Tasks extends Model
{
    public function __construct()
    {
		// parses the settings file
		$settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
		
		// parse json db into an array
        $db = $settings['database']['dbname'];
        $this->_dbh = ROOT_PATH . '/web/' . $db . '.json';
        $this->_table = (array) json_decode(file_get_contents($this->_dbh));
    }

    //function to get _table data
    public function _getTable() {
        return $this->_table;
    }

    public function listTasks()
    {
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