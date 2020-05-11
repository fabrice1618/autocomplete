<?php
/*
Format des parametres de l'API:
search: donnée à rechercher
limit: nombre de resultats à retourner
count: si count = 1, alors retourne le nombre de resultats
*/

// DEBUG_API constante permettant de générer le fichier api.log si DEBUG_API=1
define( "DEBUG_API", 1);

if ( defined("DEBUG_API") && DEBUG_API == 1 ) {
    // mesure performance
    $nTimeStart = microtime(true);
}

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

// Recuperation du parametre limit
$nLimit = intval($_GET['limit'] ?? "10");
if ($nLimit<=0 || $nLimit>1000 ) {
    $nLimit = 10;
}

// recuperation du parametre count
$lCount = false;
if ( isset($_GET['count']) && $_GET['count'] === "1" ) {
    $lCount = true;
}

// Recuperation du parametre search
$sSearch = $_GET['search'] ?? "";
if (!empty($sSearch)) {

    if (intval($sSearch)>0) {
        // Si $sSearch est un entier faire une recherche par code postal
        if ($lCount) {
            $sOutputAPI = comptage_codepostal($sSearch);
        } else {
            $sOutputAPI = recherche_codepostal($sSearch, $nLimit);
        }        
    }
    else {
        // Sinon faire une recherche par nom de commune
        if ($lCount) {
            $sOutputAPI = comptage_commune($sSearch);
        } else {
            $sOutputAPI = recherche_commune($sSearch, $nLimit);
        }        
    }
} else {
    $sOutputAPI = json_encode( false );
}

// Output Data
echo $sOutputAPI;

//Close database
$dbh = null;

if ( defined("DEBUG_API") && DEBUG_API == 1 ) {
    // mesure performances
    $nTimeEnd = microtime(true);
    $nDureeAPI = round(($nTimeEnd - $nTimeStart)*1000);
    
    // Ecriture du fichier log
    $fp = fopen("api.log", "a");
    if ($lCount) {
        fwrite($fp, "\nSearch=$sSearch Duree = $nDureeAPI ms\n");
        fwrite($fp, "Count=". $sOutputAPI );
    } else {
        fwrite($fp, "\nSearch=$sSearch Limit=$nLimit Duree = $nDureeAPI ms\n");
        fwrite($fp, $sOutputAPI );
    }
    fclose($fp);
}

/// Fin du programme principal
//////////////////////////////

// Comptage par code postal
function comptage_codepostal($sSearch)
{
    global $dbh;

    // Chargement du datamodel
    $commune = new CommuneModel($dbh);

    $nCount = $commune->cp_search_count($sSearch);

    return( json_encode($nCount) );
}

// Comptage par nom de commune
function comptage_commune($sSearch)
{
    global $dbh;

    // Chargement du datamodel
    $commune = new CommuneModel($dbh);

    $sSearch = ConvertUtil::convertString($sSearch);
    $nCount = $commune->commune_search_count($sSearch);

    return( json_encode($nCount) );
}

// Recherche par code postal en respectant les priorites
function recherche_codepostal($sSearch, $nLimit)
{
    global $dbh;

    // Chargement du datamodel
    $commune = new CommuneModel($dbh);

    $aReturn = array();

    // recherche de résultats terminant par "000" soit priorite="1"
    $aResult = $commune->cp_search($sSearch, $nLimit, "1");
    $aReturn = $aResult;
    $nMaxResult = $nLimit - count($aReturn);    // Nombre de resultats restants a chercher

    // recherche de résultats terminant par "00" soit priorite="2"
    if ($nMaxResult>0) {
        $aResult = $commune->cp_search($sSearch, $nMaxResult, "2");
        $aReturn = array_merge($aReturn, $aResult);     // Ajoute les 2 tableaux
        $nMaxResult = $nLimit - count($aReturn);    // Nombre de resultats restants a chercher
    }

    // recherche de résultats terminant par "0" soit priorite="3"
    if ($nMaxResult>0) {
        $aResult = $commune->cp_search($sSearch, $nMaxResult, "3");
        $aReturn = array_merge($aReturn, $aResult);     // Ajoute les 2 tableaux
    }

    return( json_encode( miseEnForme($aReturn) ) );
}

// Recherche par nom de commune en respectant les priorites
function recherche_commune($sSearch, $nLimit)
{
    global $dbh;

    // Chargement du datamodel
    $commune = new CommuneModel($dbh);

    $aReturn = array();

    $sSearch = ConvertUtil::convertString($sSearch);

    // recherche de résultats terminant par "000" soit priorite="1"
    $aResult = $commune->commune_search($sSearch, $nLimit, "1");
    $aReturn = $aResult;
    $nMaxResult = $nLimit - count($aReturn);    // Nombre de resultats restants a chercher

    // recherche de résultats terminant par "00" soit priorite="2"
    if ($nMaxResult>0) {
        $aResult = $commune->commune_search($sSearch, $nLimit, "2");
        $aReturn = array_merge($aReturn, $aResult);     // Ajoute les 2 tableaux
        $nMaxResult = $nLimit - count($aReturn);    // Nombre de resultats restants a chercher
    }

    // recherche de résultats terminant par "0" soit priorite="3"
    if ($nMaxResult>0) {
        $aResult = $commune->commune_search($sSearch, $nLimit, "3");
        $aReturn = array_merge($aReturn, $aResult);     // Ajoute les 2 tableaux
    }

    return( json_encode( miseEnForme($aReturn) ) );
}

// Mise en forme des resultats de requete $aResult en tableau pour export JSON
function miseEnForme( $aSuggestion )
{
    $aJson = array();
    
    foreach ($aSuggestion as $aCommune) {
        // Format: nom_de_commune (code_postal)
        $aJson[] = sprintf("%s (%s)", $aCommune['commune'], $aCommune['cp']);
    }

    return($aJson);
}