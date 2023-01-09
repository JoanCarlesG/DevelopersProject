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
        dd($table);
        $this->view;
    }
    function taskAction(){
        $this->view;
    }
    function savedAction($data = array()){
        $model = new Tasks;
        $table = $model->saveData($data);
        var_dump($table);
        $this->view;
    }
}
