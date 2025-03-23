<?php

class User
{
    private $db;

    // Constructor to inject the database connection
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the users table if it doesn't exist
    public function createTable()
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS users ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                username VARCHAR(50) NOT NULL UNIQUE, 
                email VARCHAR(100) NOT NULL UNIQUE, 
                password VARCHAR(255) NOT NULL, 
                role ENUM('admin', 'user') NOT NULL, 
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
            )
        ");
    }

    // Initialize the database with default users
    public function initializeData()
    {
        $this->createTable();

        $initialData = [
            [
                'username' => 'Ahmed Benali',
                'email' => 'ahmed.benali@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT), // Hashing password
                'role' => 'user'
            ],
            [
                'username' => 'Mohamed Ziani',
                'email' => 'mohamed.ziani@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user'
            ],
            [
                'username' => 'Karim Lamine',
                'email' => 'karim.lamine@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user'
            ],
            [
                'username' => 'Berkane Rachid',
                'email' => 'berkane.rachid@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'username' => 'Samir Djeridi',
                'email' => 'samir.djeridi@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user'
            ]
        ];

        foreach ($initialData as $user) {
            if (!$this->userExists($user['email'])) { // Check existence by email
                $this->insertUser($user['username'], $user['email'], $user['password'], $user['role']);
            }
        }
    }

    // Check if a user with the given email already exists
    public function userExists($email)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        return $query->fetchColumn() > 0;
    }

    // Insert a new user into the database
    public function insertUser($username, $email, $password, $role)
    {
        $sql = "INSERT INTO users (username, email, password, role)
                VALUES (:username, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password, // Already hashed
            'role' => $role
        ]);
    }

    // Retrieve all users from the database
    public function getAllUsers()
    {
        $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a user by email
    public function getUserByEmail($email)
    {
        $query = $this->db->prepare("SELECT id, username, email, password, role FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    

    // Delete a user by email
    public function deleteUser($email)
    {
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
    }

    // Update user details
    public function updateUser($id, $username, $email, $role, $password = null)
    {
        $sql = "UPDATE users SET username = :username, email = :email, role = :role";

        $params = [
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'id' => $id
        ];

        // Update password only if provided
        if ($password) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}

?>
