
#Authors        : Said BAHAOUARY & Mina ESSALMI
#Plugin name    : Semantic 
#Version        : O.O1
#contact info   : saidmaster88@gmail.com
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

1 => Install wordnet exe
    Ex: Linux OS
    Debian :    # apt-get update
                # apt-get install wordnet
    
2 => Install mongodb Data Base
3 => import on your database wn_pro_mysql.

Nota : mongodb needs to add its extension to your php config files

##################################################################################################

##################################### Example ####################################################

Semantic gives you more many arrays based on semantic and syntaxic user search, also some based on 
collaborative filtering, here is some example to use our Plugin

$mongo = new MongoOperations($metadatan,$id,$poid);   # instrance of MongoOperations class
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



################### Thanks For Trying, contact us in every new suggestions ######################




























