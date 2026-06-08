<?php

namespace alcamo\iterator;

use Ds\Set;

class RecursiveDirectoryIteratorWithBlacklist extends \RecursiveDirectoryIterator
{
    private $dirnameBlacklist_; ///< ?Set

    public function __construct(
        string $directory,
        int $flags,
        $dirnameBlacklist = null
    ) {
        parent::__construct($directory, $flags);

        if (isset($dirnameBlacklist)) {
            $this->dirnameBlacklist_ = $dirnameBlacklist instanceof Set
                ? $dirnameBlacklist
                : new Set($dirnameBlacklist);
        }
    }

    public function hasChildren($allowLinks = null)
    {
        return (isset($this->dirnameBlacklist_)
                && $this->isDir()
                && $this->dirnameBlacklist_->contains($this->getBasename()))
            ? false
            : parent::hasChildren($allowLinks);
    }
}
