<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Recherche commune</title>
        <script src="autocomplete.js"></script>
    </head>
    <body>
        <h1>Chercher une commune</h1>
        <form action="/autocomplete/index.php" method="post" name="form-loc" id="form-loc">
            <label for="count-input">Nombre de résultats</label>
            <input type="text" id="count-input" name="count-input" size=5 readonly value="0">
            <br />
            <label for="loc-input">Localisation</label>
            <input type="text" id="loc-input" name="loc-input" list="loc-datalist" size=50 placeholder="Ville ou Code postal...">
            <datalist id="loc-datalist">
            </datalist>
            <input type="submit" value="OK"  id="bouton-submit">
        </form>

<?php
require("lib/autoload.php");

date_default_timezone_set('Europe/Paris');

// Configuration et ouverture de la base de données
$dbu = new DBUtil('dbconfig.json');
try {
    $dbh = new PDO($dbu->getDBDSN(), $dbu->getDBUser(), $dbu->getDBPassword(), $dbu->getDBOptions() );
} catch (PDOException $e) {
    echo "Impossible de se connecter à la base de données: " . $e->getMessage() . "\n";
    die();
}

// Chargement du datamodel
$commune = new CommuneModel($dbh);

$sSearch = $_POST['loc-input'] ?? "";
if (!empty($sSearch)) {
    // Format du champ: nom_commune (code_postal) 

    // Recuperation du code postal de la commune
    $nPos1 = strpos($sSearch, '(' );
    $nPos2 = strpos($sSearch, ')' );
    $sCp = substr($sSearch, $nPos1+1, $nPos2-$nPos1-1);

    // Recuperation du nom de la commune
    $sCommune = substr($sSearch, 0, $nPos1-1);

    // Recherche des données de la commune
    $aResult = $commune->read($sCp, $sCommune);

    if ( count($aResult) > 0 ) {
        echo sprintf("Commune: %s<br>", $aResult['commune']);
        echo sprintf("Code postal: %s<br>", $aResult['cp']);
        echo sprintf("Departement: %s %s<br>", $aResult['dep_code'], $aResult['dep']);
        echo sprintf("Latitude: %s<br>", $aResult['latitude']);
        echo sprintf("Longitude: %s<br>", $aResult['longitude']);
    } else {
        echo "Commune non trouvée";
    }

}
?>

    </body>
</html>
