<?php declare(strict_types=1);

namespace EddIriarte\Console\Inputs\Traits;

use EddIriarte\Console\Inputs\Exceptions\IndexOutOfRange;


/**
 * Trait ChunkableOptions
 * @package EddIriarte\Console\Inputs\Traits
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
trait ChunkableOptions
{
	protected array $chunks;

	protected int $chunkSize = 3;

	/**
	 * @return array<int, list<string>>
	 */
	public function getChunks(int $chunkSize = null): array
	{
		if (!is_null($chunkSize)) {
			$this->chunkSize = $chunkSize;
		}

		if (!isset($this->chunks)) {
			$this->chunks = array_chunk($this->getOptions(), $this->chunkSize);
		}

		return $this->chunks;
	}

	/**
	 * @return array<int, list<string>>
	 * @throws IndexOutOfRange
	 */
	public function getChunkAt(int $index): array
	{
		if (!empty($this->getChunks()[$index])) {
			return $this->getChunks()[$index];
		}

		throw new IndexOutOfRange((string)$index);
	}

	public function getChunksCount(): int
	{
		return count($this->getChunks());
	}

	public function hasEntryAt(int $rowIndex, int $colIndex): bool
	{
		$chunks = $this->getChunks();
		return array_key_exists($rowIndex, $chunks) && array_key_exists($colIndex, $chunks[$rowIndex]);
	}

	/**
	 * @throws IndexOutOfRange
	 */
	public function getEntryAt(int $rowIndex, int $colIndex): string
	{
		if ($this->hasEntryAt($rowIndex, $colIndex)) {
			return $this->getChunks()[$rowIndex][$colIndex];
		}

		throw new IndexOutOfRange("{$rowIndex}:{$colIndex}");
	}
}
