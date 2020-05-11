<?php

define("QUERY_COMMUNE_READ",                    
    "SELECT cp, commune, dep, dep_code, latitude, longitude FROM commune WHERE cp = :cp AND commune=:commune"
);
define("QUERY_COMMUNE_CP_SEARCH",               
    "SELECT cp, commune FROM commune WHERE cp LIKE :cp AND priorite=:priorite LIMIT :limit"
);
define("QUERY_COMMUNE_COMMUNE_SEARCH",          
    "SELECT cp, commune FROM commune WHERE recherche LIKE :commune AND priorite=:priorite LIMIT :limit"
);
define("QUERY_COMMUNE_CP_SEARCH_COUNT",         
    "SELECT COUNT(*) cnt FROM commune WHERE cp LIKE :cp"
);
define("QUERY_COMMUNE_COMMUNE_SEARCH_COUNT",    
    "SELECT COUNT(*) cnt FROM commune WHERE recherche LIKE :commune"
);

class CommuneModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    // Recherche des données par code postal
    public function cp_search($sCp, $nLimit, $sPriorite)
    {
        $aResult=array();

        if ( !empty($sCp) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_CP_SEARCH);
            $stmt1->bindValue(':cp',  $sCp.'%',  PDO::PARAM_STR);
            $stmt1->bindValue(':priorite',  $sPriorite,  PDO::PARAM_STR);
            $stmt1->bindValue(':limit',  $nLimit,  PDO::PARAM_INT);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return($aResult);
    }

    // Comptage des données par code postal
    public function cp_search_count($sCp)
    {
        $nCount=0;

        if ( !empty($sCp) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_CP_SEARCH_COUNT);
            $stmt1->bindValue(':cp',  $sCp.'%',  PDO::PARAM_STR);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                $nCount = $aResult[0]['cnt'];
            }
        }

        return($nCount);
    }

    // Recherche par nom de commune
    public function commune_search($sCommune, $nLimit, $sPriorite)
    {
        $aResult=array();

        if ( !empty($sCommune) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_COMMUNE_SEARCH);
            $stmt1->bindValue(':commune',  $sCommune.'%',  PDO::PARAM_STR);
            $stmt1->bindValue(':priorite',  $sPriorite,  PDO::PARAM_STR);
            $stmt1->bindValue(':limit',  $nLimit,  PDO::PARAM_INT);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return($aResult);
    }

    // Comptage par nom de commune
    public function commune_search_count($sCommune)
    {
        $nCount = 0;

        if ( !empty($sCommune) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_COMMUNE_SEARCH_COUNT);
            $stmt1->bindValue(':commune',  $sCommune.'%',  PDO::PARAM_STR);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                $nCount = $aResult[0]['cnt'];
            }
        }

        return($nCount);
    }

    // LEcture des données à propos d'une commune
    public function read($sCp, $sCommune)
    {
        $aReturn=array();

        if ( !empty($sCp) && !empty($sCommune) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_READ);
            $stmt1->bindValue(':cp',       $sCp,       PDO::PARAM_STR);
            $stmt1->bindValue(':commune',  $sCommune,  PDO::PARAM_STR);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }
            $aReturn = $aResult[0];
        }

        return($aReturn);
    }

}
