<?php
session_start();
include(__DIR__ . "/../conn_db.php");

$non_connecte = empty($_SESSION['agent_id']);

// ---------- Déconnexion ----------
if (isset($_GET['deconnexion'])) {
    session_destroy();
    header("Location: agent.php");
    exit();
}

// ---------- Vérifier si l'agent est connecté ----------
$agent_nom = '';
$id_agent = 0;
if (!empty($_SESSION['agent_connecte'])) {
    $agent_nom = $_SESSION['agent_nom'];

    // Récupérer ID agent connecté
    $stmt = mysqli_prepare($conn, "SELECT id_agent FROM agents WHERE nom_utilisateur = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $agent_nom);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    $id_agent = $row['id_agent'];
}

// ---------- Formulaire de connexion ----------
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    $nom_utilisateur = trim($_POST['nom']);
    $motdepasse      = trim($_POST['motdepasse']);

    $sql = "SELECT * FROM agents WHERE nom_utilisateur = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nom_utilisateur);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($agent = mysqli_fetch_assoc($result)) {
        if ($motdepasse === $agent['motdepasse']) {
            $_SESSION['agent_connecte'] = true;
            $_SESSION['agent_nom'] = $agent['nom_utilisateur'];
            header("Location: agent.php");
            exit();
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Nom d'utilisateur inexistant.";
    }
}

// ---------- Recherche client ----------
$client_info = null;
$factures = [];
$client_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chercher_client'])) {
    $telephone_client = trim($_POST['telephone_client']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM clients WHERE telephone = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $telephone_client);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($client_info = mysqli_fetch_assoc($result)) {
        $client_id = $client_info['id'];
        $factures = mysqli_query($conn, 
            "SELECT f.*, s.nom_service, s.image AS service_logo 
             FROM factures f 
             LEFT JOIN service s ON f.service_id = s.id
             WHERE f.client_id = $client_id"
        );
    } else {
        $client_message = "Client inexistant avec ce numéro.";
    }
}

// ---------- Paiement facture ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer_facture'])) {
    $id_facture = intval($_POST['id_facture']);
    $res_facture = mysqli_query($conn, "SELECT * FROM factures WHERE id_facture = $id_facture LIMIT 1");
    $facture = mysqli_fetch_assoc($res_facture);

    $client_id = $facture['client_id'];
    $client = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM clients WHERE id = $client_id"));

    if ($client['solde'] >= $facture['montant']) {
        $nouveau_solde = $client['solde'] - $facture['montant'];
        mysqli_query($conn, "UPDATE clients SET solde=$nouveau_solde WHERE id=$client_id");
        mysqli_query($conn, "UPDATE factures SET statut='payée', date_paiement=NOW() WHERE id_facture=$id_facture");

        // enregistrer le paiement
        mysqli_query($conn, 
            "INSERT INTO paiements (id_facture, montant_paye, methode, date_paiement) 
             VALUES ($id_facture, {$facture['montant']}, 'Espèces', NOW())"
        );

        // associer agent
        mysqli_query($conn, "UPDATE factures SET id_agent = $id_agent WHERE id_facture = $id_facture");

        $client_message = "Paiement effectué avec succès ! Nouveau solde : $nouveau_solde FCFA";
    } else {
        mysqli_query($conn, "UPDATE factures SET statut='échouée', id_agent=$id_agent WHERE id_facture=$id_facture");
        $client_message = "Solde insuffisant pour payer cette facture.";
    }

    // recharger client
    $stmt = mysqli_prepare($conn, "SELECT * FROM clients WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);
    $client_info = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $factures = mysqli_query($conn, 
        "SELECT f.*, s.nom_service, s.image AS service_logo 
         FROM factures f 
         LEFT JOIN service s ON f.service_id = s.id
         WHERE f.client_id = $client_id"
    );
}

// ---------- Terminer infos client ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminer_client'])) {
    $client_info = null;
    $factures = [];
    $client_message = '';
}

// ---------- Statistiques Agent ----------
$stats = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT 
        (SELECT COUNT(*) FROM appels WHERE id_agent=$id_agent) AS total_appels,
        (SELECT COUNT(*) FROM appels WHERE id_agent=$id_agent AND repondu=1) AS appels_repondu,
        (SELECT COUNT(*) FROM appels WHERE id_agent=$id_agent AND repondu=0) AS appels_non_repondu,
        (SELECT COUNT(*) FROM factures WHERE id_agent=$id_agent) AS total_factures,
        (SELECT COUNT(*) FROM factures WHERE id_agent=$id_agent AND statut='payée') AS factures_payees,
        (SELECT COUNT(*) FROM factures WHERE id_agent=$id_agent AND statut='échouée') AS factures_echouees"
));

// ---------- Appels récents ----------
$liste_appels = mysqli_query($conn, 
    "SELECT * FROM appels WHERE id_agent=$id_agent ORDER BY date_appel DESC LIMIT 20"
);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Agent</title>
    <link rel="stylesheet" href="agent.css?v=<?=time()?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?= !empty($_SESSION['agent_connecte']) ? '' : 'fond-connexion'; ?>">




