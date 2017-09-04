<?php
interface Wordnet
{
    /**
     * @param string $metadata
     * @return array
     */
    public function getSynonymes(string $metadata);

    /**
     * @param string $metadata
     * @return array
     */
    public function getHypernonymes(string $metadata);

    /**
     * we return an array of nearest words
     * = the semantique similarity between
     * this searched word and the other words in devlopper's table is inferior of the choosed Seuil
     * so for this we need to configure our seuil first
     * and give the searched word $metadata + the other words to compare $titles
     * @param string $metadata
     * @param array $titles
     * @return array
     */
    public function getNearestNeighbors(string $metadata, array $titles);

}
