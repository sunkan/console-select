<?php declare(strict_types=1);

namespace EddIriarte\Console\Helpers;

use Symfony\Component\Console\Input\StreamableInputInterface;

trait StreamableInput
{
	/** @var resource */
	protected $inputStream;

	/** @return false|resource */
	protected function getInputStream()
	{
		if (empty($this->inputStream) && $this->input instanceof StreamableInputInterface) {
			$this->inputStream = $this->input->getStream() ?: STDIN;
		}

		return $this->inputStream;
	}
}
