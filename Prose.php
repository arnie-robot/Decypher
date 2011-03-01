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
}