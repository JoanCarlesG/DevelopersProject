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

    /**
     * Lists tasks from user's session
     */
    public function listTasksAction()
    {
        //List all user session tasks
        $model = new Tasks;
        $userData = array();
        $userData = $model->listTasks();

        //Apply filter and search if set
        if (isset($_GET['filter'])) {
            $userData = $this->filterAction($userData);
        }

        if (isset($_GET['search'])) {
            $userData = $this->searchAction($userData);
            //Clean $_POST
             $_GET = array();
        }

        //Save data in view
        $this->view->__set('data', $userData);
    }

    public function filterAction($userData){
        if (isset($_GET['filter']) && ($_GET['filter'] != 'All status')) {
            $model = new Tasks;
            $userData = $model->statusFilter($userData, $_GET['filter']);
            $this->view->__set('filter', $_GET['filter']);
        }

        return $userData;
    }

    public function searchAction($userData)
    {
        if (isset($_GET['search']) && $_GET['search'] != "") {
            $search = $_GET['search'];
            $model = new Tasks;
            $userData = $model->search($userData, $search);
            $this->view->__set('search', $_GET['search']);
        }
        
        return $userData;
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

    public function updateTaskAction()
    {
        $taskId = $_GET['taskId'];
        $model = new Tasks;

        //update task if there is new data
        if (empty($_POST)) {
            $this->view->__set('data', $model->getTask($taskId));
        } else {
            $model->updateTask($model->getData(), $taskId);
            header("Location: home");
        }
    }

    public function deleteAction()
    {
        if (!empty($_GET)) {
            $model = new Tasks;
            $data = $model->getData();
            $model->deleteTask($data, $_GET['taskId']);
            header("Location: home");
        }
        ;
    }

    public function loginAction()
    {
        $this->view->setLayout("loginLayout");
        if (!empty($_POST)) {
            $model = new Users;
            if ($model->validateLogin()) {
                header("Location: home");
            } else {
                $this->view->__set('error',true);
            }
        }
    }

    public function registerAction()
    {
        $this->view->setLayout("loginLayout");
        if (!empty($_POST)) {
            $model = new Users;
            if ($model->addUser()) {
                $_POST = array();
                header("Location: ./");
            }
        }
    }
}