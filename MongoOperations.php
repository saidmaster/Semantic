<?php

require_once 'MongoDBConnectionDB.php'; # include connection client file
include_once 'Proposition.php'; # getHyperonyms and synonyms 
include_once 'Parametre.php'; # param of the whole plugin

# class of syntaxic similarity
require 'Phpml/Math/Distance/Euclidean.php';
require 'Phpml/Math/Distance/Manhattan.php';
require 'Phpml/Math/Distance/Chebyshev.php';
require 'Phpml/Math/Distance/Minkowski.php';
require 'Phpml/Preprocessing/Normalizer.php';

#vendor of lemmatized class
require_once __DIR__ . "/vendor/autoload.php";

# namespaces
use Phpml\Math\Distance\Euclidean;
use Phpml\Math\Distance\Manhattan;
use Phpml\Math\Distance\Chebyshev;
use Phpml\Math\Distance\Minkowski;
use Phpml\Preprocessing\Normalizer;
use Skyeng\Lemmatizer;
use Skyeng\Lemma;

class MongoOperations {

    /**
     * Searched word
     * @var  string
     */
    private $metadt;
    
    /**
     * @ip 
     * @var string 
     */
    private $ip;
    
    /**
     * value of filtrage collaboratif
     * @var float 
     */
    private $poid;
    
     /**
     * Get Ip
     * @return string
     */
    
    function getIp() {
        return $this->ip;
    }

     /**
     * Set ip
     *
     * @param string $ip
     *
     * @return void
     */
    function setIp($ip) {
        $this->ip = $ip;
    }

    /**
     * get metadata
     * @return string
     */
    function getMetadt() {
        return $this->metadt;
    }

     /**
     * Set metadata
     *
     * @param string $metadt
     *
     * @return void
     */
    function setMetadt($metadt) {
        $this->metadt = $metadt;
    }
     /**
     * Set poid
     * @return string
     */
    function getPoid() {
        return $this->poid;
    }
     /**
     * Set poid
     *
     * @param string $poid
     *
     * @return void
     */
    function setPoid($poid) {
        $this->poid = $poid;
    }

    # constructeur
    public function __construct($metadata = null, $ip = null, $poid = null) {
        $this->metadt = $metadata;
        $this->ip = $ip;
        $this->poid = $poid;
    }
     /**
     * the function of saving data on mongodb data base [ metadata, array syn, array hyper, ip client ]
     * @param null
     *
     * @return void
     */
    public function saveSearch() {
        # If the User Authenticated !!!
        if (is_user_logged_in()) { # testing if the user is logged in 
            # inserting document 
            # Getting Id of User 
            $idUser = get_current_user_id();    # getting the user Id 
            $userInfo = wp_get_current_user();  # getting the user global info 
            $userName = $userInfo->last_name;   # getting the user name
        } else {
            # Getting Id of User 
            $idUser = '';    # getting the user Id 
            $userInfo = '';  # getting the user global info 
            $userName = '';   # getting the user name
        }
        if (!empty($_POST['product'])) {
            $word = $_POST['product'];
        } else if (!empty($_POST['metadata'])) {
            $word = $_POST['metadata'];
        }
        # Synonyms 
        $proposition = new Proposition($this->metadt);
        $synonymes = $proposition->getPsynonymes();
        
        #document which will be inserted on mongodb 
        $document = array(# create a new Document 
            "idUser" => "$idUser", # id of the user
            "usr" => "$userName", # user name
            "host" => $this->ip, # ip client 
            "WordSearch" => "$this->metadt", # the word entred by the user
            "synonyms" => $synonymes, # array of synonyms
            "poid" => $this->poid # weight (filtrage collaboratif value)
        );
        
        #instance of MongoDBConnectionDB to save data into db -> db_semantic, collection -> colsem
        $con = new MongoDBConnectionDB();
        $conClient = $con->getConnectionDB();
        $db = $conClient->db_semantic; # connect to data base db_semantic
        $col = $db->colsem;         # select colsem collection 
        $collection = $conClient->selectCollection($db, $col); # calling selectCollection function 
        $collection->insertOne($document);

        # to drop your database
        #$conClient->dropDatabase($db);
    }

