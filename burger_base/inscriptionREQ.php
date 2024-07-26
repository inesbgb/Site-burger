<?php
require 'bdd.php';
$db = Database::connect();
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom = htmlspecialchars($_POST['nom']);
    $mail = htmlspecialchars($_POST['mail']);
    $mdp = htmlspecialchars($_POST['mdp']);


    // Vérifier si un utilisateur existe déjà en bdd avec le même mail
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :mail');
    $stmt->execute(['mail' => $mail]);
    if($stmt->fetch()){
        $error = 'Cet email est déjà utilisé';
        $_SESSION['error'] = $error;
        header('Location: inscription.php?error=' . $error);
        exit;   
    }

    $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

    $stmt = $db->prepare('INSERT INTO users (nom, email, mot_de_passe) VALUES (:nom, :mail, :mdp)');
    $success = $stmt->execute(['nom' => $nom, 'mail' => $mail, 'mdp' => $mdpHash]);

    if($success){
        $error = ' Vous êtes inscrit !';
        $_SESSION['error'] = $error;
       
        header('Location: inscription.php');
    }else{


        $error = 'Erreur lors de l\'inscription';
        $_SESSION['error'] = $error;
        header('Location: inscription.php?error=' . $error);}
    }

Database::disconnect();