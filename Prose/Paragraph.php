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
}