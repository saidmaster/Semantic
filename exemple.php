<?php

include_once('./Proposition');
include_once('./Formulaire');
	$cc=new form('insertval.php');
	$cc->forum();
	if(isset($_GET['metadata'])){
                $metadata = $_GET['metadata'];
		$proposition= new Proposition($metadata);
		echo "<h1>Synonymes</h1>";
		echo "<pre>";
			print_r($proposition->getPsynonymes());
		echo "</pre>";
		echo "<h1>Hypernonymes</h1>";
		echo "<pre>";
			print_r($proposition->getPhyperonymes());
		echo "</pre>";	
	}
?>
