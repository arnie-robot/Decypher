<?php

/**
* Decypher
*/

/**
* Processes the lexed and parsed prose
*/
class Processor
{
	/**
	* Constants outlining why sentences may have been skipped
	*/
	const SKIP_NO_VALID_SENTENCE = 0;
	const SKIP_NO_VALID_MATCHES = 1;
	
	/**
	* Array with explainations for skip messages
	*
	* @var array
	*/
	public $skip_messages = array(
		'The sentence found had no action associated with it',
		'No valid sentence structure was found in the grammar',
	);

	/**
	* The prose we will process
	*
	* @var Prose
	*/
	protected $prose;

	/**
	* Constructs the processor with the grammar and parsed prose
	*
	* @param Prose $proseparsed
	*
	* @return Processor
	*/
	public function __construct($prose)
	{
		$this->prose = $prose;
	}
	
	/**
	* Processes the prose using the grammar
	*
	* @return array
	*/
	public function process()
	{
		// initialise the variables that will be used for all paragraphs
		$variables = array();
		$labels = array();
		$skippedSentences = array();
	
		// iterate over all of the paragraphs
		foreach ($this->prose as $p=>$paragraph) {
			// each paragraph gets its own state variables which are re-initialised here
			
			// process each sentence
			foreach ($paragraph as $s=>$sentence) {
				// check to see if we got a decent level of match for a sentence
				$bestmatch = $sentence->getMatch();
				if (count($sentence)/2 >= $bestmatch['score']) {
					// failed to find a decent match, record and skip to next
					$skippedSentences[] = array('paragraph'=>$p, 'sentence'=>$s, 'value'=>$sentence, 'reason'=>self::SKIP_NO_VALID_MATCHES);
					continue;
				}

				// switch which action to perform based on the sentence type
				switch ($bestmatch['name']) {
					case 'label':		// label command
						$labels[(string)$bestmatch['data'][1]['found']] = (string)$bestmatch['data'][5]['found'];
						break;
					case 'variable':	// variable command
						$variables[(string)$bestmatch['data'][1]['found']] = (string)$bestmatch['data'][3]['found'];
						break;
					default:			// no actionable command found
						$skippedSentences[] = array('paragraph'=>$p, 'sentence'=>$s, 'value'=>$sentence, 'reason'=>self::SKIP_NO_VALID_SENTENCE);
						continue;
				}
			}
		}
		echo '<strong>Variables</strong><br />';
		foreach ($variables as $k=>$v) {
			echo "$k = $v<br />";
		}
		echo '<br /><strong>Labels</strong><br />';
		foreach ($labels as $k=>$v) {
			echo "$k = $v<br />";
		}
		echo '<br /><strong>Skipped Sentences</strong><br />';
		foreach ($skippedSentences as $sentence) {
			echo "Paragraph {$sentence['paragraph']}, sentence {$sentence['sentence']}, because " . $this->skip_messages[$sentence['reason']] . '<br />' . print_r($sentence, true) . '<br />';
		}
		echo '<br />';
		return array();
	}
}