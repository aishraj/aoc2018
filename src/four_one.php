<?hh //strict

namespace adventofcode\four\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace HH\Lib\{C, Str, Vec, Dict, Keyset, Regex, Math};

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
				$id = (int)$guard_id[1];
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

	//$guard_ids = Vec\map($results, $item ==> $item['guard_id'] );
	//\var_dump($guard_ids);
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
			$sleep = $sleep_end - $sleep_start;
			$current_asleep = $total_asleep[$guard] ?? 0;
			$total_asleep[$guard] = $current_asleep + $sleep;
			$cur_min_asleep = $minute_asleep[$guard] ?? vec[];
			$minute_asleep[$guard] = Vec\concat(
				$cur_min_asleep,
				Vec\range($sleep_start, $sleep_end - 1),
			);
		}

	}
	list($top_sleepy_guard, $_top_sleep_duration) =
		get_max_key_vey($total_asleep);
	$durations = $minute_asleep[$top_sleepy_guard];
	$top_sleepy_minutes = C\reduce(
		$durations,
		($agg, $item) ==> {
			$current_count = $agg[$item] ?? 0;
			$agg[$item] = $current_count + 1;
			return $agg;
		},
		dict[],
	);
	list($top_sleepy_min, $_repeat_times) =
		get_max_key_vey($top_sleepy_minutes);
	\var_dump($top_sleepy_min);
	\var_dump($top_sleepy_guard);
	\var_dump($top_sleepy_guard * $top_sleepy_min);
	exit(0);
}

function get_max_key_vey(dict<int, int> $d): (int, int) {
	$max_key = -1;
	$max_value = 0;
	foreach ($d as $k => $v) {
		if ($v > $max_value) {
			$max_key = $k;
			$max_value = $v;
		}
	}
	return tuple($max_key, $max_value);
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
