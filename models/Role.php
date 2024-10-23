<?php

require_once __DIR__ . '/../_init.php';


class Role
{
    public $id;
    public $role_name;

    private static $cache = null;

    public function __construct($role)
    {

        $this->id = $role['id'];
        $this->role_name = $role['role_name'];
    }

    public static function add($role_name)
    {
        global $connection;

        if (static::findRoleByRole($role_name)) {
            throw new Exception('Role already Exist');
        }

        $stmt = $connection->prepare('INSERT INTO `user_roles`(role_name) VALUES (:role_name)');
        $stmt->bindParam("role_name", $role_name);
        $stmt->execute();

        // Return the last inserted user ID
        return $connection->lastInsertId();
    }

    public static function findRoleByRole($role_name)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `user_roles` WHERE role_name=:role_name");
        $stmt->bindParam("role_name", $role_name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new User($result[0]);
        }

        return null;
    }

    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('SELECT * FROM `user_roles`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($role) {
            return new Role($role);
        }, $result);

        return static::$cache;
    }

    public function delete()
    {
        global $connection;

        try {
            // Begin a transaction
            $connection->beginTransaction();

            // Delete the associated records in the user_role_module_access table
            $stmt = $connection->prepare('DELETE FROM `user_role_module_access` WHERE role_id = :role_id');
            $stmt->bindParam('role_id', $this->id);
            $stmt->execute();

            // Delete the role itself from the user_roles table
            $stmt = $connection->prepare('DELETE FROM `user_roles` WHERE id = :id');
            $stmt->bindParam('id', $this->id);
            $stmt->execute();

            // Commit the transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            $connection->rollBack();
            throw $e;
        }
    }


    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `user_roles` WHERE id=:id");
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Role($result[0]);
        }

        return null;
    }


    public static function addModuleAccess($roleId, $module)
    {
        global $connection;

        $stmt = $connection->prepare('INSERT INTO `user_role_module_access`(role_id, module) VALUES (:role_id, :module)');
        $stmt->bindParam("role_id", $roleId);
        $stmt->bindParam("module", $module);
        $stmt->execute();
    }


}