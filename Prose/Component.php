<?php

/**
* Decypher
*/

/**
* A base class for prose components
*/
abstract class Prose_Component implements Iterator
{
	/**
	* A list of items in this prose component
	*
	* @var array
	*/
	protected $items = array();
	
	/**
	* The original content of this component
	*
	* @var string
	*/
	protected $original;
	
	/**
	* The separator for returning to string
	*
	* @var string
	*/
	protected $separator = ' ';
	
	/**
	* Constructs the component
	*
	* @param string $prose
	*
	* @return Prose_Component
	*/
	final public function __construct($prose)
	{
		$this->original = $prose;
		$this->reset();
		$this->itemise();
	}
	
	/**
	* Resets the class to empty
	*
	* @return void
	*/
	final protected function reset()
	{
		$this->items = array();
	}
	
	/**
	* Outputs this object to a string
	*/
	final public function __toString()
	{
		return implode($this->separator, $this->items);
	}
	
	/**
	* Itemises the original text into the items array
	*
	* @return void
	*/
	abstract protected function itemise();
	
	/**
	* Iterator position
	*
	* @var int
	*/
	private $pos = 0;
	
	/**
	* Iterator::rewind
	*/
	public function rewind()
	{
		$this->pos = 0;
	}
	
	/**
	* Iterator::current
	*/
	public function current()
	{
		return $this->items[$this->pos];
	}
	
	/**
	* Iterator::key
	*/
	public function key()
	{
		return $this->pos;
	}
	
	/**
	* Iterator::next
	*/
	public function next()
	{
		$this->pos++;
	}
	
	/**
	* Iterator::valid
	*/
	public function valid()
	{
		return isset($this->items[$this->pos]);
	}
}