<?hh // strict

namespace adventofcode\one\two;

require __DIR__.'/../vendor/hh_autoload.php';

use namespace HH\Lib\{C, Str, Vec};


<<__EntryPoint>>
async function two(): Awaitable<noreturn> {
  $file_contents = await read_file("src/two_input.txt");
  $parsed_items =
    Str\split($file_contents, "\n") |> Vec\map($$, $item ==> (int)$item);
  $seen = keyset[0];
  $current_index = 0;
  while (true) {
    $change = $parsed_items[$current_index];
    $total_elements_seen = C\count($seen) - 1;
    $last_element = Vec\drop($seen, $total_elements_seen)[0];
    \var_dump("last_element is", $last_element);
    \var_dump("parsed item is", $parsed_items[$current_index]);
    $s = $last_element + $parsed_items[$current_index];
    if (C\contains($seen, $s)) {
      \var_dump($seen);
      \var_dump($s);
      break;
    }
    \array_push(&$seen, $s);
    $current_index = ($current_index + 1) % C\count($parsed_items);
  }
  exit(0);
}

//TODO: Move this into a utils module.
//TODO: fix major bug where an empty line gets parsed as 0
async function read_file(string $file_name): Awaitable<string> {
  $file_handle = \fopen($file_name, "r");
  $result = "";
  if ($file_handle) {
    while (($line = \fgets($file_handle)) !== false) {
      $result .= $line;
    }
    \fclose($file_handle);
  } else {
    throw new \Exception("Unable to open the file for reading");
  }
  return $result;
}
