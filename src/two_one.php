<?hh //strict

namespace adventofcode\two\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace adventofcode\one\two;
use namespace HH\Lib\{C, Str, Vec, Dict};

<<__EntryPoint>>
async function main(): Awaitable<noreturn> {
	$file_contents = await read_file("src/input_day_two.txt");
	$parsed_items = Str\split($file_contents, "\n")
		|> Vec\filter($$, $item ==> Str\length($item) > 0);
	//Map the file into an array of histograms	
	$counts = Vec\map($parsed_items, function($line_item) {
		$item_chars = Str\chunk($line_item, 1);
		$initial_map = dict[];
		$res = C\reduce(
			$item_chars,
			function($d, $character) {
				if (C\contains_key($d, $character)) {
					$d[$character] += 1;
				} else {
					$d[$character] = 1;
				}
				return $d;
			},
			$initial_map,
		)
			|> Dict\filter($$, $value ==> $value == 2 || $value == 3);
		return $res;
	});
	$two_counts = Vec\filter($counts, $lookup ==> C\contains($lookup, 2));
	$three_counts = Vec\filter($counts, $lookup ==> C\contains($lookup, 3));
	\var_dump(C\count($two_counts) * C\count($three_counts));
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
