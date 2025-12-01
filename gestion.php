<?php
session_start();
include("../conn_db.php");

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_connecte'])) {
    header("Location: admin.php");
    exit();
}

$message = "";

// Ajouter ou modifier un agent
if (isset($_POST['enregistrer'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $numero_interne = $_POST['numero_interne'];
    $telephone = $_POST['telephone'];
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);

    if (!empty($_POST['id_agent'])) {
        $id = $_POST['id_agent'];
        $sql = "UPDATE agents SET nom='$nom', prenom='$prenom', numero_interne='$numero_interne', telephone='$telephone', nom_utilisateur='$nom_utilisateur', motdepasse='$motdepasse' WHERE id_agent=$id";
        $message = "Agent modifié avec succès.";
    } else {
        $sql = "INSERT INTO agents (nom, prenom, numero_interne, telephone, nom_utilisateur, motdepasse) VALUES ('$nom', '$prenom', '$numero_interne', '$telephone', '$nom_utilisateur', '$motdepasse')";
        $message = "Nouvel agent ajouté.";
    }
    mysqli_query($conn, $sql);
}

// Supprimer un agent
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    mysqli_query($conn, "DELETE FROM agents WHERE id_agent=$id");
    header("Location: gestion.php");
    exit();
}

// Préparer la modification
$agent_modifier = null;
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $res = mysqli_query($conn, "SELECT * FROM agents WHERE id_agent=$id");
    $agent_modifier = mysqli_fetch_assoc($res);
}

// Liste des agents
$agents = mysqli_query($conn, "SELECT * FROM agents ORDER BY nom");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Agents</title>
    <link rel="stylesheet" href="gestion.css">
</head>
<body>
<div class="conteneur-admin">
    <h2>Gestion des Agents</h2>

    <?php if ($message): ?>
        <p class="success"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" class="boite-formulaire">
        <h3><?= $agent_modifier ? "Modifier l'agent" : "Ajouter un agent" ?></h3>
        <input type="hidden" name="id_agent" value="<?= $agent_modifier['id_agent'] ?? '' ?>">

        <label>Nom :</label>
        <input type="text" name="nom" value="<?= $agent_modifier['nom'] ?? '' ?>" required>

        <label>Prénom :</label>
        <input type="text" name="prenom" value="<?= $agent_modifier['prenom'] ?? '' ?>" required>

        <label>Numéro interne :</label>
        <input type="text" name="numero_interne" value="<?= $agent_modifier['numero_interne'] ?? '' ?>" required>

        <label>Téléphone :</label>
        <input type="text" name="telephone" value="<?= $agent_modifier['telephone'] ?? '' ?>" required>

        <label>Nom d'utilisateur :</label>
        <input type="text" name="nom_utilisateur" value="<?= $agent_modifier['nom_utilisateur'] ?? '' ?>" required>

        <label>Mot de passe :</label>
        <input type="password" name="motdepasse" required>

        <button type="submit" name="enregistrer"><?= $agent_modifier ? "Modifier" : "Ajouter" ?></button>

        <?php if ($agent_modifier): ?>
            <a href="gestion.php" class="bouton-retour">Annuler</a>
        <?php endif; ?>
    </form>

    <h3>Liste des agents</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Numéro interne</th>
            <th>Téléphone</th>
            <th>Nom d'utilisateur</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($agents)) { ?>
        <tr>
            <td><?= $row['id_agent'] ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['numero_interne']) ?></td>
            <td><?= htmlspecialchars($row['telephone']) ?></td>
            <td><?= htmlspecialchars($row['nom_utilisateur']) ?></td>
            <td>
                <a href="gestion.php?modifier=<?= $row['id_agent'] ?>">Modifier</a> |
                <a href="gestion.php?supprimer=<?= $row['id_agent'] ?>" onclick="return confirm('Supprimer cet agent ?')">Supprimer</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<div style="margin-bottom: 20px;">
    <a href="admin.php" class="btn-retour-admin">← Retour à l’espace administrateur</a>
</div>

</body>
</html>
