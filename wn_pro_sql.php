<?php

/**
 * Description of wn_pro_connection
 *
 * @author Said BAHAOUARY & Mina ESSALMI
 */
include_once 'MongoOperations.php';
class wn_pro_connection {

    public function __construct() {
        // default constructor 
    }
    /**
     * creat a DB connection of wordnet 
     * @return object \PDO
     */
    public static function getConnection() {
        $hostname = DB_PRO_HOST;
        $database = DB_PRO_NAME;
        $password = DB_PRO_PASSWORD;
        $username = DB_PRO_USER;

        try {
            $connection = new PDO("mysql:host=$hostname;dbname:$database", "$username", "$password", array(
                PDO::ATTR_PERSISTENT => true));
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            echo "Error " . $ex->getMessage();
        }
        return $connection;
    }

    /**
     * get all sense of id synsets
     * 
     * @param string $metadata
     * @return string[]
     * 
     */
    public static function getIdSynset($metadata) {
        $array_result = array();
        // selectionner l id du mot 
        $query = "SELECT DISTINCT `wn_pro_mysql`.wn_synset.synset_id from `wn_pro_mysql`.`wn_synset` where `wn_pro_mysql`.`wn_synset`.`word` = :metadata";
        $statement = wn_pro_connection::getConnection()->prepare($query);
        $statement->bindParam(':metadata', $metadata);
        $statement->execute();

        $results = $statement->fetchAll();
        foreach ($results as $r) {
            array_push($array_result, $r['synset_id']);
        }
        return $array_result;
    }

    /**
     * return word from id synset
     * 
     * @param string $idSynst
     * @return string
     * 
     */
    public static function getWordSynset($idSynst) {

        $query = "SELECT DISTINCT  wn_pro_mysql.wn_synset.word from wn_pro_mysql.wn_synset where wn_pro_mysql.wn_synset.synset_id = :id";
        $statement = wn_pro_connection::getConnection()->prepare($query);
        $statement->bindParam(':id', $idSynst);
        $statement->execute();
        $results = $statement->fetch();
        $wordSynset = $results[0];
        return $wordSynset;
    }

    /**
     * get array of hypernym from a an id synset
     * 
     * @param string $idSynset
     * @return string[]
     */
    public function getHypernym($idSynset) {
        $i = 0; 
        $array_assoc = array(); # we declar an associative array to hold idsynset and its value
        $array_key = array(); # array_key will hold the idsynsey
        $array_value = array(); # array_value will hold the value of the synset
        if ($idSynset == '100001740') { # if the idsynset equal this id (entity) so there is no hypernyms 
            array_push($array_key, 100001740);
            array_push($array_value, "entity");
        } else { # we search here for the value of metadata and we save its id in the array_key
            $sql = "SELECT DISTINCT `wn_pro_mysql`.`wn_synset`.`synset_id` , `wn_pro_mysql`.`wn_synset`.`word` from"
                    . "`wn_pro_mysql`.`wn_synset` WHERE "
                    . "`wn_pro_mysql`.`wn_synset`.`synset_id` = "
                    . "(SELECT DISTINCT `wn_pro_mysql`.`wn_hypernym`.`synset_id_2` FROM "
                    . "`wn_pro_mysql`.`wn_synset` INNER JOIN `wn_pro_mysql`.`wn_hypernym` on"
                    . "`wn_pro_mysql`.`wn_hypernym`.`synset_id_1` = `wn_pro_mysql`.`wn_synset`.`synset_id` "
                    . "and `wn_pro_mysql`.`wn_synset`.`synset_id` = :idSynset limit 1) LIMIT 1";
            $STH = wn_pro_connection::getConnection()->prepare($sql);
            $STH->bindParam(':idSynset', $idSynset);
            $STH->execute();
            $results = $STH->fetch();
            array_push($array_key, $results['synset_id']);
            array_push($array_value, $results['word']);
        }

        while ($array_key[$i] != '100001740') { # next we search the precedent value of the array_key and we loop intel we find the hypernym equal idsynset  entity
            $sql = "SELECT DISTINCT `wn_pro_mysql`.`wn_synset`.`synset_id` , `wn_pro_mysql`.`wn_synset`.`word` from "
                    . "`wn_pro_mysql`.`wn_synset` WHERE `wn_pro_mysql`.`wn_synset`.`synset_id` = "
                    . "(SELECT DISTINCT `wn_pro_mysql`.`wn_hypernym`.`synset_id_2` FROM"
                    . "`wn_pro_mysql`.`wn_synset` INNER JOIN `wn_pro_mysql`.`wn_hypernym` on"
                    . "`wn_pro_mysql`.`wn_hypernym`.`synset_id_1` = `wn_pro_mysql`.`wn_synset`.`synset_id` and "
                    . "`wn_pro_mysql`.`wn_synset`.`synset_id` = :idSynset limit 1) LIMIT 1";
            $STH = wn_pro_connection::getConnection()->prepare($sql);

            $param = $array_key[$i];
            $STH->bindParam(':idSynset', $param);
            $STH->execute();
            $resluts = $STH->fetch();

            if ($array_key[$i] == '') { #if the value of hypernym equal null we give this word entity as hypernym and we break the loop
                array_push($array_key, 100001740);
                array_push($array_value, "entity");
                break; # if there is no result break the loop and give the last array index value of entity
            }
            array_push($array_key, $resluts['synset_id']);
            array_push($array_value, $resluts['word']);

            $i = $i + 1;
        }
        $array_assoc = array_combine($array_key, $array_value);
        return $array_assoc;
    }