     /**
     *get visual table of user search  
     * 
     * @param int|string $iduser
     * @param string $by
     *
     * @return void
     */  
    public function getUserSearch($idUser, $by) {

        #instance of MongoDBConnectionDB to get data from db -> db_semantic, collection -> colsem
        $con = new MongoDBConnectionDB();
        $conClient = $con->getConnectionDB();
        $db = $conClient->db_semantic; # connect to data base db_semantic
        $col = $db->colsem;
        $collection = $conClient->selectCollection($db, $col);
        $arr = $collection->find();

        # you can remove this table after you will see your result
        echo "<table border='1'>"
        . "<tr>"
        . "<th>Id user</th>"
        . "<th>User Name</th>"
        . "<th>Metadata</th>"
        . "<th>IP Client</th>"
        . "<th>Synonyms</th>"
        . "<th>Poid</th>"
        . "</tr>";
        foreach ($arr as $a) {
            if ($by == "id" && $a['idUser'] == $idUser) { #check if user connected
                echo ""
                . "<tr>"
                . "<td>" . $a['idUser'] . "</td>"
                . "<td>" . $a['usr'] . "</td>"
                . "<td>" . $a['WordSearch'] . "</td>"
                . "<td>" . $a['host'] . "</td>"
                . "<td>";
                ?>
                <?php
                # here start looping array of synonyms
                foreach ($a['synonyms'] as $syno) {
                    echo " | " . $syno;
                }
                echo "<td>" . $a['poid'] . "</td></tr>";
            } elseif (strcmp($a['host'], $idUser) == 0) { # else get result by ip address
                echo ""
                . "<tr>"
                . "<td>" . $a['idUser'] . "</td>"
                . "<td>" . $a['usr'] . "</td>"
                . "<td>" . $a['WordSearch'] . "</td>"
                . "<td>" . $a['host'] . "</td>"
                . "<td>";
                ?>
                <?php
                # here start looping array of synonyms
                foreach ($a['synonyms'] as $syno) {
                    echo " | " . $syno;
                }
                echo "<td>" . $a['poid'] . "</td>";
            }
        }
        echo "</tr></table>";# end table
    }

    /**
     * get a non deplication array from words searched by unique user
     * 
     * @param int|string $idUser
     * @param string $by
     * @return string[]
     * 
     */
    public static function getArrayUserSearch($idUser, $by) {

        $array = array(); # this array will hold the values of searched words stored in mongo db 
        $returnArray = array(); # return function
        
        $con = new MongoDBConnectionDB();
        $conClient = $con->getConnectionDB();
        $db = $conClient->db_semantic;
        $col = $db->colsem;
        $collection = $conClient->selectCollection($db, $col);
        $arr = $collection->find();

        foreach ($arr as $a) {
            if ($by == "id" && $idUser == $a['idUser']) {
                array_push($array, $a['WordSearch']);
            } else if (strcmp($a['host'], $idUser) == 0) {
                array_push($array, $a['WordSearch']);
            }
        }
        $array_unique = array_unique($array); # removing duplicated values

        foreach ($array_unique as $a_key => $a_value) {
            array_push($returnArray, $a_value);
        }
        return $returnArray;
    }

