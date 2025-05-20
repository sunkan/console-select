<?php declare(strict_types=1);

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Handlers\SelectHandler;
use EddIriarte\Console\Inputs\Traits\ChunkableOptions;
use EddIriarte\Console\Inputs\Interfaces\SelectInput;

abstract class AbstractSelect implements SelectInput
{
	use ChunkableOptions;

	/** @var list<string> */
	protected array $selections;

	/**
	 * @param list<string> $options
	 * @param list<string> $defaultSelection
	 */
	public function __construct(
		protected string $message,
		protected array $options,
		array $defaultSelection = [],
	) {
		$this->selections = $defaultSelection;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @return list<string>
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @return list<string>
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
		return in_array($option, $this->selections, true);
	}

	public function controlMode(): int
	{
		return SelectHandler::DEFAULT_CTR;
	}
}
