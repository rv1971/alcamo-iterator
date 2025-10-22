<?php

namespace alcamo\iterator;

use alcamo\exception\Unsupported;

/**
 * @brief Iterator reading lines from a file pointer
 *
 * key() returns the current line number, starting at 1. If the `SKIP_EMPTY`
 * flag is used, empty lines are skipped and are not counted in the line
 * numbering.
 *
 * @date Last reviewed 2025-10-22
 */
class InputStreamLineIterator implements \Iterator
{
    use IteratorCurrentTrait;

    /// Whether to include the line delimiter into the result of current()
    public const INCLUDE_LINE_DELIMITER = 1;

    /// Whether to skip empty lines
    public const SKIP_EMPTY = 2;

    private $stream_; ///< file pointer
    private $flags_;  ///< OR-combination of the above constants

    /**
     * @param $stream File pointer
     *
     * @param $flags Bitwise or of the above class constants
     */
    public function __construct($stream, ?int $flags = null)
    {
        $this->stream_ = $stream;
        $this->flags_ = (int)$flags;

        /** Call readline() to read the first item. */
        $this->currentKey_ = 1;
        $this->current_ = $this->readLine();
    }

    public function getFlags(): int
    {
        return $this->flags_;
    }

    public function rewind(): void
    {
        if ($this->currentKey_ > 1) {
            /** @throw alcamo::exception::Unsupported when attempting to
             *  rewind, except if the iterator is still at the beginning. */
            throw (new Unsupported())
                ->setMessageContext(
                    [
                        'feature' => 'rewind()',
                        'atLine' => $this->currentKey_
                    ]
                );
        }
    }

    public function next()
    {
        if (isset($this->current_)) {
            $this->currentKey_++;
            $this->current_ = $this->readLine();
        }
    }

    /**
     * @brief Read a line, if possible
     *
     * @return The line read, or `null` if eof or any other low-level error
     */
    protected function readLine(): ?string
    {
        $line = fgets($this->stream_);

        if ($line === false) {
            return null;
        } else {
            if (
                $this->flags_ & self::SKIP_EMPTY && rtrim($line, PHP_EOL) == ''
            ) {
                return $this->readLine();
            }

            if (!($this->flags_ & self::INCLUDE_LINE_DELIMITER)) {
                $line = rtrim($line, PHP_EOL);
            }

            return $line;
        }
    }
}
