<?php

final class Parametre {

    const SUEIL = 0.2;
    const LIMIT = -1;
    const WordnetPath = "/usr/bin/wn";

    # le choix du trois types parametrable Click | search | cmd  Tracking User
    
    const POIDCLICK = 0.3;
    const POIDSEARCH = 0.1;
    const POIDCMD = 0.8;
    const MESURE = "Le";
    const LIMITFREQUENCY = 4;
    const LIMITPOID = 4;
    const SEMANTIC_MESURE = "wu_palmer";
    const LIMIT_SEMANTIC_PRODUCT = 4;
    const LIMIT_Comment_Array = 3;

    # param of sql wordnet
    const DB_PRO_HOST = "localhost";
    const DB_PRO_NAME = "wn_pro_mysql";
    const DB_PRO_USER = "root";
    const DB_PRO_PASSWORD = "root";
    # end choix
    
    public function __construct() {
        // Sueil percent value  to accept the similar word.
        define('SUEIL', self::SUEIL);

        // Limit of searched result.
        define('LIMITFREQUENCY', self::LIMITFREQUENCY);

        // Limit of searched result.
        define('LIMITPOID', self::LIMITPOID);

        // Limit of searched result.
        define('LIMIT', self::LIMIT);

        // MESURE of searched result.
        define('MESURE', self::MESURE);

        //Nom of Data Base MySQL wordnet.
        define('DB_PRO_NAME', self::DB_PRO_NAME);
        
         //User of Data Base MySQL wordnet.
        define('DB_PRO_USER', self::DB_PRO_USER);
        
        // Password of Data Base MySQL wordnet.
        define('DB_PRO_PASSWORD', self::DB_PRO_PASSWORD);
        
        // hebergement MySQL wordnet.
        define('DB_PRO_HOST', self::DB_PRO_HOST);

        // Field Name where we have to search.
        define('WordnetPath', self::WordnetPath);

        //Poid 3 types -> click | search  | cmd 
        define("poidClick", self::POIDCLICK);

        define("poidSearch", self::POIDSEARCH);

        define("poidCmd", self::POIDCMD);

        // Sematic Mesurement.
        define('Semantic_mesure', self::SEMANTIC_MESURE);

        //Limit Semanctic product 
        define('Limit_semantic_pro', self::LIMIT_SEMANTIC_PRODUCT);

        // array comment  
        define('Limit_Comment', self::LIMIT_Comment_Array);
    }

}
$Parametre = new Parametre();
