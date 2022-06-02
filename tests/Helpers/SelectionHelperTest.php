<?php

namespace Tests\Helpers;

use EddIriarte\Console\Helpers\SelectionHelper;
use EddIriarte\Console\Inputs\CheckboxInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\InputOutputStreamMocks;
use Tests\Key;

class SelectionHelperTest extends TestCase
{
	use InputOutputStreamMocks;

	/**
	 * @test
	 */
	public function it_initializes_output_styles(): void
	{
		$hasStyle = false;
		$formatter = $this->getMockBuilder(OutputFormatterInterface::class)
			->getMock();
		$formatter->method('hasStyle')->willReturn($hasStyle);
		$formatter->method('setStyle')
			->willReturnCallback(function ($name, $style) use (&$hasStyle) {
				$hasStyle = $name === 'hl';
			});

		$input = $this->getMockBuilder(InputInterface::class)->getMock();
		$output = $this->getMockBuilder(OutputInterface::class)->getMock();
		$output->method('isDecorated')->willReturn(true);
		$output->method('getFormatter')->willReturn($formatter);

		$helper = new SelectionHelper($input, $output);
		$this->assertTrue($hasStyle, 'Style was not initialized');
	}

	/**
	 * @test
	 */
	public function it_gets_name(): void
	{
		$formatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
		$formatter->method('hasStyle')->willReturn(true);
		$input = $this->getMockBuilder(InputInterface::class)->getMock();
		$output = $this->getMockBuilder(OutputInterface::class)->getMock();
		$output->method('isDecorated')->willReturn(true);
		$output->method('getFormatter')->willReturn($formatter);

		$helper = new SelectionHelper($input, $output);

		$name = $helper->getName();
		$this->assertEquals('selection', $name);
	}

	/**
	 * @test
	 */
	public function it_manipulates_helpersets(): void
	{
		$formatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
		$formatter->method('hasStyle')->willReturn(true);
		$input = $this->getMockBuilder(InputInterface::class)->getMock();
		$output = $this->getMockBuilder(OutputInterface::class)->getMock();
		$output->method('isDecorated')->willReturn(true);
		$output->method('getFormatter')->willReturn($formatter);

		$helper = new SelectionHelper($input, $output);
		$this->assertEmpty($helper->getHelperSet(), 'HelperSet already exists!');

		$helper->setHelperSet(new HelperSet());
		$this->assertNotEmpty($helper->getHelperSet(), "HelperSet isn't  set!");
	}

	/**
	 * @test
	 */
	public function it_triggers_selection(): void
	{
		$stream = $this->getInputStream(Key::RIGHT . Key::SELECT);
		$input = $this->createStreamableInputInterface($stream);
		$output = $this->createOutputInterface();

		$helper = new SelectionHelper($input, $output);

		$question = new CheckboxInput('Select one', ['one', 'two', 'three']);
		[$response] = $helper->select($question);

		$this->assertEquals('two', $response);
	}
}
