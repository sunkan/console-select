<?php

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Inputs\Traits\ChunkableOptions;
use EddIriarte\Console\Inputs\Interfaces\SelectInput;


/**
 * Class AbstractSelect
 * @package EddIriarte\Console\Inputs
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
abstract class AbstractSelect implements SelectInput
{
	use ChunkableOptions;

	protected $message;

	protected $options;

	protected $selections;

	/**
	 * @param array $options
	 */
	public function __construct(string $message, array $options)
	{
		$this->message = $message;
		$this->options = $options;
		$this->selections = [];
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @return array
	 */
	public function getSelections(): array
	{
		return $this->selections;
	}

	public function hasSelections(): bool
	{
		return !empty($this->selections);
	}

	public function isSelected(string $option): bool
	{
		return (bool)in_array($option, $this->selections);
	}
}
