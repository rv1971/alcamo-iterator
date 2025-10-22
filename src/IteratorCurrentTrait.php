<?php

namespace alcamo\iterator;

/**
 * @namespace alcamo::iterator
 *
 * @brief Iterator-related stuff
 */

/**
 * @brief Provide part of the Iterator interface by accessing class properties
 * $currentKey_ and $current_.
 *
 * @attention Derived classes must implement the methods rewind() and next().
 *
 * @sa [Iterator interface](https://www.php.net/manual/en/class.iterator)
 *
 * @date Last reviewed 2025-10-22
 */
trait IteratorCurrentTrait
{
    private $currentKey_; ///< Key of current element
    private $current_;    ///< Current element

    public function current()
    {
        return $this->current_;
    }

    public function key()
    {
        return $this->currentKey_;
    }

    public function valid(): bool
    {
        return isset($this->current_);
    }

    /// Call rewind() and return current(), just as PHP's built-in reset()
    public function reset()
    {
        $this->rewind();

        return $this->current_ ?? false;
    }
}
