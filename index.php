<?php
session_start();
include("conn_db.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PayPlus - Paiement de Factures et Services</title>
    <link rel="stylesheet" href="style.css">
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
                <li><a href="#a-propos">À propos</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin/admin.php">Espace Admin</a></li>
            </ul>
           
        </nav>
    </div>
</header>


<!-- SECTION VIDEO -->
<section class="hero" id="accueil">
  <video autoplay muted loop playsinline class="hero-video">
    <source src="images/video2.mp4" type="video/mp4">
  </video>
</section>


<!-- A PROPOS -->
<section id="a-propos" class="a-propos">
  <div class="container">
    <h2>À propos de PayPlus</h2>
    <div class="apropos-content">

      <div class="apropos-images">
        <img src="images/center.jpeg" alt="">
        <img src="images/main.png" alt="">
      </div>

      <div class="apropos-text">
                <p><strong>PayPlus</strong> est votre centre d’appel pour régler vos factures facilement. Un agent vous répond rapidement et effectue le paiement immédiatement.</p>
                <p>Notre objectif est simple : payer vos factures sans vous déplacer et sans manipuler d’applications.</p>
                <p>PayPlus couvre : <strong>électricité, eau, internet, TV et partenaires divers</strong>.</p>
                <a href="services.php" class="btn-inscrire">Voir les services</a>
            </div>

    </div>
  </div>
</section>


<!-- TEXTE ENCADRÉ -->
<section class="a-propos">
    <div class="container">
        <div class="apropos-texte encadrer-bleu">
           
            <p><strong>Paiement sécurisé, rapide et disponible 24h/24.</strong></p>

            <p><em>Simplifiez vos factures. Payez mieux, payez PayPlus.</em></p>
        </div>
    </div>
</section>



<!-- 6 RAISONS -->
<section class="bloc-valeurs">
    <div class="ligne-haut"></div>

    <div class="bloc-logo">
        <img src="images/logo.jpg" alt="Logo PayPlus">
    </div>

    <h2 class="titre-valeurs">6 bonnes raisons de choisir PayPlus</h2>

    <p class="texte-valeurs">
        Depuis plus de 10 ans, PayPlus accompagne les ménages et les entreprises dans leur gestion quotidienne des paiements.
    </p>

    <div class="stats-container">
        <div class="stat-box">
            <h2>10 ans</h2>
            <p>d'expérience</p>
        </div>
        <div class="stat-box">
            <h2>20 000+</h2>
            <p>factures traitées</p>
        </div>
        <div class="stat-box">
            <h2>5</h2>
            <p>services essentiels</p>
        </div>
        <div class="stat-box">
            <h2>100%</h2>
            <p>sécurisé</p>
        </div>
        <div class="stat-box">
            <h2>24h/24</h2>
            <p>support actif</p>
        </div>
        
    </div>
</section>

<!-- BLOC TEMOIGNAGES -->
<section class="bloc-temoignages">
    <h2 class="titre-section">Témoignages de nos utilisateurs</h2>

    <div class="temoignages-container">
        <div class="carte-temoignage">
            <p class="texte-temoignage">"Je n’utilise pas les applications de paiement. Grâce à PayPlus, je paie toutes mes factures juste en appelant un agent."</p>
            <p class="auteur-temoignage">– Mme Diop, Dakar</p>
        </div>
        <div class="carte-temoignage">
            <p class="texte-temoignage">"Le service est rapide. L’agent m’a aidé à régler ma facture Senelec en moins de 2 minutes."</p>
            <p class="auteur-temoignage">– Moustapha B., Rufisque</p>
        </div>
        <div class="carte-temoignage">
            <p class="texte-temoignage">"Très pratique pour mes parents qui ne maîtrisent pas les téléphones modernes."</p>
            <p class="auteur-temoignage">– Aminata S., Thiès</p>
        </div>
    </div>
</section>


<?php
include("conn_db.php");

// Récupération des services Senelec et SenEau
$sql_services = "SELECT * FROM service WHERE id IN (1,3)";
$result_services = mysqli_query($conn, $sql_services);
?>

<!-- NOS SERVICES DE PAIEMENT -->
<section id="services" class="formations-accueil">
    <div style="text-align: center; margin-top: 30px;">
        <h2 class="titre-section">Nos Services de Paiement</h2>
        <p>Payer vos factures n’a jamais été aussi simple.</p>
    </div>

    <div class="grille-formations">
        <?php while($service = mysqli_fetch_assoc($result_services)) { ?>
            <div class="carte-formation">
                <div class="image-formation">
                    <img src="images/services/<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['nom_service']) ?>">
                </div>
                <div class="infos-formation">
                    <h3><?= htmlspecialchars($service['nom_service']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($service['description'])) ?></p>
                    
                </div>
            </div>
        <?php } ?>
    </div>

    <div style="text-align:center; margin: 30px;">
        <a href="services.php" class="btn-voir-plus">Voir tous les services</a>
    </div>
</section>




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
            <p>➤ Suivez-nous</p>
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
