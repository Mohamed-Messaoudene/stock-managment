<?php

class Client
{
    private $db;
    private $table = 'clients';

    public $id;
    public $name;
    public $email;
    public $phone;
    public $created_at;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the clients table
    public function createTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                phone VARCHAR(15),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";

        $this->db->exec($sql);
    }

    public function initializeData()
    {
        $this->createTable();

        $initialData = [
            [
                'name' => 'Ali Amrani',
                'email' => 'ali.amrani@example.com',
                'phone' => '0550000001'
            ],
            [
                'name' => 'Karima Lahlou',
                'email' => 'karima.lahlou@example.com',
                'phone' => '0550000002'
            ],
            [
                'name' => 'Mohammed Bouzid',
                'email' => 'mohammed.bouzid@example.com',
                'phone' => '0550000003'
            ],
            [
                'name' => 'Fatima Zohra Boudiaf',
                'email' => 'fatima.boudiaf@example.com',
                'phone' => '0550000004'
            ],
            [
                'name' => 'Ahmed taleb',
                'email' => 'ahmed.taleb@example.com',
                'phone' => '0550000005'
            ],
            [
                'name' => 'Sofia Khelil',
                'email' => 'sofia.khelil@example.com',
                'phone' => '0550000006'
            ],
            [
                'name' => 'Samir Benali',
                'email' => 'samir.benali@example.com',
                'phone' => '0550000007'
            ],
            [
                'name' => 'Yasmine Messaoud',
                'email' => 'yasmine.messaoud@example.com',
                'phone' => '0550000008'
            ],
            [
                'name' => 'Rachid Djemai',
                'email' => 'rachid.djemai@example.com',
                'phone' => '0550000009'
            ],
            [
                'name' => 'Nadia Merbah',
                'email' => 'nadia.merbah@example.com',
                'phone' => '0550000010'
            ]
        ];

        foreach ($initialData as $client) {
            if (!$this->clientExists($client['name'])) {
                $this->insertClient($client['name'], $client['email'], $client['phone']);
            }
        }
    }

    // Check if the client already exists by email
    public function clientExists($email)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = :email");
        $query->execute(['email' => $email]);
        return $query->fetchColumn() > 0;
    }

    // Insert a new client into the database
    public function insertClient($name, $email, $phone)
    {
        $sql = "INSERT INTO {$this->table} (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);
    }

    // Update an existing client's information
    public function updateClient($id, $name, $email, $phone)
    {
        $sql = "
            UPDATE {$this->table}
            SET name = :name, email = :email, phone = :phone
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);
    }

    // Delete a client by ID
    public function deleteClientById($client_id)
    {
        // Begin a transaction to ensure atomicity
        $this->db->beginTransaction();

        try {
            // Delete related facture items via cascading from factures
            $stmt = $this->db->prepare("DELETE FROM factures WHERE client_id = :client_id");
            $stmt->execute(['client_id' => $client_id]);

            // Delete the client after deleting related factures
            $stmt = $this->db->prepare("DELETE FROM clients WHERE id = :client_id");
            $stmt->execute(['client_id' => $client_id]);

            // Commit the transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Roll back the transaction if an error occurs
            $this->db->rollBack();
            throw $e;
        }
    }

    // Get all clients
    public function getAllClients()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return all clients as an associative array
    }
}
