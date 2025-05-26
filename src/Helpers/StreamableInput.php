<?php declare(strict_types=1);

namespace EddIriarte\Console\Helpers;

use Symfony\Component\Console\Input\StreamableInputInterface;

trait StreamableInput
{
	/** @var null|resource */
	protected $inputStream;

	/**
	 * @return resource
	 */
	protected function getInputStream()
	{
		if (empty($this->inputStream) && $this->input instanceof StreamableInputInterface) {
			$this->inputStream = $this->input->getStream() ?: STDIN;
		}
		if ($this->inputStream === null) {
			throw new \RuntimeException('The input stream cannot be retrieved.');
		}

		return $this->inputStream;
	}
}
