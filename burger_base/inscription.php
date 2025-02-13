<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration and Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css">
  
    <style>
     
        .container-account {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .vertical-line {
           
            border-right: 1px solid black;
            margin-right:50px
           
           
        }
    </style>
</head>
<body>
<?php 
session_start();

if(isset($_SESSION['error'])&& !empty($_SESSION['error'])){
    echo '<div class="alert alert-danger" role="alert" style="text-align:center">' .$_SESSION['error']. "</div>";
}

?>



    <div class="container-account">
 
        <div class="row">
            <div class="col-md-4">
                <h3>Registration</h3>
                <form action="inscriptionREQ.php" method="post">
                    <div class="mb-3">
                        <label for="regName" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" id="regName" placeholder="Enter your name">
                    </div>
                    <div class="mb-3">
                        <label for="regEmail" class="form-label">Email </label>
                        <input type="email" class="form-control" name="mail" id="regEmail" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label for="regPassword" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="regPassword" name="mdp" placeholder="Enter a password">
                    </div>
                    <button type="submit" class="btn btn-primary">inscription</button>
                </form>
            </div>
            <div class="col-md-2 vertical-line"></div>
            <div class="col-md-4">
                <h3>Login</h3>
                <form action="connexionREQ.php" method="post">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email </label>
                        <input type="email" class="form-control" id="loginEmail" name="mail" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="loginPassword" name="mdp" placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary">connexion</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
