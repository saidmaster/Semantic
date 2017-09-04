<?php

/**
 * Description of MongoDBConnectionDB
 *
 * @author root
 */
class MongoDBConnectionDB {
    /**
     * Constructeur par default 
     */
    public function __construct(){}
    
    /**
     * get connection from mongodb
     * @return object \MongoDB\Client
     * 
     */
    public function getConnectionDB() {
        require 'vendor/autoload.php'; # include Composer's autoloader
        $conClient = new MongoDB\Client("mongodb://localhost:27017"); # instanciate class client 
        return $conClient;# return connection client which will be used to communicate with mongodb database
    }
}