    /**
     * return a float value of the minimum simantic similarity based on ontology
     * 
     * @param string $w
     * @param string $m_db
     * @return float  
     * 
     */
    public static function getSemanticMesure($w, $m_db) {

        $array_v_w = array_values($w); # change the value to an indexed array
        $array_v_db = array_values($m_db);
        $array_sim = 0;
        # define the max path 
        $array_w_metadata = count($array_v_w) > count($array_v_db) ? count($array_v_w) : count($array_v_db);
        if (empty($array_v_db[0]) || empty($array_v_w[0])) {
            $array_sim = 0;
        } else {
            foreach ($array_v_db as $a) {# loop the element in the second array
                $array_index = array_search($a, $array_v_w); # get the index of the current element
                if (gettype($array_index) == "integer") { # here if no value return boolean if the key exist it return an integer value
                    $index_a = array_search($a, $array_v_db); # get the index of the second array
                    # semantic calculation based Leacock
                    switch (Semantic_mesure) {
                        case "leacock":
                            $array_sim = -log((1 + $array_index + $index_a ) / ($array_w_metadata * 2));
                            break;
                        case "path_based":
                            $array_sim = 1 / ($array_index + $index_a + 1 );
                            break;
                        case "wu_palmer":
                            $index = count($array_v_w) > count($array_v_db) ? $array_index : $index_a;

                            $array_sim = (2 * ($array_w_metadata - $index )) / (count($array_v_w) + count($array_v_db));
                            break;
                        case "jiang":
                            // still working on
                            break;
                    }
                    break;
                }
            }
        }
        return $array_sim;
    }

    /**
     * return the nearest simantic word of a unique user based on id or ip address
     *  
     * @param string $metadata
     * @param int|string $idUser
     * @param string $by
     * @return string[]
     * 
     */
    public function getSemanticWordsFromMongoDB_Metadata($metadata, $idUser, $by) {
        $arrray_lastValue = array();
        $array_sim = array();
        $array_result = array(); # array assiciative will hold simantic value and word
        $array_value_sim = array();
        $array_word = array();
        $arrayValueWords = MongoOperations::getArrayUserSearch($idUser, "id");
        $array_final_result= array();
        $c = 0;
        while ($c < count($arrayValueWords)) {
            $idsynst = wn_pro_connection::getIdSynset($arrayValueWords[$c]);
            foreach ($idsynst as $ids) {
                $arrayHypernyms = $this->getHypernym($ids);
                $idsynset = wn_pro_connection::getIdSynset($metadata);
                foreach ($idsynset as $i) {
                    $array_w_metadata = $this->getHypernym($i);
                    $semantic_mesure = $this->getSemanticMesure($arrayHypernyms, $array_w_metadata);
                    array_push($array_sim, $semantic_mesure);
                    $array_w_metadata = array();
                }
                $arrayHypernyms = array();
            }
            arsort($array_sim);
            foreach ($array_sim as $a) {
                array_push($arrray_lastValue, $a);
            }
            array_push($array_word, $arrayValueWords[$c]);
            array_push($array_value_sim, $arrray_lastValue[0]);
            $arrray_lastValue = array();
            $array_sim = array();
            $c++;
        }
        $array_result = array_combine($array_word, $array_value_sim);
        arsort($array_result);
        
        if (count($array_result) > Limit_semantic_pro) {
            $count = 0;
            foreach ($array_result as $value=>$sim) {
               if($count < Limit_semantic_pro){
                   array_push($array_final_result, $value);
               }
               $count ++;
            }
        }
        else {
           foreach ($array_result as $value=>$sim) {
                   array_push($array_final_result, $value);
            }
        }
        return $array_final_result;
    }
}
