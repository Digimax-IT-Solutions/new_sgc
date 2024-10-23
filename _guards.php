<?php

// _guards.php

require_once '_init.php';

class Guard
{
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

    public static function restrictToModule($module)
    {
        $currentUser = User::getAuthenticatedUser();
        $currentPage = basename($_SERVER['PHP_SELF']);

        if ($currentPage === 'login.php') {
            return;
        }

        if (!$currentUser) {
            redirect('login.php');
        }

        if (!$currentUser->hasModuleAccess($module)) {
            redirect('login.php');
        }
    }
}
