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
	
	$prose = new Prose($english);
	$res = $prose->matchGrammar($GLOBALS['grammar']);
	$processor = new Processor($prose);
	$res = $processor->process();
	print_r($res);
	exit;
	
	// output it nicely
	foreach ($prose as $p=>$paragraph) {
		echo '<strong>Paragraph ' . $p . '</strong><br />';
		foreach ($paragraph as $s=>$sentence) {
			foreach ($sentence as $w=>$word) {
				echo $word . ' - <em>' . get_class($word) . '</em><br />';
			}
			$matches = $sentence->matchGrammar($GLOBALS['grammar']);
			echo '<br /><strong>Results</strong><br />';
			foreach ($matches as $match) {
				echo $match['name'] . ': <strong>' . $match['score'] . '</strong><br />';
			}
			echo '<br />';
		}
		echo '<br />';
	}
} else {
	// we are not receiving a post - just render the homepage
	include('./Views/Home.php');
}