<?php

namespace Tests\Handlers;

use EddIriarte\Console\Handlers\SelectHandler;
use EddIriarte\Console\Inputs\CheckboxInput;
use EddIriarte\Console\Inputs\RadioInput;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\InputOutputStreamMocks;
use Tests\Key;

class SelectHandlerTest extends TestCase
{
	use InputOutputStreamMocks;

	#[Test]
	public function it_handles_radio_inputs(): void
	{
		$question = new RadioInput('Select one', ['one', 'two', 'three']);
		$output = $this->createOutputInterface();
		$stream = $this->getInputStream(
			Key::RIGHT .
			Key::RIGHT .
			Key::SELECT .
			Key::LEFT .
			Key::SELECT .
			Key::SUBMIT
		);

		$handler = new SelectHandler($question, $output, $stream);

		$handler->handle();

		$selections = $question->getSelections();
		$this->assertCount(1, $selections);
		$this->assertEquals('two', $selections[0]);
	}

	#[Test]
	public function it_handles_checkboxes_inputs(): void
	{
		$question = new CheckboxInput('Select one', ['one', 'two', 'three']);
		$output = $this->createOutputInterface();
		$stream = $this->getInputStream(
			Key::RIGHT .
			Key::RIGHT .
			Key::SELECT .
			Key::LEFT .
			Key::SELECT .
			Key::SUBMIT
		);

		$handler = new SelectHandler($question, $output, $stream);

		$handler->handle();

		$selections = $question->getSelections();
		$this->assertCount(2, $selections);
		$this->assertEquals('three', $selections[0]);
		$this->assertEquals('two', $selections[1]);
	}

	#[Test]
	public function it_handles_selection_toggle(): void
	{
		$question = new CheckboxInput('Select one', ['one', 'two', 'three']);
		$output = $this->createOutputInterface();
		$stream = $this->getInputStream(
			Key::RIGHT .
			Key::RIGHT .
			Key::SELECT .
			Key::LEFT .
			Key::SELECT .
			Key::RIGHT .
			Key::SELECT .
			Key::SUBMIT
		);

		$handler = new SelectHandler($question, $output, $stream);

		$handler->handle();

		$selections = $question->getSelections();
		$this->assertCount(1, $selections);
		$this->assertEquals('two', $selections[0]);
	}

	#[Test]
	#[DataProvider('provideExistenceCheckers')]
	public function it_checks_option_existence($handler, $row, $column, $expected): void
	{
		$exists = $handler->exists($row, $column);

		$this->assertEquals($expected, $exists);
	}

	/**
	 * @return array<array-key, array{SelectHandler, int, int, bool}>
	 */
	public static function provideExistenceCheckers(): array
	{
		$question = new CheckboxInput('Select one', [
			'one', 'two', 'three',
			'four', 'five', 'six',
			'seven',
		]);
		$output = self::createOutputInterface();
		$stream = self::getInputStream("");

		$handler = new SelectHandler($question, $output, $stream);

		return [
			[$handler, 0, 0, true],
			[$handler, 3, 0, false],
			[$handler, 1, 1, true],
			[$handler, 0, 3, false],
			[$handler, 2, 2, false],
		];
	}

	#[Test]
	public function it_clears_checkbox_output(): void
	{
		$question = new CheckboxInput('Select an item', [
			'one', 'two', 'three',
		]);

		$buffer = new TestConsoleBuffer;

		$output = $this->getMockBuilder(OutputInterface::class)->getMock();
		$output
			->method('write')
			->willReturnCallback(\Closure::fromCallable([$buffer, 'write']));

		$output
			->method('writeln')
			->willReturnCallback(\Closure::fromCallable([$buffer, 'writeln']));

		$stream = $this->getInputStream(Key::RIGHT);
		$handler = new SelectHandler($question, $output, $stream);

		$handler->repaint();
		$this->assertCount(1, $buffer->getLines());
		$before = $buffer->getLines()[0];

		$handler->clear();
		$this->assertCount(1, $buffer->getLines());
		$after = $buffer->getLines()[0];

		$this->assertNotEquals($before, $after);
	}

	#[Test]
	public function it_can_navigate_down(): void
	{
		$question = new CheckboxInput('Select an item', [
			'one', 'two', 'three', 'four', 'five', 'six',
		]);

		$buffer = new TestConsoleBuffer;

		$output = $this->getMockBuilder(OutputInterface::class)->getMock();
		$output
			->method('write')
			->willReturnCallback(\Closure::fromCallable([$buffer, 'write']));

		$output
			->method('writeln')
			->willReturnCallback(\Closure::fromCallable([$buffer, 'writeln']));

		$stream = $this->getInputStream(
			Key::DOWN .
			Key::SELECT
		);
		$handler = new SelectHandler($question, $output, $stream);
		$handler->handle();

		[$selection] = $question->getSelections();
		$this->assertEquals('five', $selection);
	}

	#[Test]
	public function it_can_navigate_up(): void
	{
		$question = new CheckboxInput('Select an item', [
			'one', 'two', 'three', 'four', 'five', 'six',
		]);

		$buffer = new TestConsoleBuffer;

		$output = $this->getMockBuilder(OutputInterface::class)->getMock();
		$output
			->method('write')
			->willReturnCallback(\Closure::fromCallable([$buffer, 'write']));

		$output
			->method('writeln')
			->willReturnCallback(\Closure::fromCallable([$buffer, 'writeln']));

		$stream = $this->getInputStream(
			Key::DOWN .
			Key::RIGHT .
			Key::UP .
			Key::SELECT
		);
		$handler = new SelectHandler($question, $output, $stream);
		$handler->handle();

		[$selection] = $question->getSelections();
		$this->assertEquals('two', $selection);
	}
}

class TestConsoleBuffer
{
	protected $buffer = "";

	public function write($msg, $level)
	{
		$this->buffer .= $msg;
	}

	public function writeln($msg, $level)
	{
		$this->write($msg . PHP_EOL, $level);
	}

	public function getLines()
	{
		return explode(PHP_EOL, $this->buffer);
	}
}
