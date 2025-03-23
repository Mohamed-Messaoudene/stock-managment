<?php

class FactureItem
{
    private $db;

    // Constructor to inject the database connection
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the facture_items table if it doesn't exist
    public function createTable()
    {
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS facture_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            facture_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (facture_id) REFERENCES factures(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ");
    }
    public function initializeData()
    {
        $this->createTable();

        $factureItemsData = [
            ['facture_id' => 1, 'product_id' => 1, 'quantity' => 2, 'total_price' => 199.98],
            ['facture_id' => 1, 'product_id' => 2, 'quantity' => 1, 'total_price' => 799.99],
            ['facture_id' => 2, 'product_id' => 3, 'quantity' => 1, 'total_price' => 499.99],
            ['facture_id' => 2, 'product_id' => 4, 'quantity' => 3, 'total_price' => 179.97],
            ['facture_id' => 3, 'product_id' => 5, 'quantity' => 1, 'total_price' => 1299.99],
            ['facture_id' => 3, 'product_id' => 6, 'quantity' => 2, 'total_price' => 399.98],
            ['facture_id' => 4, 'product_id' => 7, 'quantity' => 1, 'total_price' => 149.99],
            ['facture_id' => 4, 'product_id' => 8, 'quantity' => 4, 'total_price' => 119.96],
            ['facture_id' => 5, 'product_id' => 9, 'quantity' => 1, 'total_price' => 999.99],
            ['facture_id' => 5, 'product_id' => 10, 'quantity' => 1, 'total_price' => 499.99]
        ];

        foreach ($factureItemsData as $factureItem) {
            $this->createFactureItem(
                $factureItem['facture_id'],
                $factureItem['product_id'],
                $factureItem['quantity'],
                $factureItem['total_price']
            );
        }
    }

    // Insert a new facture item into the database
    public function createFactureItem($facture_id, $product_id, $quantity, $total_price)
    {
        $sql = "INSERT INTO facture_items (facture_id, product_id, quantity, total_price)
                VALUES (:facture_id, :product_id, :quantity, :total_price)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'facture_id' => $facture_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'total_price' => $total_price
        ]);
    }

    // Retrieve all items for a specific facture
    public function getAllItemsByFacture($facture_id)
    {
        $query = $this->db->prepare("SELECT * FROM facture_items WHERE facture_id = :facture_id");
        $query->execute(['facture_id' => $facture_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete a facture item by its ID
    public function deleteFactureItem($id)
    {
        $sql = "DELETE FROM facture_items WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