<?php if (empty($_SESSION['agent_connecte'])): ?>
<div class="conteneur-admin">
    <h2>Connexion Agent</h2>
    <?php if (!empty($message)) echo '<p class="erreur">'.htmlspecialchars($message).'</p>'; ?>
    <form method="post" class="boite-formulaire">
        <input type="text" name="nom" placeholder="Nom d'utilisateur" required>
        <input type="password" name="motdepasse" placeholder="Mot de passe" required>
        <button type="submit" name="connexion">Se connecter</button>
    </form>
    <a href="admin.php" class="btn-retour">Retour Admin</a>
</div>

<?php else: ?>

<header>
    <h1>Tableau de bord Agent</h1>
    <div class="header-right">
        <span>Bienvenue, <?= htmlspecialchars($agent_nom) ?></span>
        <a href="agent.php?deconnexion=1" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </div>
</header>

<div class="stats-box">
    <h3>Statistiques de ton compte</h3>

    <div class="stats-grid">
        <div class="stat-card yellow">
            <h4>📞 Appels reçus</h4>
            <p><?= $stats['total_appels'] ?></p>
        </div>

        <div class="stat-card green">
            <h4>✅ Appels répondus</h4>
            <p><?= $stats['appels_repondu'] ?></p>
        </div>

        <div class="stat-card red">
            <h4>❌ Appels non répondus</h4>
            <p><?= $stats['appels_non_repondu'] ?></p>
        </div>

        <div class="stat-card blue">
            <h4>📄 Factures traitées</h4>
            <p><?= $stats['total_factures'] ?></p>
        </div>

        <div class="stat-card green2">
            <h4>💰 Factures payées</h4>
            <p><?= $stats['factures_payees'] ?></p>
        </div>

        <div class="stat-card red2">
            <h4>⚠️ Factures échouées</h4>
            <p><?= $stats['factures_echouees'] ?></p>
        </div>
    </div>
</div>

<div class="container">
    <!-- Panel Appels -->
    <div class="panel">
        <h3>Appels récents</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Numéro</th>
                <th>Répondu</th>
                <th>Action</th>
            </tr>
            <?php while ($appel = mysqli_fetch_assoc($liste_appels)): ?>
            <tr>
                <td><?= $appel['id_appel'] ?></td>
                <td><?= htmlspecialchars($appel['numero']) ?></td>
                <td><?= $appel['repondu'] ? 'Oui' : 'Non' ?></td>
                <td><a class="btn-action" href="agent.php?appel_id=<?= $appel['id_appel'] ?>">Voir client</a></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3>Rechercher un client</h3>
        <?php if (!empty($client_message)) echo '<p class="'.(strpos($client_message,'succès')!==false?'succes':'erreur').'">'.htmlspecialchars($client_message).'</p>'; ?>
        <form method="post">
            <input type="text" name="telephone_client" placeholder="Téléphone client" required>
            <button type="submit" name="chercher_client">Chercher</button>
        </form>
    </div>

    <!-- Panel Infos client / factures -->
    <div class="panel client-section">
        <?php if ($client_info): ?>
            <h3>Infos Client</h3>
            <p><strong>Nom :</strong> <?= htmlspecialchars($client_info['nom']) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($client_info['prenom']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($client_info['telephone']) ?></p>
            <p><strong>Solde :</strong> <?= $client_info['solde'] ?> FCFA</p>

            <h4>Factures</h4>

<div class="factures-cartes">
<?php while ($facture = mysqli_fetch_assoc($factures)):
    switch ($facture['statut']) {
        case 'en_attente': $bg='#fff8dc'; $emoji='⚠️'; $txt='En attente'; break;
        case 'payée': $bg='#d4edda'; $emoji='✅'; $txt='Payée'; break;
        case 'échouée': $bg='#f8d7da'; $emoji='❌'; $txt='Non payée'; break;
        default: $bg='#eee'; $emoji='❔'; $txt='Inconnu';
    }

    $service_img = ($facture['service_logo'] && file_exists("../images/services/".$facture['service_logo']))
                    ? "../images/services/".$facture['service_logo']
                    : "../images/services/default.png";
?>
    <div class="carte-facture" style="background: <?= $bg ?>;">
        <div class="carte-header">
            <img src="<?= $service_img ?>" class="service-logo">
            <h5><?= htmlspecialchars($facture['nom_service']) ?></h5>
        </div>

        <p><strong>Facture :</strong> <?= $facture['numero_facture'] ?></p>
        <p><strong>Montant :</strong> <?= $facture['montant'] ?> FCFA</p>
        <p><strong>Statut :</strong> <?= $emoji ?> <?= $txt ?></p>

        <?php if ($facture['statut'] == 'en_attente' || $facture['statut'] == 'échouée'): ?>
        <form method="post">
            <input type="hidden" name="id_facture" value="<?= $facture['id_facture'] ?>">
            <button type="submit" name="payer_facture" class="btn-action">Valider paiement</button>
        </form>
        <?php endif; ?>

    </div>
<?php endwhile; ?>
</div>

<form method="post">
    <button type="submit" name="terminer_client" class="btn-action btn-termine">Terminer</button>
</form>

        <?php else: ?>
            <h3>Informations du Client</h3>
            <p>Les informations du client apparaîtront ici après recherche.</p>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>
</body>
</html>
