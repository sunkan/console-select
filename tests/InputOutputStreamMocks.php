<?php declare(strict_types=1);

namespace Tests;

use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\StreamOutput;

trait InputOutputStreamMocks
{
	public static function getInputStream($input)
	{
		$stream = fopen('php://memory', 'r+', false);
		fwrite($stream, $input);
		rewind($stream);
		return $stream;
	}

	public function createStreamableInputInterface($stream = null, $interactive = true)
	{
		$mock = $this->getMockBuilder(StreamableInputInterface::class)->getMock();
		$mock
			->method('isInteractive')
			->willReturn($interactive);
		if ($stream) {
			$mock
				->method('getStream')
				->willReturn($stream);
		}

		return $mock;
	}

	public static function createOutputInterface(bool $canWrite = false): StreamOutput
	{
		return new StreamOutput(
			fopen('php://memory', ($canWrite ? 'w+' : 'r+'), false)
		);
	}
}
