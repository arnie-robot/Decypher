<?php

/**
* Page for accessing Decypher
*/

// load configuration
$config = json_decode(file_get_contents('config.json'), true);

include('/home/ferret/www/ServerConfig.php');
include('autoload.php');

if (isset($_POST['english'])) {
	// we are receiving a post
	
	// grab the data
	$english = $_POST['english'];
	
	// init the lexer
	$lexer = new English_Lexer($config['db']['host'],$config['db']['user'],$config['db']['pass'],$config['db']['db']);
	
	// split the input into paragraphs and sentences
	$paragraphs = explode("\r\n\r\n", $english);
	foreach ($paragraphs as $p=>$para) {
		$sentences = explode('.', $para);
		$all_sentences = array();
		foreach ($sentences as $s=>$sentence) {
			// explode out new lines too
			$sens = explode("\r\n", $sentence);
			foreach ($sens as $s_key=>$s_val) {
				if (strlen($s_val) < 1) {
					unset($sens[$s_key]);
				} else {
					$sens[$s_key] .= '.'; // add full stops back on
				}
			}
			foreach ($sens as $s) {
				$all_sentences[] = $s;
			}
		}
		$paragraphs[$p] = $all_sentences;
	}
	
	// lex the sentences
	foreach ($paragraphs as $p=>$paragraph) {
		foreach ($paragraph as $s=>$sentence) {
			$paragraphs[$p][$s] = $lexer->lex($sentence);
		}
	}
	
	// output it nicely
	foreach ($paragraphs as $p=>$paragraph) {
		echo '<strong>Paragraph ' . $p . '</strong><br />';
		foreach ($paragraph as $s=>$sentence) {
			foreach ($sentence as $w=>$word) {
				echo $word . ' - <em>' . get_class($word) . '</em><br />';
			}
			echo '<br />';
		}
		echo '<br />';
	}
} else {
	// we are not receiving a post - just render the homepage
	include('./Views/Home.php');
}