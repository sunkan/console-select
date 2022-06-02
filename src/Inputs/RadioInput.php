<?php declare(strict_types=1);

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Inputs\Exceptions\UnknownOption;

class RadioInput extends AbstractSelect
{
	public function select(string $option): void
	{
		if (empty(array_intersect($this->options, [$option]))) {
			throw new UnknownOption($option);
		}

		$this->selections = $this->isSelected($option) ? [] : [$option];
	}
}
