<?php
require 'bdd.php';
session_start();
$db = Database::connect();
$userTemp = $_SESSION['userTemp'] ?? null;
$userId = $_SESSION['userId'] ?? null;

if (!empty($userId)) {
    $query = 'SELECT panier.*, items.name, items.price, items.image 
              FROM panier
              INNER JOIN items ON panier.id_item = items.id
              WHERE user_id = ?';
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $query = 'SELECT pa.*, p.name, p.price, p.image 
              FROM panier pa
              INNER JOIN items p ON pa.id_item = p.id 
              WHERE userTemp = ? AND user_id IS NULL';
    $stmt = $db->prepare($query);
    $stmt->execute([$userTemp]);
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalPanier = 0;

// Calculer le total du panier
foreach ($panier as $item) {
    $totalPanier += $item['price'] * $item['qte'];
}

// Vérifiez si la clé 'remise' existe dans $_SESSION avant d'y accéder
$reduc = $_SESSION['remise'] ?? 0;

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="cart">
        <?php 
        if (!$panier) {
            echo '<div class="alert alert-danger" role="alert" style="text-align:center;">
            Votre panier est vide !
        </div>';
        }
        ?>
        <div class="cart-container">
            <div class="row justify-content-between">
                <div class="col-12">
                    <div class="">
                        <div class="">
                            <table class="table table-bordered mb-30">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Produit</th>
                                        <th scope="col">Prix unitaire</th>
                                        <th scope="col">Quantité</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($panier as $item): ?>
                                    <tr>
                                        <th scope="row">
                                            <a href="" class="btn-delete" data-id="<?= $item['id']; ?>"
                                                onclick="return confirm('Etes-vous sûr de vouloir supprimer ce produit de votre panier ?')">
                                                <i class="bi bi-archive"></i>
                                            </a>
                                        </th>
                                        <td>
                                            <img src="images/<?= $item['image'] ?>" style="width:100px" alt="<?= htmlspecialchars($item['name']); ?>">
                                        </td>
                                        <td>
                                            <small><?= htmlspecialchars($item['name']); ?></small>
                                        </td>
                                        <td class='prix-unitaire'><?= htmlspecialchars($item['price']); ?></td>
                                        <td>
                                            <div class="quantity" style="display:flex; justify-content:center; align-items:center">
                                                <a href="panier.php" class="changeQte" data-id="<?= $item['id']; ?>" data-action="decrease"
                                                    style="border:none; background-color:white; text-decoration:none; color:black; font-size:30px;">-</a>
                                                <span id="qtpanier"><?= htmlspecialchars($item['qte']); ?></span>
                                                <a href="panier.php" class="changeQte" data-id="<?= $item['id']; ?>" data-action="increase"
                                                    style="border:none; background-color:white; text-decoration:none; color:black; font-size:30px;">+</a>
                                            </div>
                                        </td>
                                        <td class='sous-total'><?= $item['price'] * $item['qte']; ?>€</td>
                                    </tr>
                                    <?php $totalPanier += $item['price'] * $item['qte']; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Coupon -->
                <div class="col-12 col-lg-6">
                    <div class="mb-30">
                        <h6>Avez vous un coupon?</h6>
                        <p>Entrer le code de la remise</p>
                        <?php if ($_SESSION['remise'] ?? '') { ?>
                            <div class="alert alert-primary" role="alert">
                                Vous avez ajouté un code de réduction !
                            </div>
                            <?php $reduc = $_SESSION['remise']; }
                            else { ?>
                            <div class="alert alert-danger" role="alert">
                                Attention : le code remise saisi est incorrect !
                            </div>
                            <?php $reduc = 0; } ?>
                        <div class="coupon-form">
                            <form action="reductionREQ.php" method="post">
                                <input type="text" class="form-control" name="code" placeholder="Entrer le code">
                                <input type="hidden" name="totalPanier" value="<?= $totalPanier ?>">
                                <button type="submit" class="btn btn-primary" style="margin-top:20px">Valider</button>
                            </form>
                        </div>
                        <br>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="mb-30">
                        <h5 class="mb-3">Total panier</h5>
                        <div class="">
                            <table class="table mb-3">
                                <tbody>
                                    <tr>
                                        <td>Total produit HT</td>
                                        <td id='HT'><?php 
                                        $totalPanier = $totalPanier - $reduc;
                                        if ($totalPanier < 0) {
                                            $totalPanier = 0;
                                        }
                                        echo $totalPanier; ?> €</td>
                                    </tr>
                                    <tr>
                                        <td>TVA</td>
                                        <td id="TVA"><?= $totalTVAPanier = $totalPanier * 0.2; ?> €</td>
                                    </tr>
                                    <tr>
                                        <td>Remise</td>
                                        <td><?= $reduc ?> €</td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL TTC</td>
                                        <td id='TTC'><?= $totalTTCPanier = $totalPanier + $totalTVAPanier; ?>€</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <a class="btn btn-primary" href="index.php"><span class="bi-arrow-left"></span> Retour</a>
        </div>
    </div>

    <script>
    // Mettre à jour la quantité
    document.querySelectorAll('.changeQte').forEach(function(btn){
        btn.addEventListener('click', function(e){
            const action = this.dataset.action;
            const id = this.dataset.id;
            let row = this.closest('tr');
            let qteEle = row.querySelector('span');
            let sousTotal = row.querySelector('.sous-total');
            let prixUnitaire = parseFloat(row.querySelector('.prix-unitaire').textContent);
            let totalPanier = 0;

            let newQte = parseInt(qteEle.textContent);

            if(action === 'increase'){
                newQte++;
            }
            if(action === 'decrease' && newQte > 1){
                newQte--;
            }

            fetch('upQteREQ.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&qte=${newQte}`
            })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'success'){
                    qteEle.textContent = newQte;
                    sousTotal.textContent = (prixUnitaire * newQte).toFixed(2) + '€';
                    document.querySelectorAll('.sous-total').forEach(function(st){
                        totalPanier += parseFloat(st.textContent);
                    });
                    document.querySelector('.total-panier').textContent = totalPanier.toFixed(2) + '€';
                } else {
                    console.log("Erreur");
                }
            });
        });
    });

    // Supprimer un produit du panier
    document.querySelectorAll('.btn-delete').forEach(function(btn){
        btn.addEventListener('click', function(e){
            const id = this.dataset.id;
            let row = this.closest('tr');
            const confirmation = confirm('Voulez-vous vraiment supprimer ce produit ?');

            if(confirmation){
                fetch('suppPanierREQ.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}`
                })
                .then(response => response.text())
                .then(data => {
                    if(data.trim() === 'success'){
                        row.remove();
                        let totalPanier = 0;
                        document.querySelectorAll('.sous-total').forEach(function(st){
                            totalPanier += parseFloat(st.textContent);
                        });
                        document.querySelector('.total-panier').textContent = totalPanier.toFixed(2) + '€';
                    } else {
                        console.log(`La suppression a échoué ${data}`);
                    }
                });
            }
        });
    });
    </script>
</body>

</html>