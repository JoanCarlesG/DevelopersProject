<?php

/**
 * Tasks interace for handling tasks data
 */
interface TasksInterface
{
    public function getData();
    public function setData($data);
    public function addTask($newData);
    public function listTasks();
    //public function statusFilter($userData, $value){}
    public function search($userData,$value);
    public function updateTask($data, $tasakId);
    public function deleteTask($data,$taskId);
    
}
?>