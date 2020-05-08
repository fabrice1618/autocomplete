<?php 

class ConvertUtil
{
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
    
    public static function convertString( $sInString )
    {
        $sOutString = "";
        $sInString = mb_strtolower($sInString);
    
        $nLen = mb_strlen($sInString);
        for ($i=0; $i < $nLen ; $i++) { 
            $sChar = mb_substr($sInString, $i, 1);
    
            if (
                ( $sChar === ' ' ) ||
                ( ( mb_ord($sChar)>=mb_ord('a') ) && ( mb_ord($sChar)<=mb_ord('z') ) ) ||
                ( ( mb_ord($sChar)>=mb_ord('0') ) && ( mb_ord($sChar)<=mb_ord('9') ) ) 
            ) {
                // Conserve le caractere tel quel
                $sOutString .= $sChar;
            } else {
    
                $sConvert = self::convertChar($sChar);
                if ($sConvert === false ) {
                    // Erreur pour caracteres inconnu
                    throw new Exception(__CLASS__.": convertString not known '$sChar' (".mb_ord($sChar).") ");

                } else {
                    $sOutString .= $sConvert;
                }
            }
        }
    
        return($sOutString);
    }

    public static function convertChar($sChar)
    {
        $sConvert = false;
    
        if ( isset( self::CONVERT_TABLE[mb_ord($sChar)] ) ) {
            $sConvert = self::CONVERT_TABLE[mb_ord($sChar)];
        }
    
        return($sConvert);
    }

    
}