    /**
     * semantic similar product by user search 
     * 
     * @param int|string $idUser
     * @param string $by
     * @return string[]
     * 
     */
    public function getSimilarProductbyUser($idUser, $by) { 
        $proposition = new Proposition($this->metadt);
        $con = new MongoDBConnectionDB();
        $conClient = $con->getConnectionDB();
        $db = $conClient->db_semantic; # connect to data base db_semantic
        $col = $db->colsem;
        $collection = $conClient->selectCollection($db, $col);
        $arr = $collection->find();
        $recommandation = array(); # return function 
        foreach ($arr as $a) {
            //here we have to put the similarity result to sort our table by similarity
            if (in_array($a['WordSearch'], $recommandation) == null || in_array($a['WordSearch'], $recommandation) == 0) {
                //echo "<br/> ww==> ".$this->metadt." & ".$a['WordSearch'];
                //echo "<br/> egale==> ". $this->SyntaxiqueDistance($this->metadt,$a['WordSearch']);
                if ($by == "id" && $a['idUser'] == $idUser) {
                    $sims = $proposition->getSemanticSimilarity($this->metadt, $a['WordSearch']);
                    if ($sims > SUEIL) {
                        array_push($recommandation, $a['WordSearch']);
                    }
                    $distanceSyn = $this->SyntaxiqueDistance($this->metadt, $a['WordSearch']);
                    if ($distanceSyn == 0) {
                        array_push($recommandation, $a['WordSearch']);
                    }
                } elseif (strcmp($a['host'], $idUser) == 0) {
                    $sims = $proposition->getSemanticSimilarity($this->metadt, $a['WordSearch']);
                    if ($sims > SUEIL) {
                        array_push($recommandation, $a['WordSearch']);
                    }
                    $distanceSyn = $this->SyntaxiqueDistance($this->metadt, $a['WordSearch']);
                    if ($distanceSyn == 0) {
                        array_push($recommandation, $a['WordSearch']);
                    }
                }
            }
        }
        return $recommandation;
    }

    /**
     * syntaxic similarity distance of two word
     * 
     * @param string $word1
     * @param string $word2
     * @return float
     * 
     */
    function SyntaxiqueDistance($word1, $word2) {
        //=======================> Semantique Distant
        $taille = max(strlen($word1), strlen($word2));
        $v1 = $this->VersVecteur($word1, $taille);
        $v2 = $this->VersVecteur($word2, $taille);
        $v1 = $this->normaliser($v1);
        $v2 = $this->normaliser($v2);

        $mesure = MESURE;
        $distance = -1;
        switch ($mesure) {
            case 'Eu' : {
                    $typeMesure = "Euclidean";
                    $euclidean = new Euclidean();
                    $distance = $euclidean->distance($v1, $v2);
                    break;
                }
            case 'Ma' : {
                    $typeMesure = "Manhattan";
                    $manhattan = new Manhattan();
                    $distance = $manhattan->distance($v1, $v2);
                    break;
                }
            case 'Ch' : {
                    $typeMesure = "Chebyshev";
                    $chebyshev = new Chebyshev();
                    $distance = $chebyshev->distance($v1, $v2);
                    break;
                }
            case 'Mi' : {
                    $typeMesure = "Minkowski";
                    $minkowski = new Minkowski();
                    $distance = $minkowski->distance($v1, $v2);
                    break;
                }
            case 'Le' : {
                    $typeMesure = "Levenshtein";
                    $distance = levenshtein($word1, $word2) / max(strlen($word1), strlen($word2));
                    break;
                }
        }
        return $distance;
    }

    /**
     * 
     * @param string $chaine
     * @param int $taille
     * @return string[]
     * 
     */
    function VersVecteur($chaine, $taille) {
        $vecteur = array();
        for ($i = 0; $i < strlen($chaine); $i++)
            array_push($vecteur, ord($chaine[$i]));
        $tailleVect = count($vecteur);
        if ($tailleVect < $taille)
            for ($i = 0; $i < $taille - $tailleVect; $i++)
                array_push($vecteur, 0);
        return $vecteur;
    }

    /**
     * 
     * @param string $vecteur
     * @return string
     * 
     */
    function normaliser($vecteur) {
        $normalizer = new Normalizer();
        $normalizer->normalizeL1($vecteur);
        return $vecteur;
    }

