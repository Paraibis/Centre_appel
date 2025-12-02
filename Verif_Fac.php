
<?php
include(__DIR__ . "/../conn_db.php");

// --- 2. Initialisation AGI ---
$stdin = fopen('php://stdin', 'r');
$stdout = fopen('php://stdout', 'w');

// Lire les variables passées par Asterisk (ici, le numéro de facture)
$args = array();
while (!feof($stdin)) {
    $line = fgets($stdin);
    $line = str_replace("\n", "", $line);
    if ($line == "") break;
    
    // Les variables sont passées au format clé: valeur
    if (preg_match("/^agi_arg_([0-9]+):\s*(.*)$/", $line, $matches)) {
        $args[$matches[1]] = trim($matches[2]);
    }
}

// L'argument 1 est le numéro de facture que nous avons passé dans le dialplan :
// exten => verifier,1,AGI(check_bill_due.php,${REF_FACTURE})
$numero_facture = isset($args[1]) ? $args[1] : '';

// --- 3. Connexion à la base de données ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $result = 0; // 0 = Erreur, 1 = Succès
    $montant = 0;

    if (!empty($numero_facture)) {
        // Préparer la requête SQL
        $stmt = $pdo->prepare("SELECT montant FROM factures WHERE numero_facture = :numero AND statut = 'en_attente'");
        $stmt->bindParam(':numero', $numero_facture);
        $stmt->execute();
        $facture = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($facture) {
            $montant = $facture['montant'];
            $result = 1; // Facture trouvée et en attente
        }
    }

} catch (PDOException $e) {
    // En cas d'erreur de connexion ou de requête
    $result = 0;
    // Pour débogage : logguer l'erreur dans un fichier ou utiliser la fonction AGI 'VERBOSE'
}

// --- 4. Retourner les variables à Asterisk ---
// AGI_RESULT : 1 si la facture a été trouvée, 0 sinon.
// AGI_MONTANT : Le montant dû.

// Utiliser la fonction AGI 'SET VARIABLE' pour définir les variables dans Asterisk
fwrite($stdout, "SET VARIABLE AGI_RESULT \"$result\"\n");
fwrite($stdout, "SET VARIABLE AGI_MONTANT \"$montant\"\n");
fwrite($stdout, "SET VARIABLE AGI_NUM_FACTURE \"$numero_facture\"\n");

// Fin de la communication AGI
fwrite($stdout, "HANGUP\n");
fclose($stdin);
fclose($stdout);
?>