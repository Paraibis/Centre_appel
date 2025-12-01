<?php
session_start();
include(__DIR__ . "/../conn_db.php");

// ---------- Config admin ----------
$admin_nom = "admin";
$admin_motdepasse = "admin123";
$message = "";

// ---------- Déconnexion ----------
if (isset($_GET['deconnexion'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// ---------- Étape 1 : Continuer après "Espace réservé" ----------
if (isset($_POST['continuer'])) {
    $_SESSION['choix_role'] = true;
}

// ---------- Étape 2 : Choix du rôle ----------
if (isset($_POST['role'])) {
    if ($_POST['role'] === 'administrateur') {
        $_SESSION['afficher_formulaire_admin'] = true;
    } elseif ($_POST['role'] === 'agent') {
        // Définit le rôle agent pour que agent.php accepte l'accès
        $_SESSION['role'] = 'agent';
        header("Location: agent.php"); // chemin vers agent.php
        exit();
    }
}

// ---------- Validation identifiants admin ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['connexion'])) {
    $nom = $_POST['nom'];
    $motdepasse = $_POST['motdepasse'];

    if ($nom === $admin_nom && $motdepasse === $admin_motdepasse) {
        $_SESSION['admin_connecte'] = true;
    } else {
        $message = "Nom ou mot de passe incorrect.";
    }
}

// ---------- Statistiques ----------
$total = $rep = $nrp = $minutes = 0;
$liste_appels = [];

if (isset($conn) && isset($_SESSION['admin_connecte']) && $_SESSION['admin_connecte']) {
    $total   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM appels"))['t'];
    $rep     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM appels WHERE repondu = 1"))['t'];
    $nrp     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM appels WHERE repondu = 0"))['t'];
    $minutes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(duree)/60 AS total FROM appels"))['total'];
    $minutes = round($minutes, 1);

    $liste_appels = mysqli_query($conn, "SELECT * FROM appels ORDER BY date_appel DESC");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Admin PayPlus</title>
    <link rel="stylesheet" href="admin.css?v=<?=time()?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?= !empty($_SESSION['admin_connecte']) ? '' : 'fond-connexion'; ?>">


<?php if (!isset($_SESSION['admin_connecte'])): ?>

    <!-- Header avant connexion -->
    <header>
        <div class="header-container">
            <div class="logo-titre">
                <img src="../images/logo.jpg" alt="Logo PayPlus" class="logo-header">
                <h1>PayPlus</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="../services.php">Nos Services</a></li>
                    <li><a href="../index.php">À propos</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="admin.php">Espace Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php if (!isset($_SESSION['choix_role'])): ?>
        <!-- Étape 0 : Message "Espace réservé" -->
        <div class="conteneur-acces-admin">
            <img src="../images/acces.jpg" alt="Accès Admin" class="image-admin">
            <h2>Espace réservé aux administrateurs</h2>
            <p>⚠️ Cet espace est strictement réservé aux administrateurs de PayPlus.</p>
            <form method="post">
                <button type="submit" name="continuer" class="btn-admin">Continuer</button>
                <a href="../index.php" class="btn-retour">Retour</a>
            </form>
        </div>

    <?php elseif (!isset($_SESSION['afficher_formulaire_admin'])): ?>
        <!-- Étape 1 : Choix du rôle -->
        <div class="conteneur-acces-admin">
            <h2>Voulez-vous continuer en tant que :</h2>
            <form method="post">
                <button type="submit" name="role" value="administrateur" class="btn-admin">Administrateur</button>
                <button type="submit" name="role" value="agent" class="btn-admin">Agent</button>
                <a href="../index.php" class="btn-retour">Retour</a>
            </form>
        </div>

    <?php else: ?>
        <!-- Étape 2 : Formulaire admin -->
        <div class="conteneur-admin centrer-page">
            <h2>Connexion à l'espace administrateur</h2>
            <?php if ($message): ?><p class="erreur"><?= $message ?></p><?php endif; ?>
            <form method="post" class="boite-formulaire">
                <label>Nom d'utilisateur :</label><br>
                <input type="text" name="nom" required><br><br>
                <label>Mot de passe :</label><br>
                <input type="password" name="motdepasse" required><br><br>
                <button type="submit" name="connexion">Se connecter</button>
            </form>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Dashboard admin -->
    <header>
        <div class="header-container">
            <div class="logo-titre">
                <img src="../images/logo.jpg" alt="Logo PayPlus" class="logo-header">
                <h1>PayPlus</h1>
            </div>
            <div class="boutons-admin-header">
                <a href="gestion.php" class="btn-admin-dashboard"><i class="fa-solid fa-user-tie"></i> Gérer les agents</a>
                <a href="admin.php?deconnexion=1" class="btn-admin-dashboard"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
            </div>
        </div>
    </header>

    <div class="titre-dashboard">
        <h2>Tableau de bord</h2>
    </div>

    <!-- Statistiques -->
    <section class="stats-container">
        <div class="carte"><h2>☎Total Appels</h2><p class="valeur"><?= $total ?></p></div>
        <div class="carte"><h2>✔Appels Répondus</h2><p class="valeur"><?= $rep ?></p></div>
        <div class="carte"><h2>✖Non Répondus</h2><p class="valeur"><?= $nrp ?></p></div>
        <div class="carte"><h2>⏱Durée Totale</h2><p class="valeur"><?= $minutes ?> min</p></div>
    </section>

    <!-- Tableau des appels -->
    <h2 class="titre-table"><i class="fa-solid fa-phone"></i> Journal des appels Asterisk</h2>
    <div class="table-container">
        <table class="table-appels">
            <tr>
                <th>ID</th>
                <th>Numéro appelant</th>
                <th>Agent</th>
                <th>Durée</th>
                <th>Service</th>
                <th>Paiement</th>
                <th>Répondu</th>
                <th>Date</th>
            </tr>
            <?php while ($a = mysqli_fetch_assoc($liste_appels)) { ?>
                <tr>
                    <td><?= $a['id_appel'] ?></td>
                    <td><?= $a['numero'] ?></td>
                    <td><i class="fa-solid fa-user-tie"></i> <?= $a['agent'] ?></td>
                    <td><?= $a['duree'] ?> sec</td>
                    <td><?= $a['service'] ?></td>
                    <td><?= $a['statut_paiement'] ? '<span class="vert">✔ Payé</span>' : '<span class="rouge">✖ Non payé</span>' ?></td>
                    <td><?= $a['repondu'] ? '<span class="vert">Oui</span>' : '<span class="rouge">Non</span>' ?></td>
                    <td><?= $a['date_appel'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

<?php endif; ?>
</body>
</html>
