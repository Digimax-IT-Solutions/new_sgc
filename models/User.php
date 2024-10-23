<?php

// models/User.php

require_once __DIR__ . '/../_init.php';

class User
{
    public $id;
    public $name;
    public $username;
    public $role_id;
    public $role_name;
    public $password;

    private static $cache = null;
    private static $currentUser = null;

    public function __construct($user)
    {
        $this->id = intval($user['id']);
        $this->name = $user['name'];
        $this->username = $user['username'];
        $this->role_id = intval($user['role_id']);
        $this->password = $user['password'];
        $this->role_name = $user['role_name'] ?? $this->getRoleName(); // Use getRoleName() as fallback
    }

    public function getHomePage()
    {
        // All roles are directed to the dashboard
        return 'dashboard';
    }

    public static function getAllRoles()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT id, role_name FROM user_roles');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('SELECT u.*, r.role_name FROM `users` u JOIN `user_roles` r ON u.role_id = r.id');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($user) {
            return new User($user);
        }, $result);

        return static::$cache;
    }


    public static function add($name, $username, $role_id, $password)
    {
        global $connection;

        if (static::findByUsername($username)) {
            throw new Exception('User already exists');
        }

        $stmt = $connection->prepare('INSERT INTO `users`(name, username, role_id, password) VALUES (:name, :username, :role_id, :password)');
        $stmt->bindParam("name", $name);
        $stmt->bindParam("username", $username);
        $stmt->bindParam("role_id", $role_id);
        $stmt->bindParam("password", $password);
        $stmt->execute();

        return $connection->lastInsertId();
    }

    public static function addModuleAccess($role_id, $module)
    {
        global $connection;

        $stmt = $connection->prepare('INSERT INTO `user_role_module_access`(role_id, module) VALUES (:role_id, :module)');
        $stmt->bindParam("role_id", $role_id);
        $stmt->bindParam("module", $module);
        $stmt->execute();
    }

    public static function getAuthenticatedUser()
    {
        if (!isset($_SESSION['user_id']))
            return null;

        if (!static::$currentUser) {
            static::$currentUser = static::find($_SESSION['user_id']);
        }

        return static::$currentUser;
    }

    public static function find($user_id)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT u.*, r.role_name FROM `users` u JOIN `user_roles` r ON u.role_id = r.id WHERE u.id=:id");
        $stmt->bindParam("id", $user_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new User($result[0]);
        }

        return null;
    }

    public static function findByUsername($username)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT u.*, r.role_name FROM `users` u JOIN `user_roles` r ON u.role_id = r.id WHERE u.username=:username");
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new User($result[0]);
        }

        return null;
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `users` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }


    public static function login($username, $password)
    {
        if (empty($username))
            throw new Exception("The username is required");
        if (empty($password))
            throw new Exception("The password is required");

        $user = static::findByUsername($username);

        if ($user && $user->password == $password) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role_id'] = $user->role_id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['role_name'] = $user->role_name;

            return $user;
        }

        throw new Exception('Wrong username or password.');
    }

    public static function getRoleModuleAccess($role_id)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT module FROM user_role_module_access WHERE role_id = :role_id');
        $stmt->bindParam('role_id', $role_id);
        $stmt->execute();
        $modules = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $modules;
    }

    public function hasModuleAccess($module)
    {
        $roleName = $this->getRoleName();
        if ($roleName === 'SUPERADMIN') {
            return true;  // SUPERADMIN has access to everything
        }
        $modules = self::getRoleModuleAccess($this->role_id);
        return in_array($module, $modules);
    }

    public function getRoleName()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT role_name FROM user_roles WHERE id = :role_id');
        $stmt->bindParam('role_id', $this->role_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}