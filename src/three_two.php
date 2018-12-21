<?hh //strict

namespace adventofcode\three\two;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace HH\Lib\{C, Str, Vec, Dict, Keyset};


<<__EntryPoint>>
async function main_three_two(): Awaitable<noreturn> {
	$file_contents = await read_file("src/input_day_3.txt");
	$parsed_items = Str\split($file_contents, "\n")
		|> Vec\filter($$, $item ==> Str\length($item) > 0);
	$all_ids = Keyset\map($parsed_items, $item ==> {
		$splits = Str\split($item, "@");
		$id = (Str\split($splits[0], "#"))[1] |> Str\trim($$);
		return $id;
	});
	$rets = C\reduce(
		$parsed_items,
		($counts, $line) ==> {
			$splits = Str\split($line, "@");
			$id = (Str\split($splits[0], "#"))[1] |> Str\trim($$);
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
						$counts[$i][$j] = Vec\concat($counts[$i][$j], vec[$id]);
					} else if (C\contains_key($counts, $i)) {
						$current_lookup = $counts[$i];
						$current_lookup[$j] = vec[$id];
						$counts[$i] = $current_lookup;
					} else {
						$counts[$i] = dict[$j => vec[$id]];
					}
				}
			}
			return $counts;
		},
		dict[],
	);
	$duplicate_ids = C\reduce(
		$rets,
		($agg, $lookup) ==> {
			$duplicates = Dict\filter($lookup, $items ==> C\count($items) > 1)
				|> Vec\map($$, $items ==> $items)
				|> Keyset\flatten($$);
			$agg = Keyset\union($agg, $duplicates);
			return $agg;
		},
		keyset[],
	)
		|> Vec\unique($$);
	$unique_ids = Keyset\diff($all_ids, $duplicate_ids);
	\var_dump($unique_ids);
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
