<?php

// _guards.php

require_once '_init.php';

class Guard
{

    public static function adminOnly()
    {
        $currentUser = User::getAuthenticatedUser();

        if (!$currentUser || $currentUser->role !== ROLE_ADMIN) {
            redirect('login.php');
        }
    }

    public static function accountingOnly()
    {
        $currentUser = User::getAuthenticatedUser();

        if (!$currentUser || $currentUser->role !== ROLE_ACCOUNTING) {
            redirect('login.php');
        }
    }

    public static function purchasingOnly()
    {
        $currentUser = User::getAuthenticatedUser();

        if (!$currentUser || $currentUser->role !== ROLE_PURCHASING) {
            redirect('login.php');
        }
    }


    public static function warehouseOnly()
    {
        $currentUser = User::getAuthenticatedUser();

        if (!$currentUser || $currentUser->role !== ROLE_WAREHOUSE) {
            redirect('login.php');
        }
    }

    public static function cashierOnly()
    {
        $currentUser = User::getAuthenticatedUser();

        if (!$currentUser || $currentUser->role !== ROLE_CASHIER) {
            redirect('login.php');
        }
    }

    public static function hasModel($modelClass)
    {
        $model = $modelClass::find(get('id'));

        if ($model == null) {
            header('Content-type: text/plain');
            die('Page not found');
        }

        return $model;
    }

    public static function guestOnly()
    {
        $currentUser = User::getAuthenticatedUser();

        if (!$currentUser)
            return;

        redirect($currentUser->getHomePage());
    }
}