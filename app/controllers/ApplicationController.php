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
        //veure que es crea l'objecte Tasks a partir del model creat, i que emmagatzema la informació del JSON
        $model = new Tasks;
        $this->view->_data = $model->listTasks();

    }
    function taskAction()
    {
        $this->view;
    }
}