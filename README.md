# Usage example

~~~
use alcamo\iterator\InputStreamLineIterator;

$iterator = new InputStreamLineIterator(fopen(__FILE__, 'r'));

foreach ($iterator as $line => $data) {
    printf("%3d %s\n", $line, $data);
}
~~~

This will output its own source file with line numbers.

# Provided classes and traits

* `IteratorCurrentTrait` provides a partial implementation of the
  `Iterator` interface so that a class using this trait just needs to
  implement the methods `rewind()` and `next()`.
* `InputStreamLineIterator` is an iterator class that reads lines from
  an input stream. Flags can be given e.g. to skip empty
  lines. Derived classes can easily add other features such as
  skipping comment lines.
* `FnmatchFilterIterator` is a filter iterator that filters filenames
  using `fnmatch()`.
