<?hh //strict

namespace adventofcode\three\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace HH\Lib\{C, Str, Vec, Dict};


<<__EntryPoint>>
async function main_three(): Awaitable<noreturn> {
	$file_contents = await read_file("src/input_day_3.txt");
	$parsed_items = Str\split($file_contents, "\n")
		|> Vec\filter($$, $item ==> Str\length($item) > 0);
	$rets = C\reduce(
		$parsed_items,
		($counts, $line) ==> {
			$splits = Str\split($line, "@");
			$rectangle_details = Str\trim($splits[1]) |> Str\split($$, ":");
			$positions = Str\split($rectangle_details[0], ",")
				|> Vec\map($$, $p ==> Str\trim($p))
				|> Vec\map($$, ($position) ==> {
					return Str\to_int($position);
				})
				|> Vec\filter_nulls($$);
			$sizes = Str\split($rectangle_details[1], "x")
				|> Vec\map($$, $size ==> Str\trim($size))
				|> Vec\map($$, $size ==> Str\to_int($size))
				|> Vec\filter_nulls($$);
			for ($i = $positions[1]; $i < $positions[1] + $sizes[1]; $i++) {
				//\var_dump($counts);
				for ($j = $positions[0]; $j < $positions[0] + $sizes[0]; $j++) {
					if (
						C\contains_key($counts, $i) &&
						C\contains_key($counts[$i], $j)
					) {
						$counts[$i][$j] += 1;
					} else if (C\contains_key($counts, $i)) {
						$current_lookup = $counts[$i];
						$current_lookup[$j] = 1;
						$counts[$i] = $current_lookup;
					} else {
						$counts[$i] = dict[$j => 1];
					}
				}
			}
			return $counts;
		},
		dict[],
	);
	$double_written_count = C\reduce(
		$rets,
		($dc, $lookup) ==> {
			$greater_than_one = Dict\filter($lookup, $value ==> $value > 1)
				|> C\count($$);
			$dc += $greater_than_one;
			return $dc;
		},
		0,
	);
	\var_dump($double_written_count);
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
