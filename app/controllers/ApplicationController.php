<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
include(ROOT_PATH . '/app/models/TasksModel.class.php');

class ApplicationController extends Controller
{
    public function homeAction()
    {
        $this->listTasksAction();
    }

    public function listTasksAction()
    {

        
        if(isset($_POST['filter']) && ($_POST['filter'] != 'All status')) {
            $user_data = $this->filterAction();
            $this->view->__set('filter', $_POST['filter']);
        } else {
            $model = new Tasks;
            $user_data = $model->listTasks($model->get_user_id());
        }

        $this->view->__set('data', $user_data);
    }

    function savedAction($data = array())
    {
        $this->view;
    }
    function taskAction()
    {
        $this->view;
        // add --> header("Location: home"); 
    }

    public function loginAction()
    {
        if (!empty($_POST)) {
            $model = new Tasks;
            if ($model->validate_login()) {
                header("Location: home");
            } else {
                echo '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-gray-800 dark:text-red-400" role="alert"><span class="font-medium">Danger alert!</span> Change a few things up and try submitting again.</div>';
            }
        }
    }
    public function deleteAction()
    {   
        if (!empty($_POST)) {
            $model = new Tasks;
            $data = $model->getData();
            $model->deleteTask($data, $_POST);
            header("Location: home"); 
        };
        
    }

    public function updateTaskAction()
    {
        $task_id = $_GET['task_id'];
        $model = new Tasks;
        
        //update task if there is new data
        if (empty($_POST)) {
            $this->view->_data = $model->getTask($task_id);
        } else {
            $model->updateTask($model->getData(),$task_id);
            header("Location: home");
        }
    }

    public function filterAction() {
        $model = new Tasks;
        return $model->filter($model->get_user_id(), $_POST['filter']);
    }

}