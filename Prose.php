<?php

/**
* Decypher
*/

/**
* A block of prose, comprised of multiple paragraphs
*/
class Prose extends Prose_Component
{
	/**
	* The separator for returning to string
	*
	* @var string
	*/
	protected $separator = "\n\n";

	/**
	* Splits the original content into paragraphs
	*
	* @return void
	*/
	protected function itemise()
	{
		$paragraphs = explode("\r\n\r\n", $this->original);
		
		foreach ($paragraphs as $text) {
			$this->items[] = new Prose_Paragraph($text);
		}
	}
	
	/**
	* Matches the paragraphs in the prose against the defined grammar, and returns what it finds
	*
	* @param array $grammar		the grammar configuration
	*
	* @return array
	*/
	public function matchGrammar($grammar)
	{
		$result = array();
		foreach ($this->items as $item) {
			$result[] = $item->matchGrammar($grammar);
		}
		return $result;
	}
	
	/**
	* Gets the results that were matched in the grammar matching
	*
	* @return array
	*/
	public function getMatched()
	{
		$result = array();
		foreach ($this->items as $item) {
			$result[] = $item->getMatched();
		}
		return $result;
	}
}