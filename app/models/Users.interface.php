<?php

/**
 * Users interface for handling users data
 */
interface UsersInterface
{
    public function getData();

    public function setData($data);

    public function validateLogin();

    public function addUser();
}
