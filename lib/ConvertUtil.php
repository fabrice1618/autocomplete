<?php 

class ConvertUtil
{
    // Table de conversion
    // Index = Code UTF-8
    // Valeur = Caractere de remplacement
    // En commentaire le caractere original
    const CONVERT_TABLE = [
        39 => ' ',      // '
        40 => ' ',      // (
        41 => ' ',      // )
        45 => ' ',      // -
        224 => 'a',     // à
        226 => 'a',     // â
        231 => 'c',     // ç
        232 => 'e',     // è
        233 => 'e',     // é
        234 => 'e',     // ê
        235 => 'e',     // ë
        237 => 'i',     // í
        238 => 'i',     // î
        239 => 'i',     // ï
        243 => 'o',     // ó
        244 => 'o',     // ô
        250 => 'u',     // ú
        251 => 'u',     // û
        252 => 'u',     // ü
        255 => 'y',     // ÿ
        339 => 'oe',    // œ
        8217 => ' '     // '
    ];
    
    // Conversion d'une chaine UTF-8, utilisation des fonctions mb_*
    public static function convertString( $sInString )
    {
        $sOutString = "";
        $sInString = mb_strtolower($sInString);
    
        // Parcourir les caractères de la chaine
        $nLen = mb_strlen($sInString);
        for ($i=0; $i < $nLen ; $i++) { 
            $sChar = mb_substr($sInString, $i, 1);

            if (
                ( $sChar === ' ' ) ||
                ( $sChar >= 'a' && $sChar <= 'z' ) ||
                ( $sChar >= '0' && $sChar <= '9' ) 
            ) {
                // Conserve le caractere tel quel
                $sOutString .= $sChar;
            } else {
                $sConvert = self::convertChar($sChar);
                if ($sConvert === false ) {
                    // Erreur pour caracteres inconnu
                    throw new Exception(__CLASS__.": convertString unknown character '$sChar' (".mb_ord($sChar).") ");
                } else {
                    // Conversion du caractère
                    $sOutString .= $sConvert;
                }
            }
        }
    
        return($sOutString);
    }

    public static function convertChar($sChar)
    {
        $sConvert = false;
    
        $nCodeChar = mb_ord($sChar);    // Code UTF-8 du caractere
        // Si une conversion existe dans la table, retourner le caractere converti
        if ( isset( self::CONVERT_TABLE[$nCodeChar] ) ) {
            $sConvert = self::CONVERT_TABLE[$nCodeChar];
        }
    
        return($sConvert);
    }

    
}
