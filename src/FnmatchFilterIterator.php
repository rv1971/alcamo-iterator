<?php

namespace alcamo\iterator;

/**
 * @brief Filter an iterator with a pattern as used in fnmatch()
 *
 * @note
 * - This works only on platforms where fnmatch() is available.
 * - Patterns containing path components are not supported.
 *
 * The iterator works correctly only in the following situations:
 * - The keys of the inner iterator are filenames without path, and
 * alcamo::iterator::FnmatchFilterIterator::FILTER_KEY is set in the flags.
 * - The inner iterator is a FilesystemIterator with the KEY_AS_FILENAME flag.
 * - The values of the inner iterator are either filenames or SplFileInfo
 * objects or DirectoryIterator objects.
 */
class FnmatchFilterIterator extends \FilterIterator
{
    /// Apply filter to key() rather than to current()
    public const FILTER_KEY = 0x8000;

    private $pattern_;      ///< string
    private $flags_;        ///< int
    private $fnmatchFlags_; ///< int

    /**
     * @param $iterator Iterator to filter.
     *
     * @param $pattern Pattern accepted by fnmatch().
     *
     * @param $flags OR-Combination of fnmatch() flags and the above class
     * constants.
     */
    public function __construct(
        \Iterator $iterator,
        string $pattern,
        ?int $flags = null
    ) {
        parent::__construct($iterator);

        $this->pattern_ = $pattern;

        switch (true) {
            case isset($flags):
                $this->flags_ = $flags;
                break;

                /** If $flags is not given and the underlying iterator is a
                 *  FilesystemIterator having the KEY_AS_FILENAME flag, then
                 *  set ref $flags_ to FILTER_KEY. */
            case $iterator instanceof \FilesystemIterator
                && ($iterator->getFlags()
                    & \FilesystemIterator::KEY_AS_FILENAME):
                $this->flags_ = self::FILTER_KEY;
                break;

            default:
                $this->flags_ = 0;
        }

        $this->fnmatchFlags_ = $this->flags_
            & (FNM_NOESCAPE | FNM_PATHNAME | FNM_PERIOD | FNM_CASEFOLD);
    }

    public function getPattern(): string
    {
        return $this->pattern_;
    }

    public function getFlags(): int
    {
        return $this->flags_;
    }

    public function accept(): bool
    {
        if ($this->flags_ & self::FILTER_KEY) {
            return fnmatch($this->pattern_, $this->key(), $this->fnmatchFlags_);
        } else {
            $current = $this->current();

            return fnmatch(
                $this->pattern_,
                ($current instanceof \SplFileInfo
                 || $current instanceof \DirectoryIterator)
                ? $current->getFilename()
                : $current,
                $this->fnmatchFlags_
            );
        }
    }
}
