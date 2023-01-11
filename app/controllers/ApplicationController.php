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
        $model = new Tasks;
        $this->view->_data = $model->listTasks($model->get_user_id());
    }

    function savedAction($data = array())
    {
        $this->view;
    }
    function taskAction()
    {
        $this->view;
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



}