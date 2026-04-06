<?php
session_start();

// Set JSON header
header('Content-Type: application/json');

// Simple test - just check if we can receive POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    echo json_encode([
        'received' => true,
        'input' => $input,
        'session' => $_SESSION,
        'action' => $_GET['action'] ?? null,
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
} else {
    echo json_encode([
        'error' => 'Method harus POST'
    ]);
}
?>
