<?hh //strict

namespace adventofcode\four\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace HH\Lib\{C, Str, Vec, Dict, Keyset, Regex};

type shiftinfo = shape(
	'timestamp' => string,
	'minute' => int,
	?'guard_id' => int,
	'action' => string,
);

<<__EntryPoint>>
async function main_four_one(): Awaitable<noreturn> {
	$file_contents = await read_file("src/input_day_4.txt");
	$parsed_items = Str\split($file_contents, "\n")
		|> Vec\filter($$, $item ==> Str\length($item) > 0);
	$results = Vec\map($parsed_items, $line ==> {
		$times = Regex\first_match($line, re"/\[(.*?)\]/");
		if ($times !== null) {
			$hourmark = Regex\first_match($times[1], re"/\d{1,2}$/");
			if ($hourmark === null) {
				$hour = 0;
			} else {
				$hour = (int)$hourmark[0];
			}
			$guard_id = Regex\first_match($line, re"/#(\d{1,10})/");
			if ($guard_id === null) {
				$id = null;
			} else {
				$id = (int)$guard_id[0];
			}
			return shape(
				'timestamp' => $times[1],
				'minute' => $hour,
				'guard_id' => $id,
				'action' => $line,
			);
		} else {
			return null;
		}
	})
		|> Vec\filter_nulls($$)
		|> Vec\sort_by($$, $re ==> $re['timestamp']);
	$total_asleep = dict[];
	$minute_asleep = dict[];
	$guard = null;
	$sleep = 0;
	$sleep_start = 0;
	$sleep_end = 0;
	foreach ($results as $item) {
		if ($guard === null || Str\contains($item['action'], "begins shift")) {
			$guard = $item['guard_id'];
			$sleep_start = 0;
			$sleep_end = 0;
			continue;
		}
		if (Str\contains($item['action'], "falls asleep")) {
			$sleep_start = $item['minute'];
		} else if (Str\contains($item['action'], "wakes up")) {
			$sleep_end = $item['minute'];
			$sleep = $sleep_end - $sleep_start + 1;
			$current_asleep = $total_asleep[$guard] ?? 0;
			$total_asleep[$guard] = $current_asleep + $sleep;
			$cur_min_asleep = $minute_asleep[$guard] ?? vec[];
			$minute_asleep[$guard] =
				Vec\concat($cur_min_asleep, [tuple($sleep_start, $sleep_end)]);
		}

	}
	\echo($minute_asleep);
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
