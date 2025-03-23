<?php

class Facture
{
    private $db;

    // Constructor to inject the database connection
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the factures table if it doesn't exist
    public function createTable()
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS factures (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (client_id) REFERENCES clients(id)
            )
        ");
    }

    public function initializeData()
    {
        $this->createTable();

        $facturesData = [
            ['client_id' => 1],
            ['client_id' => 2],
            ['client_id' => 3],
            ['client_id' => 4],
            ['client_id' => 5]
        ];

        foreach ($facturesData as $facture) {
            $this->createFacture($facture['client_id']);
        }
    }

    // Insert a new facture into the database
    public function createFacture($client_id)
    {
        $sql = "INSERT INTO factures (client_id) VALUES (:client_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'client_id' => $client_id
        ]);
    }

    // Retrieve all factures for a specific client
    public function getAllFacturesByClient($client_id)
    {
        $query = $this->db->prepare("SELECT * FROM factures WHERE client_id = :client_id");
        $query->execute(['client_id' => $client_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete a facture by its ID
    public function deleteFacture($id)
    {
        $sql = "DELETE FROM factures WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
