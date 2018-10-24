
    #Authors        : Said BAHAOUARY & Mina ESSALMI
    #Plugin name    : Semantic 
    #Version        : O.O1
    #contact info   :   saidmaster88@gmail.com
                        essalmi.mina@gmail.com
    #User Guid      :
 
################################# Quick introduction ############################################

      Our Plugin is designed to all Developer using PHP technology specialy  E-Commerce  WebSite,
a  powerfull  tool  stand beside your user experience,it will facilitate products suggestions and
pushing attractiv semantic products too, also provide some products based on collaborative filtering.
It is simple to integrate with your website, you need just to follow ours instruction.

#################################################################################################

#################################### Installation ###############################################

Our Plugin is based on some external resources that should be installed before using Semantic.

A => Install wordnet exe
    Ex: Linux OS
    Debian :
       # sudo apt-get update
       # apt-get install wordnet
    
B => Install MongoDB Community Edition :

    MongoDB only provides packages for 64-bit LTS (long-term support) Ubuntu releases. For example, 12.04 LTS (precise), 14.04 LTS (trusty), 16.04 LTS (xenial), and so on. These packages may work with other Ubuntu releases, however, they are not supported.

    1=> Import the public key used by the package management system
    sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6

    2=> Create a list file for MongoDB

    Ubuntu 12.04
    echo "deb [ arch=amd64 ] http://repo.mongodb.org/apt/ubuntu precise/mongodb-org/3.4 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.4.list
    Ubuntu 14.04
    echo "deb [ arch=amd64 ] http://repo.mongodb.org/apt/ubuntu trusty/mongodb-org/3.4 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.4.list
    Ubuntu 16.04
    echo "deb [ arch=amd64,arm64 ] http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.4 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.4.list

    3=> Reload local package database
    sudo apt-get update

    4=> Install the MongoDB packages

    sudo apt-get install -y mongodb-org

    5=> to remove package
    sudo apt-get purge mongodb-org*


    Note: 
    To run mongodb server
    sudo mongod

    To start|stop|restart mongod service

    #check status
    sudo systemctl status mongodb.service

    sudo systemctl start mongodb.service 		or sudo /etc/init.d/mongod start
    sudo systemctl stop mongodb.service 		or sudo /etc/init.d/mongod stop
    sudo systemctl restart mongodb.service 		or sudo /etc/init.d/mongod restart

    for more inforamation visite the whole documentation at : https://docs.mongodb.com/manual/installation/
    
C => import on your database wn_pro_mysql.
link : 

https://drive.google.com/file/d/0B0nk1ILBKD8EOUw2bFNmSU9mVWs/view?usp=sharing

Nota : mongodb needs to add its extension to your php config files

##################################################################################################

##################################### Example ####################################################

Semantic gives you more many arrays based on semantic and syntaxic user search, also some based on 
collaborative filtering, here is some example to use our Plugin

$mongo = new MongoOperations($metadatan,$id,$poid);
      # instrance of MongoOperations class
      # it will be used on most mongodb operations
      # $metadata : searched word|null
      # $id       : adresse IP | ID USER|null
      # $poid     : click|command|search // constante var
       exist in Parametre Class

I  ) saving data on mongo db 

1 => save user search|click|products command
    $mongo->saveSearch();

2 => save user Comments using a static function saveComment
    MongoOperations::saveComment($id, $comment , $product_title );

    # $id               : user id 'ip'| 'id'
    # $comment          : user comment 
    # $product_title    : name of product

II ) Syntaxic Arrays & collaborative filtering array results


1 => array of most frequency word (popular product);
$array_freq = $mongo->wordFreqency($id, "id"); / "id" => used id 

2 => array of importance words words (popular product);
$array_poid = $mongo->highWeight($id, "id");


3 => get the most attractiv products based on user comments 
    $mongo->getSimilarProductFromComments($id,$metadata); // return array

III ) simantic Arrays from db mongodb database, synonyms and hyperonyms

1 => get synonyms and hypernyms of word search;
       $proposition = new Proposition($metadata); // word search
       $synonymes = $proposition->getPsynonymes(); // array of synonyms 
       $hyperonymes = $proposition->getPhyperonymes(); // array of hypernyms

2 => semantic array holds both syntaxic and semantic meaning of user search
    $mongo->getSimilarProductbyUser($wordSearch, $by);
    #wordSearch : searched word or product
    $by         : 'id'|'ip' // ip adress of user id

3 => semantic product based on user experience // mongodb data of each user
    $wn_proMysql->getSemanticWordsFromMongoDB_Metadata($metadata, $idUser, "id"); 
    // array of semantic products

#################################################################################################

############################################# Nota ##############################################

    all variables in Parametre Class could be change, that's gives you different calcul result and 
arrays output.

#################################################################################################

VI Plugin Parameters
    
    # SUEIL                         : Acceptance threshold
    # LIMIT                         : Limit of articles per result
    # WordnetPath                   : Path of Wordnet exe
    # POIDCLICK                     : Weight of clicked articles
    # POIDSEARCH                    : Weight of Searched articles
    # POIDCMD                       : Weight of commanded articles
    # MESURE                        : Used Mesure for syntaxic similarity
    # LIMITFREQUENCY                : Limit of the most popular product result   
    # LIMITPOID                     : limit of the high weight product result
    # SEMANTIC_MESURE               : Used mesure for semantic similarity
    # LIMIT_SEMANTIC_PRODUCT        : Limit of simantic articles results
    # LIMIT_Comment_Array           : limit from commented similarity results 
    # DB_PRO_HOST                   : hostname of SQL wordnet Database 
    # DB_PRO_NAME                   : DataBase Name of SQL Wordnet 
    # DB_PRO_USER                   : User name of SQL Wordnet
    # DB_PRO_PASSWORD               : Password of SQl Wordnet


################### Thanks For Trying, contact us in every new suggestions ######################









