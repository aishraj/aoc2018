<?hh // strict

namespace adventofcode\two;

require __DIR__.'/../vendor/hh_autoload.php';

use namespace HH\Lib\C;


<<__Entrypoint>>
async function two(): Awaitable<noreturn> {
  $file_contents = await two_read_file("src/two_input.txt");
  $parsed_items = \explode("\n", $file_contents);

  $s = 0;
  $results = array();
  foreach ($parsed_items as $number_val) {
    $item = (int)$number_val;
    $s += $item;
    \var_dump($results);
    if (C\contains_key($results, $s)) {
      \var_dump($s);
      break;
    } else {
      $results[$s] = 1;
    }
  }
  exit(0);
}

//TODO: Find out why the namespacing is not working.
async function two_read_file(string $file_name): Awaitable<string> {
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
