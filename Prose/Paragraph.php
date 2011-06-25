<?php

/**
* Decypher
*/

/**
* A paragraph of prose, split into sentences
*/
class Prose_Paragraph extends Prose_Component
{
	/**
	* The separator for returning to string
	*
	* @var string
	*/
	protected $separator = "\n";

	/**
	* Splits the original content into sentences
	*
	* @return void
	*/
	protected function itemise()
	{
		$sentences = explode('.', $this->original);
		foreach ($sentences as $sentence) {
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
				$this->items[] = new Prose_Sentence($s);
			}
		}
	}
	
	/**
	* Matches the sentences in this paragraph against the defined grammar, and returns what it finds
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
			$result[] = array('sentence'=>$item, 'grammar'=>$item->getMatched());
		}
		return $result;
	}
}