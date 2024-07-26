<?php
require('bdd.php');
$db = Database::connect();
session_start();
//pourquoi faire ca ici?
if (!isset($_SESSION['userTemp'])){
    $_SESSION['userTemp'] = time();
  }

  
$categories = $db->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);

if(isset($_SESSION['userId'])){
    $id=  $nom = htmlspecialchars($_SESSION['userId']);

  $userCourant= 'SELECT * FROM users WHERE id = :id';
  $stmt = $db->prepare($userCourant);
  $stmt -> bindValue('id',$id,PDO::PARAM_INT);// bider la valeur en specifiant que c'est un integer
  $stmt -> execute();
  $user =   $stmt -> fetch(PDO::FETCH_ASSOC); //fetch il va chercher un seul resultat et fetchAll cherche plusieurs resultats. FETCH_ASSOC rend le resultat moins vu qu'il retourne que les resultats alphabetiques et pas le numerique (de base fetch renvoie 2 champs, un champs numerique et un champs avec les resultats alphabetiques)

}
//$_SESSION stockés coté server 
// cookies stockés sur le navigateur

Database::disconnect();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Burger Code</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container site">

        <div style="text-align:center; display:flex; justify-content:center; align-items:center" class="text-logo">
            <h1>Burger Doe</h1>
            <?php if (isset($_SESSION['userId']) && isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'admin') { ?>
          <li class="nav-item">
            <a class="nav-link" href="admin/index.php">BackOffice</a>
          </li>
          <?php } ?>
           <?php if(isset($_SESSION['userId'])){
            ?>
                <span style="margin: 0 auto; font-size: 30%;"> Bonjour, <?= htmlspecialchars($user["nom"])?> </span>
           
           <?php }?>
            <a href="panier.php" class="bi bi-basket3 cart-icon"> </a>
            <a   class="bi mx-5 bi-person cart-icon" href="inscription.php"></a>
        </div>

        <!-- Navigation menu -->
        <nav>
            <ul class="nav nav-pills" role="tablist">
                <?php foreach ($categories as $cat) { ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= htmlspecialchars($cat['id']) == 1 ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="<?= '#tab' . htmlspecialchars($cat['id']); ?>" role="tab">
                            <?= htmlspecialchars($cat['name']); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
        
        <!-- Items -->
        <div class="tab-content">
            <?php foreach ($categories as $cat) { ?>
                <div class="tab-pane <?= htmlspecialchars($cat['id']) == 1 ? 'active' : '' ?>" id="<?= 'tab' . htmlspecialchars($cat['id']); ?>" role="tabpanel">
                    <div class="row">
                        <?php
                        $query = 'SELECT * FROM items WHERE category = ?';
                        $stmt = $db->prepare($query);
                        $stmt->execute([$cat['id']]);
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($products as $product) { ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="img-thumbnail">
                                    <img src="images/<?= $product['image'] ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']); ?>">
                                    <div class="price"><?= filter_var($product['price'], FILTER_VALIDATE_FLOAT) . " €" ?></div>
                                    <div class="caption">
                                        <h4><?= htmlspecialchars($product['name']); ?></h4>
                                        <p><?= htmlspecialchars($product['description']); ?></p>
                                        <a href="addPanierREQ.php?id=<?= htmlspecialchars($product['id']); ?>"  class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <script>

        </script>
</body>

</html>