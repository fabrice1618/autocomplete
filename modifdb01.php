<?php
require("lib/autoload.php");

date_default_timezone_set('Europe/Paris');
mb_internal_encoding("UTF-8");

// Configuration de la base de données
$dbu = new DBUtil('dbconfig.json');
try {
    $dbh = new PDO($dbu->getDBDSN(), $dbu->getDBUser(), $dbu->getDBPassword(), $dbu->getDBOptions() );
} catch (PDOException $e) {
    echo "Impossible de se connecter à la base de données: " . $e->getMessage() . "\n";
    die();
}

$sql = 'SELECT commune, cp FROM commune';
$sql_update = 'UPDATE commune SET recherche=:recherche WHERE cp=:cp AND commune=:commune';

try {
  $stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
  $stmt->execute();
  while ($aRow = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
    // Conversion du nom de commune
    $sCommune = $aRow['commune'];
    $sCp = $aRow['cp'];

    $sConvert = ConvertUtil::convertString($sCommune);
    echo "$sCommune = $sConvert\n";

    // Mise à jour de la base
    $stmt2 = $dbh->prepare($sql_update);
    $stmt2->bindValue(':recherche', $sConvert, PDO::PARAM_STR);
    $stmt2->bindValue(':cp', $sCp, PDO::PARAM_STR);
    $stmt2->bindValue(':commune', $sCommune, PDO::PARAM_STR);

    $stmt2->execute();
  

  }
  $stmt = null;
}
catch (PDOException $e) {
  print $e->getMessage();
}

//Close database
$dbh = null;