    /**
     * get the highest filtrage collaboratif of a specific user
     * 
     * @param int|string $idUser
     * @param string $by
     * @return strng[]
     */
    public function highWeight($idUser, $by) {
        $array = array(); # array final result
        $arrayWord = array(); # holds words result
        $arrayPoid = array(); # array holds the weight of user results
        $arraycomibe = array(); # combine the tow above arrays
        # including connection to mongodb
        include 'IncludeMongoFile.php';
        $collection = $conClient->selectCollection($db, $col); # selection db and collection
        $arr = $collection->find(); #getting all result 
        $j = 0;
        foreach ($arr as $a) {

            if ($by == "id" && $idUser == $a['idUser']) { # if the user authentified
                array_push($arrayPoid, $a['poid']);
                array_push($arrayWord, $a['WordSearch']);
            } else if (strcmp($a['host'], $idUser) == 0) {# if the user connected with ip adress
                array_push($arrayPoid, $a['poid']);
                array_push($arrayWord, $a['WordSearch']);
            }
        }
        $arraycomibe = array_combine($arrayWord, $arrayPoid);
        arsort($arraycomibe);
        if (count($arraycomibe) >= LIMITPOID) {
            $k = 0;
            foreach ($arraycomibe as $ar_value => $ar_key) {
                if ($k < LIMITPOID) {
                    array_push($array, $ar_value);
                    $k++;
                }
            }
        } else {
            foreach ($arraycomibe as $ar_value => $ar_key) {
                array_push($array, $ar_value);
            }
        }
        return $array;
    }

    /**
     * RETURN SOrted TABLE BY THE SEARCHED WORD
     * the most repeatd words
     * 
     * @param int|string $idUser
     * @param string $by
     * @return string[]
     * 
     */
    public function wordFreqency($idUser, $by) {
        $array = array(); # array will hold values from mongo db
        $arraySorted = array(); # array hold sorted indexed array
        $arrayfrenquecy = array();
        # including connection to mongodb
        include 'IncludeMongoFile.php';
        $collection = $conClient->selectCollection($db, $col);
        $arr = $collection->find();
        $i = 1;
        foreach ($arr as $a) {
            if ($by == "id" && $idUser == $a['idUser']) { # if the user authentified
                $array["word" . ++$i] = $a['WordSearch'];
            } else if (strcmp($a['host'], $idUser) == 0) {
                $array["word" . ++$i] = $a['WordSearch'];
            }
        }
        arsort($array); # sorting values of the array
        $arraySorted = array_values($array); #return an indexed array
        $arraycount = array_count_values($arraySorted); # counting values 
        arsort($arraycount); # sorted values of the associative array
        $j = 0;
        if (count($arraycount) >= LIMITFREQUENCY) {
            foreach ($arraycount as $key => $value) {
                if ($j < LIMITFREQUENCY) {
                    array_push($arrayfrenquecy, $key);
                }
                $j++;
            }
        } else {
            foreach ($arraycount as $key => $value) {
                array_push($arrayfrenquecy, $key);
            }
        }
        return $arrayfrenquecy; # return an associative array sorted by high frequency
    }

    /**
     * save user comment Collection -> colComment , db -> db_semantic
     * 
     * @param int $id
     * @param  string $comment
     * @param string $title
     * return void
     */
    public static function saveComment($id, $comment, $title) {
        $mongo = new MongoOperations();
        $document = array(# create a new Document 
            "id" => "$id", # id of the user
            "comment" => $mongo->myLemma($comment), # comment
            "title" => "$title"
        );
        $con = new MongoDBConnectionDB();
        $conClient = $con->getConnectionDB();
        $db = $conClient->db_semantic; # connect to data base db_semantic
        $col = $db->colComment;         # select comment collection 
        $collection = $conClient->selectCollection($db, $col); # calling selectCollection function 
        $collection->insertOne($document);
    }

    /**
     * get user comment 
     * 
     * @param int $id
     * @return string[]
     * 
     */
    public static function getUserComment($id) {
        $array_result = array();
        
        $con = new MongoDBConnectionDB();
        $conClient = $con->getConnectionDB();
        $db = $conClient->db_semantic; # connect to data base db_semantic
        $col = $db->colComment;         # select comment collection 
        $collection = $conClient->selectCollection($db, $col);
        $arrayComment = $collection->find(array("id" => "$id"));
        
        foreach ($arrayComment as $a) {
            array_push($array_result, $a['comment']);
        }
        return $array_result;
    }
    
