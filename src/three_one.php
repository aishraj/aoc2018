<?hh //strict

namespace adventofcode\three\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace HH\Lib\{C, Str, Vec, Dict};


<<__Entrypoint>>
async function main_three(): Awaitable<noreturn> {
	$file_contents = await read_file("src/input_day_3_test_1.txt");
	$parsed_items = Str\split($file_contents, "\n")
		|> Vec\filter($$, $item ==> Str\length($item) > 0);
	$counts = dict[];
	Vec\map($parsed_items, ($line) ==> {
		$splits = Str\split($line, "@");
		$rectangle_details = Str\trim($splits[1]) |> Str\split($$, ":");

	});
	//next iterate and collect in counts
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
