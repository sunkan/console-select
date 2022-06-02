<?php declare(strict_types=1);

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
	 * @return list<string>
	 */
	public function getOptions(): array;

	/**
	 * @return list<string>
	 */
	public function getSelections(): array;

	public function hasSelections(): bool;

	public function isSelected(string $option): bool;

	public function select(string $option): void;
}
