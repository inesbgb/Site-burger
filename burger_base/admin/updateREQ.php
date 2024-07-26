<?php 
require '../bdd.php';
session_start();
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$db = Database::connect();


if (!empty(trim($_POST['name'])) && !empty(trim($_POST['description'])) && !empty(trim($_POST['price'])) && !empty(trim($_POST['qte'])) && !empty($_POST['category'])) {

    $nom = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $prix = htmlspecialchars($_POST['price']);
    $qte = htmlspecialchars($_POST['qte']);
    $categId = $_POST['category'];
// var_dump( $_FILES['img']); // afficher la valeur 
// die();// arreter le code ici

    // traitement de l'image
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        if ($_FILES['img']['size'] <= 1024 * 1024) {
            $arrExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extUplaod = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            if (in_array($extUplaod, $arrExtensions)) {

                //  renommer l'image
                $newName = time() . '.' . $extUplaod;
                // $newName = microtime(true).'.'. $extUplaod;

                move_uploaded_file($_FILES['img']['tmp_name'], '../images/' . $newName);
            } else {
                echo "l'extension n'est pas autorisée";
                exit();
            }
        } else {
            echo "la taille de l'image est trop grande";
            exit();
        }
    } else {
        echo "l'image n'est pas valide";
        exit();
    }

    // Insertion du produit dans la bdd 
    $id=1;
    $stmt = $db->prepare('UPDATE items SET name = ?, description= ?, price= ?, qte = ?, image = ?, category = ? 
     WHERE id = ?');

    $stmt->execute([$nom, $description, $prix, $qte, $newName, $categId,$id]);

 
    header('Location: update.php?id=' .$id);
  


}else{
    echo "les champs sont vides";
    exit();
}