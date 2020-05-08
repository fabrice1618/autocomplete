<?php

define("QUERY_COMMUNE_READ",            "SELECT cp, commune, dep, dep_code, latitude, longitude FROM commune WHERE cp = :cp AND commune=:commune");
define("QUERY_COMMUNE_CP_INDEX",        "SELECT cp, commune FROM commune WHERE cp LIKE :cp LIMIT 10");
define("QUERY_COMMUNE_COMMUNE_INDEX",   "SELECT cp, commune FROM commune WHERE recherche LIKE :commune LIMIT 10");

class CommuneModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    public function cp_index($sCp)
    {

        $aResult=array();

        if ( !empty($sCp) ) {

            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_CP_INDEX);
            $stmt1->bindValue(':cp',  $sCp.'%',  PDO::PARAM_STR);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return($aResult);
    }

    public function commune_index($sCommune)
    {

        $aResult=array();

        if ( !empty($sCommune) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_COMMUNE_INDEX);
            $stmt1->bindValue(':commune',  $sCommune.'%',  PDO::PARAM_STR);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return($aResult);
    }

    public function read($sCp, $sCommune)
    {

        $aResult=array();

        if ( !empty($sCp) && !empty($sCommune) ) {
            $stmt1 = $this->dbh->prepare(QUERY_COMMUNE_READ);
            $stmt1->bindValue(':cp',       $sCp,       PDO::PARAM_STR);
            $stmt1->bindValue(':commune',  $sCommune,  PDO::PARAM_STR);

            if ( $stmt1->execute() ) {
                $aResult = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return($aResult[0]);
    }



}
