<?hh //strict

namespace adventofcode\two\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace adventofcode\one\two;
use namespace HH\Lib\{C, Str, Vec, Dict};

<<__Entrypoint>>
async function two_main(): Awaitable<noreturn> {
	$file_parsed = await read_file_two("src/input_day_two.txt");
	$lines = Str\split($file_parsed, "\n")
		|> Vec\filter($$, $s ==> Str\length($s) > 0);
	$matches = vec[];
	for ($i = 0; $i < C\count($lines); $i++) {
		for ($j = $i + 1; $j < C\count($lines); $j++) {
			//TODO: Write in a less impertive style. Hurried on this one.
			$first = $lines[$i];
			$second = $lines[$j];
			//I wish I could zip these ararys but in the absence of it lets do this:
			$one = Str\chunk($first);
			$two = Str\chunk($second);
			$diff_count = 0;
			for ($id = 0; $id < C\count($one); $id++) {
				if ($one[$id] !== $two[$id]) {
					$diff_count += 1;
				}
			}
			if ($diff_count == 1) {
				$matches = Vec\concat($matches, vec[$one, $two]);
				$result = "";
				for ($idx = 0; $idx < C\count($one); $idx++) {
					if ($one[$idx] == $two[$idx]) {
						$result .= $one[$idx];
					}
				}
				\var_dump($result);
				exit(0);
			}
		}
	}
	\var_dump($matches);
	exit(0);
}

//TODO: Move this into a utils module.
//TODO: fix major bug where an empty line gets parsed as 0
async function read_file_two(string $file_name): Awaitable<string> {
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
