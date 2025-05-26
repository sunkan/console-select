<?php declare(strict_types=1);

namespace Tests\Inputs;

use EddIriarte\Console\Inputs\Exceptions\IndexOutOfRange;
use EddIriarte\Console\Inputs\Traits\ChunkableOptions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ChunkableOptionsTest extends TestCase
{
	/**
	 * @test
	 * @dataProvider provideChunkSizes
	 */
	public function it_gets_chunks($options, $chunkSize, $expectedCount): void
	{
		$trait = $this->getMockForTrait(
			ChunkableOptions::class,
			[],
			'',
			true,
			true,
			true,
			['getOptions']
		);
		$trait->method('getOptions')->willReturn($options);

		$count = $trait->getChunks($chunkSize);

		$this->assertCount($expectedCount, $count);
	}

	/**
	 * @return array<array-key, array{list<string>, ?int, int}>
	 */
	public function provideChunkSizes(): array
	{
		return [
			[
				['a', 'b', 'c', 'd', 'e', 'f'],
				null,
				2,
			],
			[
				['a', 'b', 'c', 'd', 'e', 'f'],
				2,
				3,
			],
			[
				['a', 'b', 'c', 'd', 'e', 'f'],
				1,
				6,
			],
		];
	}

	/**
	 * @test
	 */
	public function it_gets_chunk_at(): void
	{
		$trait = $this->getMockForTrait(
			ChunkableOptions::class,
			[],
			'',
			true,
			true,
			true,
			['getOptions']
		);
		$trait->method('getOptions')
			->willReturn(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h']);

		$chunk = $trait->getChunkAt(0);
		$this->assertCount(3, $chunk);

		$chunk = $trait->getChunkAt(1);
		$this->assertCount(3, $chunk);

		$chunk = $trait->getChunkAt(2);
		$this->assertCount(2, $chunk);
		$this->assertEquals('g', $chunk[0]);
		$this->assertEquals('h', $chunk[1]);
	}

	#[Test]
	public function it_throws_exception_by_wrong_chunk_index()
	{
		$trait = $this->getMockForTrait(
			ChunkableOptions::class,
			[],
			'',
			true,
			true,
			true,
			['getOptions']
		);
		$trait->method('getOptions')
			->willReturn(['a', 'b', 'c', 'd']);

		$this->expectException(IndexOutOfRange::class);

		$chunk = $trait->getChunkAt(3);
	}

	/**
	 * @test
	 */
	public function it_gets_chunks_count()
	{
		$trait = $this->getMockForTrait(
			ChunkableOptions::class,
			[],
			'',
			true,
			true,
			true,
			['getOptions']
		);
		$trait->method('getOptions')
			->willReturn(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h']);

		$chunkCount = $trait->getChunksCount();
		$this->assertEquals(3, $chunkCount);
	}

	#[Test]
	public function it_throws_exception_by_wrong_entry_index()
	{
		$trait = $this->getMockForTrait(
			ChunkableOptions::class,
			[],
			'',
			true,
			true,
			true,
			['getOptions']
		);
		$trait->method('getOptions')->willReturn(['a', 'b', 'c', 'd']);

		$this->expectException(\EddIriarte\Console\Inputs\Exceptions\IndexOutOfRange::class);

		$entry = $trait->getEntryAt(2, 4);
	}
}
