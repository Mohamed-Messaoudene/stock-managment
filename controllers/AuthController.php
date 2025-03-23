<?php

require_once(__DIR__ . '/../models/Users.php');
class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function handleRequest() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                if ($action === 'add') {
                    $response = $this->register();
                    $users = $this->userModel->getAllUsers();
                    $this->showUsersPage($users, $response['success_message'], $response['error_message']);
                } 
                elseif ($action === 'update') {
                    $response = $this->updateUser();
                    $users = $this->userModel->getAllUsers();
                    $this->showUsersPage($users, $response['success_message'], $response['error_message']);
                }
                elseif ($action === 'delete') {
                    $response = $this->deleteUser();
                    $users = $this->userModel->getAllUsers();
                    $this->showUsersPage($users, $response['success_message'], $response['error_message']);
                }
                elseif ($action === 'login') {
                    $response = $this->login();
                    if (isset($response['redirect_url'])) {
                        header("Location: " . $response['redirect_url']);
                        exit;
                    } else {
                        $this->renderResponse($response, './views/login.php');
                    }
                } else {
                    echo "Invalid action.";
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $action = $_GET['action'] ?? null;
           if ($action === 'usersPage') {
                $users = $this->userModel->getAllUsers();
                $this->showUsersPage($users);
                return ;
            } elseif ($action === 'logout') {
                $this->logout();
            } 
            $this->showLoginPage(); // Default page
        }
    }

    private function renderResponse($response, $view) {
        if (!empty($response['error_message'])) {
            $error_message = $response['error_message'];
        }
        if (!empty($response['success_message'])) {
            $success_message = $response['success_message'];
        }
        include $view;
    }

    public function register() {
        $response = ['error_message' => '', 'success_message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $role = $_POST['role'];
    
            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $response['error_message'] = "All fields are required.";

                return $response;
            }

            if ($this->userModel->userExists($email)) {
                $response['error_message'] = "Email is already taken.";
                return $response;
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $this->userModel->insertUser($name, $email, $hashedPassword, $role);
                $response['success_message'] = "User registered successfully.";
            } catch (PDOException $e) {
                $response['error_message'] = "Database error: " . $e->getMessage();
            }
            
        }
               return $response;
    }

    public function login() {
        $response = ['error_message' => '', 'success_message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                $response['error_message'] = "Email and Password are required.";
                return $response;
            }
            echo'-----------------------';
            echo $password ,$email;
            $user = $this->userModel->getUserByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $response['success_message'] = "Login successful! Redirecting...";
                $response['redirect_url'] = '/stock_managment/views/navigationPage.php';
            } else {
                $response['error_message'] = "Invalid credentials.";
            }
        }

        return $response;
    }

    public function updateUser() {
        $response = ['error_message' => '', 'success_message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $id = $_POST['id'];
            $name = trim($_POST['username']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];

            if (empty($id) || empty($name) || empty($email) || empty($role)) {
                $response['error_message'] = "All fields are required.";
                return $response;
            }

            try {
                $this->userModel->updateUser($id, $name, $email, $role);
                $response['success_message'] = "User updated successfully.";
            } catch (Exception $e) {
                $response['error_message'] = "Error updating user: " . $e->getMessage();
            }
        }

        return $response;
    }

    public function deleteUser() {
        $response = ['error_message' => '', 'success_message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $email = trim($_POST['email']);

            if (empty($email)) {
                $response['error_message'] = "Email is required to delete a user.";
                return $response;
            }

            try {
                $this->userModel->deleteUser($email);
                $response['success_message'] = "User deleted successfully.";
            } catch (Exception $e) {
                $response['error_message'] = "Error deleting user: " . $e->getMessage();
            }
        }

        return $response;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getLoggedInUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    public function showLoginPage($data = []) {
        include __DIR__ . '/../views/login.php';
    }

    public function showUsersPage($users = [], $success = "", $error = "") {
    include __DIR__ . '/../views/usersPage.php';

    }
}
?>