    /**
     * get highest word weight unsing tf.idf
     * 
     * @param int $id
     * @param string $metadata
     * @return string
     * 
     */
    public function getSimilarProductFromComments($id, $metadata) {
        
        $associative_array = array(); # array hold the minimum value of tf-idf
        $array_name = array(); # name of element
        $array_value = array(); # valeur de element (calcule tf.idf a partir metadata et comment on mongodb)
        $idf = null; # calcule idf
        $tf_ar01 = null; # calcule tf for the firsr array
        $tf_ar02 = null; # calcule tf for the second array
        $count_f1 = null; # compter les element duplique ou repeter du premier tableau (metadatasearch)
        $count_f2 = null; # compter les element duplique ou repeter du deuzieme tableau (comment element)
        $mo = new MongoOperations();
        $metaSearch = $mo->myLemma($metadata); # array lemmatiser de la bare de recherche
        $meta_count_value = array_count_values($metaSearch);
        
        $to_array = function($array) {# anonymous function to return an array from object
            $array_r = array();
            foreach ($array as $a) {
                array_push($array_r, $a);
            }
            return $array_r;
        };

        $to_indexed_array = function($array) { #anonymous function to return an indexed array from associative array
            $array_result = array();
            foreach ($array as $value => $key) {
                array_push($array_result, $value);
            }
            return $array_result;
        };
        foreach (MongoOperations::getUserComment(get_current_user_id()) as $a) {
            $var_array = $to_array($a);
            $array_count_value = array_count_values($var_array);
            $sums = array(); // to count value from two associaltive array
            foreach (array_keys($array_count_value + $meta_count_value) as $key) {
                $sums[$key] = (isset($array_count_value[$key]) ? $array_count_value[$key] : 0) + (isset($meta_count_value[$key]) ? $meta_count_value[$key] : 0);
            }
            $array_merge = $sums;
            $count_merge = count($array_merge);
            foreach ($array_merge as $name => $value) { ##array holding all element
                if (in_array($name, $to_indexed_array($meta_count_value))) {
                    $count_f1 += 1;
                }
                if (in_array($name, $to_indexed_array($array_count_value))) {
                    $count_f2 += 1;
                }# calcule terme frequency
                $tf_ar01 = $count_f1 / ($count_merge); # calcule terme frequency
                $tf_ar02 = $count_f2 / ($count_merge);
                $idf = log(2 / $value); # calcule idf
                array_push($array_name, $name);
                array_push($array_value, ($tf_ar01 * $idf + $tf_ar02 * $idf));
                #render all variable to initial value ;
                $count_f2 = null;
                $count_f1 = null;
            }
        }
        $associative_array = array_combine($array_name, $array_value);
        arsort($associative_array); // sorting associative array from gt to less
        $arraysorted = array_reverse($associative_array); // return sorted array from less value to great value
        $final_array_result = array();
        foreach ($arraysorted as $key=>$value){
            array_push($final_array_result, $key);
        }
        $count = 0;
        $return_array = array();
        foreach($final_array_result as $arr_value){
            ($count<Limit_Comment) ? array_push($return_array, $arr_value) : 0;
        $count++;
                
        }
        return $return_array;
    }

    /**
     * 
     * @param string $sentent
     * @return string[]
     * 
     */
    public function myLemma($sentent) {

        $result = trim(preg_replace("/[^a-z0-9']+([a-z0-9']{1,2}[^a-z0-9']+)*/i", " ", " $sentent "));
        $varArrayResult = explode(' ', $result);
        $lemmatiser = new Lemmatizer();
        $array_final_result = array();
        $returnResult = array();
        $arrayPos = array(Lemma::POS_ADJECTIVE, Lemma::POS_ADVERB, Lemma::POS_NOUN, Lemma::POS_VERB);
        foreach ($varArrayResult as $a) {
            $bool = true;
            foreach ($arrayPos as $pos) {
                $lemmas = $lemmatiser->getOnlyLemmas($a, $pos);
                if ($lemmas[0] != $a) {
                    array_push($array_final_result, $lemmas[0]);
                    $bool = false;
                    break;
                }
            }
            if ($bool) {
                array_push($array_final_result, $lemmas[0]);
            }
        }
        return $array_final_result;
    }
}
