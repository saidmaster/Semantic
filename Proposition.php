<?php
require_once 'WordnetQuery.php';
class Proposition extends WordnetQuery {  
    /**
     * Searched word
     *
     * @var string
     */
    private $metadata;
    /**
     * @ip of user
     *
     * @var string
     */
    private $ip_user;
    /**
     * Get hyperonyme of the searched word
     *
     * @var string[]
     */
    private $phyperonymes;
    /**
     * Get synonymes of the searched word
     *
     * @var string[]
     */
    private $psynonymes;
    /**
     * Get similar word of the searched word
     *
     * @var string[]
     */
    private $pneighbor;
    /**
     * Proposition constructor.
     *
     * @param string $metadata
     * @param string $ip_user
     * @param string[] $hyperonyme
     * @param string[] $synonymes
     * @param string[] $neighbor
     */
    public function __construct($metadata) {
        $this->metadata = $metadata;
        $this->ip_user = $_SERVER['REMOTE_ADDR'];
    }
    /**
     * Set metadata
     *
     * @param string $metadata
     *
     * @return Proposition
     */
    public function setMetadata($metadata) {
        $this->metadata = $metadata;
        return $this;
    }
    /**
     * Get metadata
     *
     * @return string
     */
    public function getMetadata() {
        return $this->metadata;
    }
    /**
     * Get Psynonymes
     * @getPsynonymes
     * return array of synonymes
     */
    public function getPsynonymes() {
        return parent::getSynonymes($this->metadata);
    }
    /**
     * Get Psynonymes
     * @getPhyperonymes
     * return array of hyperonymes
     */
    public function getPhyperonymes() {
        return parent::getHypernonymes($this->metadata);
    }
    public function getSemanticSimilarity($metadata, $term) {
        return parent::getSemanticSimilarity($metadata, $term);
    }
}
