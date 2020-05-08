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

$sql = 'SELECT commune FROM commune';
try {
  $stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
  $stmt->execute();
  while ($aRow = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
    $sCommune = $aRow['commune'];
    $sConvert = ConvertUtil::convertString($sCommune);
  }
  $stmt = null;
}
catch (PDOException $e) {
  print $e->getMessage();
}

//Close database
$dbh = null;

