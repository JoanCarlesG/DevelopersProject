<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
include(ROOT_PATH . '/app/models/TasksModel.class.php');

class ApplicationController extends Controller 
{

	function homeAction(){
        //veure que es crea l'objecte Tasks a partir del model creat, i que emmagatzema la informació del JSON
        $model = new Tasks;
        $table = $model->getData();
        echo "<pre>";
        var_dump($table);
        echo "</pre>";
        $this->view;
    }
    function taskAction(){
        $this->view;
    }
    function savedAction(){
        $this->view;
    }
}
