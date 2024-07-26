<?php
require 'bdd.php';
$db = Database::connect();
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = htmlspecialchars($_POST['mail']);
    $mdp = htmlspecialchars($_POST['mdp']);

    // Vérifier si l'utilisateur existe en bdd avec le mail
    $stmt = $db -> prepare('SELECT * FROM users WHERE email = :email');
    $stmt -> execute(['email' => $email]);
    $user = $stmt -> fetch(PDO::FETCH_ASSOC);
  

    //  Vérifier si l'utilisateur existe et si le mot de passe est correct 
    if($user && password_verify($mdp, $user['mot_de_passe'])){
        $_SESSION['userId'] = $user['id'];
        $_SESSION['userRole']= $user['role'];
        $_SESSION['error'] = '';
        header('Location: index.php');
    }else{
        
        $error = 'Erreur les identifants ne sont pas corrects !';
        $_SESSION['error'] = $error;
        header('Location: inscription.php?error=' . $error);
    }

}

Database::disconnect();