<?php

/**
 * Tasks interace for handling tasks data
 */
interface TasksInterface
{
    public function addTask($newData);
    public function listTasks();
    //public function statusFilter($userData, $value){}
    public function search($userData,$value);
    public function updateTask($tasakId);
    public function deleteTask($taskId);
}