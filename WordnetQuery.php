<?php

include_once('Wordnet.php');

abstract class WordnetQuery implements WordNet
{
    /**
     * @param string $metadata
     * @return array
     */
    public function getSynonymes(string $metadata){

    	$myRes=array();
	    $res1 = '"'.WordnetPath.'" "'.$metadata.'" "-synsn"';
		$output1=shell_exec($res1);
		$patterns       = array(
		        '/^Synonyms.*\n/m',          # remove the '... Synonyms ...' line
		        '/.* senses of .*\n/',          # remove the '... senses of ...' line
		        '/.* sense of .*\n/',          # remove the '... senses of ...' line
		        '/^Sense.*\n/m',                # remove the 'Sense ...' lines
		        '/^ *\n/m',                      # remove empty lines
		        '/.* =>.*\n/'                      # remove empty lines
	         );
		$output1   = preg_replace( $patterns, "", $output1);
		$myRes=explode(',', $output1);
		return $myRes;
    }
    /**
     * @param string $metadata
     * @return array
     */
    public function getHypernonymes(string $metadata){
		$res1 = '"'.WordnetPath.'" "'.$metadata.'" "-hypen"';
		$output1=shell_exec($res1);
		$patterns       = array(
		        '/^Synonyms.*\n/m',          # remove the '... Synonyms ...' line
		        '/.* senses of .*\n/',          # remove the '... senses of ...' line
		        '/\-?\d+/',                # remove each number
	        );
		$output1   = preg_replace( $patterns, "", $output1);
		$output1   = explode( "Sense ", $output1);
		$output1 = array_reverse($output1);
		$myresult=array();
		//boucle each sens result
		foreach ($output1 as $key1=>$value1) {
			//get all nouns in path 
			//delete if sense has multiple path save only the first
			$value1  = explode( "=> entity", $value1);
			$res_1   = explode( "=>", $value1[0]);
			//$res_1   = preg_replace( "#[ ,;']+#" , "", $res_1);
			//add the deleted entity by explode
			array_push($res_1, "entity");
			array_push($myresult, $res_1);
		}
		unset($myresult[count($myresult)-1]);
		return $myresult;
    }
    /**
     * @param string $metadata
     * @param array $titles
     * @return array
     */
    public function getNearestNeighbors(string $metadata, array $titles){
    	return array();
    }
    /**
     * @param string $metadata
     * @param array $titles
     * @return array
     */
    public function getSemanticSimilarity($metadata, $term){
		$res1 = '"'.WordnetPath.'" "'.$metadata.'" "-hypen"';
		$res2 = '"'.WordnetPath.'" "'.$term.'" "-hypen"';
		$output1=shell_exec($res1);
		$output2=shell_exec($res2);
		$patterns       = array(
			'/^Synonyms.*\n/m',          # remove the '... Synonyms ...' line
			'/.* senses of .*\n/',          # remove the '... senses of ...' line
			'/.* sense of/',          # remove the '... sense of ...' line
			'/\-?\d+/',                # remove each number
			//'/\s+/',
			//'/Sense/',
			 );
		$output1   = preg_replace( $patterns, "", $output1);
		$output2   = preg_replace( $patterns, "", $output2);
		$output1   = explode( "Sense ", $output1);
		$output2   = explode( "Sense ", $output2);
		$output1 = array_reverse($output1);
		$output2 = array_reverse($output2);
		$my_result=array();
		$sims=array();
		//boucle each sens result
		foreach ($output1 as $key1=>$value1) {
			//get all nouns in path 
			//delete if sense has multiple path save only the first
			$value1  = explode( "=> entity", $value1);
			$res_1   = explode( "=>", $value1[0]);
			$res_1   = preg_replace( "#[ ,;']+#" , "", $res_1);
			//add the deleted entity by explode
			array_push($res_1, "entity");
			//search each noun in current sense
			foreach ($res_1 as $key=>$noun_1) {
				if ($key + 1 < count($res_1)) $parent = $res_1[$key + 1 ]; else $parent= $res_1[$key] ;
				$contain1=array_search($noun_1,$res_1);
				//boucle each senses result2
				foreach ($output2 as $key2=>$value2) {
					//get an array of words in each sense
					$res_2   = explode( "=>", $value2);
					$res_2   = preg_replace( "#[ ,;']+#" , "", $res_2);
					//search my current word on the liste of words in senses result2
					$contain=array_search($noun_1,$res_2);
					if($contain>0){
						$noun_2 = $res_2[array_search($noun_1,$res_2) ];
						if (array_search($noun_1,$res_2) + 1 < count($res_2))
							$parent2 = $res_2[array_search($noun_1,$res_2) + 1 ];
						else
							$parent2= $res_2[array_search($noun_1,$res_2)] ;

						$equal=strcmp ( $parent2 , $parent );
						if($equal == 0 ){
							$len = $contain + $contain1+1;
							array_push($my_result, $key2."=>".$len);
							//calculate similarity and make all values in my table of results
							array_push($sims, 1/$len);
							//unset($output2[$key2]);
						}else{
							array_push($sims, 0);
						}
					}else{
						array_push($sims, 0);
					}
				}
			}
		}
		return max($sims);
	}
}
