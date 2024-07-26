<?php
require 'bdd.php';
$db = Database::connect();
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $totalPanier = filter_var($_POST["totalPanier"], FILTER_VALIDATE_FLOAT);
    $code = htmlspecialchars($_POST['code']);
    $dateJour = date('Y-m-d H:i:s');
    // Verifier s'il y a une correspondance avec le coupon tapé par l'utilisateur
    $stmt = $db -> prepare("SELECT * FROM coupons WHERE  code = :coupon AND debut <= :dateJ AND fin >= :dateJ ");//verifier aussi si la date a pas expiré
    $stmt-> execute(["coupon"=>$code, "dateJ"=> $dateJour]);
    $reduc = $stmt -> fetch(PDO::FETCH_ASSOC);

    // Verifier que le coupon existe 
    if(!empty($reduc)){
        $remise = $reduc['remise'];
        

        if($coupon["type"] == "%"){
            $remiseValue  = ($remise/100) * $totalPanier ;
     
            $_SESSION["remise"] = $remiseValue;
        }else{
            
            $_SESSION["remise"] = $remise;
          
        }
      
        $_SESSION['error'] = '';


        header('Location: panier.php?success=yes' );
        session_die();
        exit();
    }else{
        
        $error = ' Attention : le code remise saisi est incorrect !';
        $_SESSION['remise'] = '';
        $_SESSION['error'] = $error;
        header('Location: panier.php?success=no');
        session_die();
        exit();
    }

}

Database::disconnect();