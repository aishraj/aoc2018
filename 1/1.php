<?hh // strict

<<__Entrypoint>>
async function main(): Awaitable<noreturn> {
  $file_contents = await read_file("input.txt");
  $parsed_items = explode("\n", $file_contents);

  //I wanted to use the pipe operator and do it using a reduce step
  //But the lack of documentation did not help.
  $s = 0;
  foreach ($parsed_items as $number_val) {
    $item = (int)$number_val;
    $s += $item;
  }
  \var_dump($s);
  exit(0);
}

async function read_file(string $file_name): Awaitable<string> {
  $file_handle = fopen($file_name, "r");
  $result = "";
  if ($file_handle) {
    while (($line = fgets($file_handle)) !== false) {
      $result .= $line;
    }
    fclose($file_handle);
  } else {
    throw new Exception("Unable to open the file for readidng");
  }
  return $result;
}
