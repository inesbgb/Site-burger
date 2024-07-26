<?php
require '../bdd.php';
session_start();
// Vérifier si l'utilisateur est connecté et si c'est un admin
if (!isset ($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
$db = Database::connect();

if (isset ($_GET['id'])) {

    $itemId = $_GET['id'] ?? null;
    $query = 'SELECT * FROM items WHERE id = ?';

    $stmt = $db->prepare($query);
    $stmt->execute([$itemId]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($item) {
      
        // Supprimer l'entrée dans panier
        $stmtdelete = $db->prepare("DELETE FROM items WHERE id = ?");
        if ($stmtdelete->execute([$itemId])) {
            echo 'success';
            
    header('Location: index.php');
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}



Database::disconnect();
