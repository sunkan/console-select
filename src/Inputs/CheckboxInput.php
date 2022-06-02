<?php declare(strict_types=1);

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Inputs\AbstractSelect;
use EddIriarte\Console\Inputs\Exceptions\UnknownOption;

class CheckboxInput extends AbstractSelect
{
	public function select(string $option): void
	{
		if (empty(array_intersect($this->options, [$option]))) {
			throw new UnknownOption($option);
		}

		if ($this->isSelected($option)) {
			$this->selections = array_values(array_diff($this->selections, [$option]));
		}
		else {
			$this->selections[] = $option;
		}
	}
}
