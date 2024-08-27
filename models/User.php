<?php

// models/User.php

require_once __DIR__ . '/../_init.php';

class User
{
    public $id;
    public $name;
    public $username;
    public $role;
    public $password;

    private static $cache = null;

    public function getHomePage()
    {
        $rolePages = [
            ROLE_ADMIN => 'dashboard',
            ROLE_PURCHASING => 'purchasing_home',
            ROLE_WAREHOUSE => 'warehouse'
        ];

        return $rolePages[$this->role] ?? 'index.php';
    }

    private static $currentUser = null;

    public function __construct($user)
    {
        $this->id = intval($user['id']);
        $this->name = $user['name'];
        $this->username = $user['username'];
        $this->role = $user['role'];
        $this->password = $user['password'];
    }

    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('SELECT * FROM `users`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($user) {
            return new User($user);
        }, $result);

        return static::$cache;
    }

    public static function add($name, $username, $role, $password)
    {
        global $connection;

        if (static::findByUsername($username))
            throw new Exception('User already exists');

        $stmt = $connection->prepare('INSERT INTO `users`(name, username, role, password) VALUES (:name, :username, :role, :password)');
        $stmt->bindParam("name", $name);
        $stmt->bindParam("username", $username);
        $stmt->bindParam("role", $role);
        $stmt->bindParam("password", $password);
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

        $stmt = $connection->prepare("SELECT * FROM `users` WHERE id=:id");
        $stmt->bindParam("id", $user_id);
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

    public static function findByUsername($username)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `users` WHERE username=:username");
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new User($result[0]);
        }

        return null;
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
            $_SESSION['role'] = $user->role; // Store the user's role in the session
            $_SESSION['user_name'] = $user->name; // Store the user's name in the session

            return $user;
        }

        throw new Exception('Wrong username or password.');
    }
}