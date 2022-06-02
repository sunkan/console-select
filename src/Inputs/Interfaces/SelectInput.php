<?php

namespace EddIriarte\Console\Inputs\Interfaces;

/**
 * Interface SelectInput
 * @package EddIriarte\Console\Inputs\Interfaces
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
interface SelectInput
{
	public function getMessage(): string;

	/**
	 * @return array
	 */
	public function getOptions(): array;

	/**
	 * @return array
	 */
	public function getSelections(): array;

	public function hasSelections(): bool;

	public function isSelected(string $option): bool;

	public function select(string $option): void;
}
