<?php
include("conn_db.php");

// Initialisation du mot-clé
$mot_cle = "";

// Vérification de la recherche
if (isset($_GET['rechercher'])) {
    $mot_cle = $_GET['mot_cle'];
    $sql = "SELECT * FROM service 
            WHERE nom_service LIKE '%$mot_cle%' 
            OR description LIKE '%$mot_cle%'";
} else {
    $sql = "SELECT * FROM service";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Services - PayPlus</title>
    <link rel="stylesheet" href="services.css">
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo-titre">
            <img src="images/logo.jpg" alt="Logo PayPlus" class="logo-header">
            <h1>PayPlus</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="services.php">Nos Services</a></li>
                <li><a href="index.php#a-propos">À propos</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin/admin.php">Espace Admin</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Section Recherche -->
<section class="recherche">
    <div class="container">
        <form method="get" class="formulaire-recherche">
            <label for="mot_cle">Rechercher un service :</label>
            <input type="text" name="mot_cle" id="mot_cle" 
                   placeholder="Ex: Senelec, Canal+, Internet..." 
                   value="<?= htmlspecialchars($mot_cle) ?>">
            <button type="submit" name="rechercher">Rechercher</button>
        </form>
    </div>
</section>

<div style="text-align: center; margin-top: 30px;">
    <h2 class="titre-section">Nos Services</h2>
    <p>Payer vos factures n’a jamais été aussi simple.</p>
</div>

<!-- AFFICHAGE DYNAMIQUE DES SERVICES -->
<div class="grille-formations">
<?php
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) { ?>
       <div class="carte-formation">
    <div class="image-formation">
        <img src="images/services/<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'default.png' ?>" 
             alt="<?= htmlspecialchars($row['nom_service']) ?>">
    </div>
    <div class="infos-formation">
        <h3><?= htmlspecialchars($row['nom_service']) ?></h3>
        <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
    </div>
</div>

    <?php }
} else {
    echo '<p style="text-align:center; font-size:1.2em; margin-top:20px;">Aucun service trouvé avec ce nom.</p>';
}
?>
</div>

<!-- FOOTER -->
<section id="contact" class="contact">
<footer class="pied-de-page">
    <div class="contenu-footer">
        <div class="bloc">
            <img src="images/logo.jpg" alt="Logo PayPlus" class="logo-footer">
            <p><strong>PayPlus</strong></p>
            <p><em>- Simplifiez vos paiements -</em></p>
        </div>

        <div class="bloc">
            <h3>Contact</h3>
            <p>Dakar, Sénégal</p>
            <p>Tél. : +221 33 800 08 80</p>
            <p>Tél. : +221 33 805 05 50</p>
            <p>Email : contact@payplus.sn</p>
        </div>

        <div class="bloc">
            <h3>Recevoir les actualités</h3>
            <form class="formulaire-newsletter">
                <input type="email" placeholder="Votre e-mail">
                <button type="submit">OK</button>
            </form>
        </div>

        <div class="reseaux-sociaux">
            <p><span class="fleche">➤</span> Suivez-nous</p>
            <div class="icones">
                <a href="#"><img src="images/facebook.png" alt=""></a>
                <a href="#"><img src="images/linkedin.png" alt=""></a>
                <a href="#"><img src="images/x.png" alt=""></a>
                <a href="#"><img src="images/youtube.png" alt=""></a>
                <a href="#"><img src="images/instagram.png" alt=""></a>
            </div>
        </div>
    </div>
    <div class="bas-footer">
        <p>© PayPlus 2025 | Tous droits réservés</p>
    </div>
</footer>
</section>

</body>
</html>
