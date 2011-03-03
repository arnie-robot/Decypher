<?php

/**
* Decypher
*/

/**
* A sentence of prose, split into English_Token objects
*/
class Prose_Sentence extends Prose_Component
{
	/**
	* Array of wordsets we are using
	*
	* @var array
	*/
	protected $wordsets = array();

	/**
	* Splits the original content into tokens
	*
	* @return void
	*/
	protected function itemise()
	{
		$lexer = new English_Lexer($GLOBALS['config']['db']['host'],$GLOBALS['config']['db']['user'],$GLOBALS['config']['db']['pass'],$GLOBALS['config']['db']['db']);
		
		$this->items = $lexer->lex($this->original);
	}
	
	/**
	* Matches this sentence against the defined grammar, and returns what it finds
	*
	* @param array $grammar		the grammar configuration
	*
	* @return array
	*/
	public function matchGrammar($grammar)
	{
		extract($grammar); // get some variables out
		$this->wordsets = $wordsets;
		$results = array();
		
		// iterate over each of the sentence types
		foreach ($sentences as $name=>$sentence) {
			list($score, $data) = $this->compareSentences($sentence);		
			if ($score > 0) {
				$results[] = array('name'=>$name, 'score'=>$score, 'data'=>$data);
			}
		}
		usort($results, array($this, 'resultCompare'));
		return $results;
	}
	
	/**
	* Comparison function used for ordering the results array
	*
	* @param array $a
	* @param array $b
	*
	* @return int
	*/
	public function resultCompare($a, $b)
	{
		if ($a['score'] == $b['score']) {
			return 0;
		}
		return ($a['score'] > $b['score']) ? -1 : 1;
	}
	
	/**
	* Compares a sentence in the grammar and lexed format, returning a score
	*
	* @param array $sentence	grammar sentence
	*
	* @return array
	*/
	protected function compareSentences($sentence, $i = 0, $j = 0, $d = 0)
	{
		$score = 0;
		$data = array();

		// do some boundary checking on the input values
		if ($i >= count($sentence) || $j >= count($this->items)
			|| $i < 0 || $j < 0 || ($i == $j && $d > 1)) {
			// out of bounds
			return false;
		}

		// $i = matching (grammar) sentence
		// $j = internal sentence
		$maxsize = max(count($sentence), count($this->items));
		array_pad($sentence, $maxsize, false);
		$jsentence = $this->items;
		array_pad($jsentence, $maxsize, false);
		for ($i; $i < $maxsize; $i++) {
			// compare left element i with right element j
			if ($this->compare($sentence[$i], $jsentence[$j])) {
				// record item found
				$data[] = array('expected'=>$sentence[$i], 'found'=>$jsentence[$j], 'i'=>$i, 'j'=>$j);
				
				// add score if it's not optional
				if (!(isset($sentence[$i]['optional']) && $sentence[$i]['optional'])) {
					$score++;
				}
			} else if ($d < 1) {
				// we have detected a diff
				
				// check its not optional first so we can just skip over it
				if (isset($sentence[$i]['optional']) && $sentence[$i]['optional']) {
					// it's optional and not present, so try and skip over it
					// record the optional parameter missing
					$data[] = array('expected'=>$sentence[$i], 'found'=>false, 'i'=>$i, 'j'=>$j);
					
					// no score change as it is not present
					continue;
				}
				
				$choices = array();
				// run diffs +/- various positions to test for added or removed items
				for ($a = -1; $a < 2; $a+=2) {
					$res = $this->compareSentences($sentence, $i+$a+1, $j+1, $d+1);
					if ($res) {
						// score returned
						$choices[] = $res;
					}
					$res = $this->compareSentences($sentence, $i+1, $j+$a+1, $d+1);
					if ($res) {
						// score returned
						$choices[] = $res;
					}
				}
				$max = 0;
				$keep = -1;
				$new_i = -1;
				$new_j = -1;
				$new_data = false;
				foreach ($choices as $k=>$choice) {
					if ($choice[0] > $max) {
						$keep = $k;
						$max = $choice[0];
						$new_i = $choice[2] - 1;
						$new_j = $choice[3] - 1;
						$new_data = $choice[1];
					}
				}
				if ($keep >= 0) {
					// add the score on from the sub process
					$score += $max;
					// move i and j indices based on how far the sub-process got
					$i = $new_i;
					$j = $new_j;
					// add on the data too
					$data = $data + $new_data;
				} else {
					// not found, so record that
					$data[] = array('expected'=>$sentence[$i], 'found'=>false, 'i'=>$i, 'j'=>$j);
					$score--;
				}
			} else {
				// diff detected in sub process
				if ($score > 0) {
					// we have a score at least, so return that
					return array($score, $data, $i, $j);
				} else {
					// we got nothing
					return false;
				}
			}
			$j++;
		}
		
		return array($score, $data, $i, $j);
	}
	
	/**
	* Compares a definition for a sentence component (from config)
	* with an object from the sentence and returns bool
	*
	* @param array $sentenceComponent
	* @param English_Token $object
	*
	* @return bool
	*/
	protected function compare($sentenceComponent, $object)
	{
		if ($sentenceComponent === false || $object === false) {
			// we have run off the end
			return false;
		}
	
		extract($sentenceComponent);
		
		switch ($type) {
			case 'word':
				return (strtolower(trim((string) $object)) === strtolower(trim($value)));
			case 'wordset':
				$wordset = $this->wordsets[$value];
				if (count($wordset['types']) > 0) {
					$match = false;
					$objtype = str_replace('English_Token_', '', get_class($object));
					foreach ($wordset['types'] as $type) {
						if (substr($objtype, 0, strlen($type)) == $type) {
							$match = true;
							break;
						}
					}
					if (!$match) {
						return false;
					}
				}
				if (count($wordset['words']) > 0) {
					$match = false;
					$objword = strtolower(trim((string) $object));
					foreach ($wordset['words'] as $word) {
						if ($objword === strtolower(trim($word))) {
							$match = true;
							break;
						}
					}
					if (!$match) {
						return false;
					}
				}
				return true;
			case 'wordtype':
				$objtype = str_replace('English_Token_', '', get_class($object));
				return (substr($objtype, 0, strlen($value)) === $value);
		}
		return false;
	}
}