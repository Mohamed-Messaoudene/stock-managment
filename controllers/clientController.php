<?php
require_once(__DIR__ . '/../models/Clients.php');
require_once(__DIR__ . '/../config/db.php');


class ClientsController {
    private $clientModel;

    public function __construct($db) {
        $this->clientModel = new Client($db);
    }

    public function handleRequest() {
        $error = $success = '';
        $clients = [];


        // Handle POST requests for add, update, delete actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $phone = $_POST['phone'] ?? '';

                if ($name && $email) {
                    if ($this->clientModel->clientExists($email)) {
                        $error = "A client with this email already exists.";
                    } else {
                        $this->clientModel->insertClient($name, $email, $phone);
                        $success = "Client added successfully!";
                    }
                } else {
                    $error = "Name and email are required.";
                }
            } elseif ($action === 'delete') {
                $clientId = $_POST['id'] ?? '';
                if ($clientId) {
                    $this->clientModel->deleteClientById($clientId);
                    $success = "Client deleted successfully!";
                } else {
                    $error = "Invalid client ID.";
                }
            } elseif ($action === 'update') {
                $id = $_POST['id'] ?? '';
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $phone = $_POST['phone'] ?? '';

                if ($id && $name && $email) {
                    $this->clientModel->updateClient($id, $name, $email, $phone);
                    $success = "Client updated successfully!";
                } else {
                    $error = "ID, Name, and Email are required.";
                }
            }
        }

        // Fetch updated client list after any modification
        $clients = $this->getAllClients();
        // Render the view with the clients, error, and success messages
        $this->renderView($error, $success, $clients);
    }

    // Fetch all clients
    private function getAllClients() {
        return $this->clientModel->getAllClients();
    }

    // Render the view
    private function renderView($error, $success, $clients) {
        require_once __DIR__.'/../views/clientsPage.php';
    }
}
