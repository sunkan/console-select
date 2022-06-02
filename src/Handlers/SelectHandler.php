<?php declare(strict_types=1);

namespace EddIriarte\Console\Handlers;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use EddIriarte\Console\Inputs\Interfaces\SelectInput;

/**
 * Class SelectHandler
 * @package Lazzier\Helpers
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
class SelectHandler
{
	/** @var resource */
	protected $stream;
	protected OutputInterface $output;
	protected SelectInput $question;
	protected int $row;
	protected int $column;
	protected bool $firstRun = false;
	protected int $terminalWidth = 0;

	/**
	 * @param resource $stream
	 */
	public function __construct(SelectInput $question, OutputInterface $output, $stream)
	{
		$this->row = 0;
		$this->column = 0;
		$this->question = $question;
		$this->output = $output;
		$this->stream = $stream;
	}

	/**
	 * Navigates through option items.
	 *
	 * @return array
	 */
	public function handle(): array
	{
		$this->firstRun = true;
		$this->output->writeln(
			'<info>' . $this->question->getMessage() . '</info> [<comment>SPACE=select</>, <comment>ENTER=submit</>]'
		);
		$this->repaint();

		$sttyMode = shell_exec('stty -g');

		// Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
		shell_exec('stty -icanon -echo');

		// Read a keypress
		while (!feof($this->stream)) {
			$char = fread($this->stream, 1);
			if (" " === $char) {
				$this->tryCellSelection();
			}
			elseif ("\033" === $char) {
				$this->tryCellNavigation($char);
			}
			elseif (10 === ord($char)) {
				//TODO handle valid state...
				$this->clear();
				$this->output->write('> ' . join(', ', $this->question->getSelections()));
				$this->output->write($char);
				break;
			}
			$this->repaint();
		}

		// Reset stty so it behaves normally again
		shell_exec(sprintf('stty %s', $sttyMode));

		$this->output->writeln(' ');

		return $this->question->getSelections();
	}

	public function exists(int $row, int $column): bool
	{
		return $this->question->hasEntryAt($row, $column);
	}

	protected function up(): void
	{
		if ($this->exists($this->row - 1, $this->column)) {
			$this->row -= 1;
		}
	}

	protected function down(): void
	{
		if ($this->exists($this->row + 1, $this->column)) {
			$this->row += 1;
		}
	}

	protected function left(): void
	{
		if ($this->exists($this->row, $this->column - 1)) {
			$this->column -= 1;
		}
	}

	protected function right(): void
	{
		if ($this->exists($this->row, $this->column + 1)) {
			$this->column += 1;
		}
	}

	protected function tryCellSelection(): void
	{
		// Try to select cell.
		if ($this->exists($this->row, $this->column)) {
			$option = $this->question->getEntryAt($this->row, $this->column);
			$this->question->select($option);
		}
	}

	protected function tryCellNavigation(string $char): void
	{
		// Did we read an escape sequence?
		$char .= fread($this->stream, 2);
		if (empty($char[2]) || !in_array($char[2], ['A', 'B', 'C', 'D'])) {
			// Input stream was not an arrow key.
			return;
		}

		switch ($char[2]) {
			case 'A': // go up!
				$this->up();
				break;
			case 'B': // go down!
				$this->down();
				break;
			case 'C': // go right!
				$this->right();
				break;
			case 'D': // go left!
				$this->left();
				break;
		}
	}

	public function repaint(): void
	{
		$message = $this->message();
		if (!$this->firstRun) {
			$this->clear();
		}

		$this->firstRun = false;
		$this->output->write($message);
	}

	public function clear(): void
	{
		// Move the cursor to the beginning of the line
		$this->output->write("\x0D");
		// Erase the line
		$this->output->write("\x1B[2K");
		// Erase previous lines
		$lines = $this->question->getChunksCount() - 1;
		if ($lines > 0) {
			$this->output->write(str_repeat("\x1B[1A\x1B[2K", $lines));
		}
	}

	protected function message(): string
	{
		$chunkSize = $this->chunkSize();
		$chunks = $this->question->getChunks($chunkSize);
		$columnSpace = floor(($this->terminalWidth() - ($chunkSize * 5)) / $chunkSize);
		return implode(PHP_EOL, array_map(function ($entries) use ($chunks, $columnSpace) {
			$hasCursor = $this->row === array_search($entries, $chunks, true);
			return $this->makeRow($entries, ($hasCursor ? $this->column : -10), $columnSpace);
		}, $chunks));
	}

	/**
	 * @return mixed
	 */
	protected function makeRow(array $entries, int $activeColumn, int $columnSpace)
	{
		return array_reduce($entries, function ($carry, $item) use ($entries, $activeColumn, $columnSpace) {
			$isActive = $activeColumn === array_search($item, $entries, true);
			return $carry . $this->makeCell($item, $isActive, $columnSpace);
		}, '');
	}

	protected function makeCell(string $option, bool $hasCursor = false, int $maxWidth = 20): string
	{
		$selected = $this->question->isSelected($option);
		$name = substr($option, 0, ($maxWidth - 1));

		return sprintf(
			$hasCursor ? ' <hl> %1$s %2$s </hl>' : ' <%3$s> %1$s %2$s </%3$s>',
			($selected ? '●' : '○'),
			$name . str_repeat(' ', $maxWidth - mb_strlen($name)),
			($selected ? 'info' : 'comment')
		);
	}

	public function terminalWidth(): int
	{
		return (new Terminal)->getWidth();
	}

	public function chunkSize(): int
	{
		$max = $this->terminalWidth();
		$largest = array_reduce($this->question->getOptions(), 'max', 0);

		if ($largest > ($max / 2)) {
			return 1;
		}

		if ($largest > ($max / 3)) {
			return 2;
		}

		return 3;
	}
}
