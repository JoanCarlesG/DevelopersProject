<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
include(ROOT_PATH . '/app/models/TasksModel.class.php');

class ApplicationController extends Controller
{
    function homeAction()
    {
        $this->listTasksAction();
    }

    function listTasksAction() {
        $model = new Tasks;
        $this->view->_data = $model->listTasks();
    }

    function savedAction($data = array()){
        $model = new Tasks;
        $table = $model->saveData($data);
        var_dump($table);
        $this->view;
    }
}