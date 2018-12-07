<?hh

namespace adventofcode\two\one;

require __DIR__.'../vendor/hh_autoload.php';
use namespace adventofcode\one\two;
use namespace HH\Lib\{C, Str, Vec, Dict};

<<__Entrypoint>>
async function main(): Awaitable<noreturn> {
	$file_contents = await two\read_file("src/input_day_two.txt");
	$parsed_items = Str\split($file_contents, "\n")
		|> Vec\filter($$, $item ==> Str\length($item) > 0);
	//Map the file into an array of histograms	
	$counts = Vec\map($parsed_items, function($line_item) {
		$item_chars =
			\str_split($line_item); //Could not find a Hack Way of doing this
		if ($item_chars != false) { //Shitty PHP
			$items = Vec\map($item_chars, $letter ==> $letter as string);
			$initial_map = dict['none' => -1]; //added for typing
			$res = C\reduce(
				$items,
				function($item, $d) {
					return dict['none' => 2];
				},
				$initial_map,
			);
		}
		return "";
	});

	exit(0);
}
