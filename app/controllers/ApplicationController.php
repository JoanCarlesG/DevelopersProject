<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
include(ROOT_PATH . '/app/models/TasksModel.class.php');
include(ROOT_PATH . '/app/models/UsersModel.class.php');

class ApplicationController extends Controller
{
    public function homeAction()
    {
        $this->listTasksAction();
    }

    public function listTasksAction()
    {
        if(isset($_POST['filter']) && ($_POST['filter'] != 'All status')) {
            $userData = $this->filterAction();
            $this->view->__set('filter', $_POST['filter']);
        } else {
            $model = new Tasks;
            $userData = $model->listTasks($model->getUserId());
        }

        $this->view->__set('data', $userData);
    }

    function savedAction($data = array())
    {
        if (!empty($_POST)) {
            $model = new Tasks;
            $model->addTask($data);
            echo '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-gray-800 dark:text-red-400" role="alert"><span class="font-medium">Danger alert!</span> Saved Data.</div>';
            header("Location: home");

        } else {
            echo '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-gray-800 dark:text-red-400" role="alert"><span class="font-medium">Danger alert!</span> The fields are empty.</div>';
            header("Location: task");
            
        }
        $this->view;
    }

    function taskAction()
    {
        $this->view; 
    }

    public function loginAction()
    {
        $this->view->setLayout("loginLayout");
        if (!empty($_POST)) {
            $model = new Users;
            if ($model->validateLogin()) {
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
        $taskId = $_GET['taskId'];
        $model = new Tasks;
        
        //update task if there is new data
        if (empty($_POST)) {
            $this->view->__set('data',$model->getTask($taskId));
        } else {
            $model->updateTask($model->getData(),$taskId);
            header("Location: home");
        }
    }

    public function filterAction() {
        $model = new Tasks;
        return $model->filter($model->getUserId(), $_POST['filter']);
    }

    public function registerAction() {
        $this->view->setLayout("loginLayout");
        if (!empty($_POST)) {
            $model = new Users;
            if ($model->addUser($_POST)) {
                $_POST = array();
                header("Location: ./");
            }
        }
    }

}