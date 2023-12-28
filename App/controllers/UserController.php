<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
    protected $db;
    public function __construct()
    {
        $config = require basePath("config/db.php");
        $this->db = new Database($config);
    }

    public function login()
    {
        loadView('users/login');
    }

    public function logout()
    {
        Session::clearAll();
        $params = session_get_cookie_params();
        setcookie("PHPSESSID", '', time() - 86400, $params['path'], $params['domain']);
        redirect('/');
    }

    // ========================================

    public function authenticate()
    {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $errors = [];
        if (!Validation::email($email)) {
            $errors['email'] = "Please enter a valid email address";
        }
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = "Password must be at least 6 characters";
        }
        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        // CHECK IF EMAIL ALREADY EXISTS
        $params = [
            'email' => $email
        ];

        $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();

        if (!$user || !password_verify($password, $user->password)) {
            $errors['error'] = "Incorrect credentials, Try Again Spanky!";
            loadView("users/login", [
                'errors' => $errors
            ]);
            exit;
        }

        Session::set("user", [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'state' => $user->state
        ]);
        redirect("/");
    }

    // ===================================



    public function create()
    {
        loadView('users/create');
    }




    public function store()
    {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $password = $_POST["password"];
        $password_confirm = $_POST["password_confirmation"];
        $errors = [];
        //VALIDATION =============================================
        if (!Validation::email($email)) {
            $errors['email'] = "Please enter a valid email address";
        }

        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = "Name must be between 2 and 50 characters";
        }
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = "Password must be at least 6 characters";
        }

        if (!Validation::match($password, $password_confirm)) {
            $errors['password_confirmation'] = "Passwords must match";
        }
        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => [
                    "name" => $name,
                    "email" => $email,
                    "city" => $city,
                    "state" => $state
                ]
            ]);
            exit;
        }
        // CHECK IF EMAIL ALREADY EXISTS
        $params = [
            'email' => $email
        ];
        $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();
        if ($user) {
            $errors['email'] = "Email Already Exists";
            loadView("users/create", [
                'errors' => $errors
            ]);
            exit;
        }
        $params = [
            "name" => $name,
            "email" => $email,
            "city" => $city,
            "state" => $state,
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->db->query("INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)", $params);
        // GET NEW USER ID 
        $userID = $this->db->conn->lastInsertId();
        Session::set("user", [
            'id' => $userID,
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state
        ]);
        redirect("/");
    }
}
