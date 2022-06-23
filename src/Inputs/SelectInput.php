<?php declare(strict_types=1);

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Handlers\SelectHandler;
use EddIriarte\Console\Inputs\Exceptions\UnknownOption;

final class SelectInput extends RadioInput
{
	public function __construct(string $message, array $options)
	{
		if (!$options) {
			throw new \BadMethodCallException('Can\'t create selection without options');
		}

		parent::__construct($message, $options, [$options[0]]);
	}

	public function controlMode(): int
	{
		return SelectHandler::SIMPLE_CTR;
	}

	public function select(string $option): void
	{
		if (empty(array_intersect($this->options, [$option]))) {
			throw new UnknownOption($option);
		}

		$this->selections = $this->isSelected($option) ? [] : [$option];
	}

	public function getFirstSelection(): string
	{
		return $this->selections[0];
	}
}
