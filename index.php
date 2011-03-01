<?php

/**
* Page for accessing Decypher
*/

// load configuration
$GLOBALS['config'] = json_decode(file_get_contents('config.json'), true);

// load the grammar
$GLOBALS['grammar'] = json_decode(file_get_contents('grammar.json'), true);

include('/home/ferret/www/ServerConfig.php');

if (isset($_POST['english'])) {
	// we are receiving a post
	
	// grab the data
	$english = $_POST['english'];
	
	$obj = new Prose($english);
	
	// output it nicely
	foreach ($obj as $p=>$paragraph) {
		echo '<strong>Paragraph ' . $p . '</strong><br />';
		foreach ($paragraph as $s=>$sentence) {
			foreach ($sentence as $w=>$word) {
				echo $word . ' - <em>' . get_class($word) . '</em><br />';
			}
			$matches = $sentence->matchGrammar($GLOBALS['grammar']);
			print_r($matches);
			echo '<br />';
		}
		echo '<br />';
	}
} else {
	// we are not receiving a post - just render the homepage
	include('./Views/Home.php');
}