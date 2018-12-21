<?hh //strict

namespace adventofcode\four\one;

require __DIR__.'/../vendor/hh_autoload.php';
use namespace HH\Lib\{C, Str, Vec, Dict, Regex, Math};


<<__EntryPoint>>
async function main_four_two(): Awaitable<noreturn> {
	$file_contents = await read_file_4_2("src/input_day_4.txt");
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
			$cur_min_asleep = $minute_asleep[$guard] ?? vec[];
			$minute_asleep[$guard] = Vec\concat(
				$cur_min_asleep,
				Vec\range($sleep_start, $sleep_end - 1),
			);
		}

	}

	$guard_frequent_minute = Dict\map($minute_asleep, $sleep_timing ==> {
		$sleep_histogram = C\reduce(
			$sleep_timing,
			($histogram, $minute) ==> {
				$current_count = $histogram[$minute] ?? 0;
				$histogram[$minute] = $current_count + 1;
				return $histogram;
			},
			dict[],
		);
		//\var_dump($sleep_histogram);
		list($frequent_sleep_minute, $repeat_count) =
			get_max_key_vey_2($sleep_histogram);
		return tuple($frequent_sleep_minute, $repeat_count);
	});


	// \var_dump($guard_frequent_minute);
	$frequent_guard_id = null;
	$frequent_minute = null;
	$max_repeat_time = -1;
	foreach ($guard_frequent_minute as $guard_id => $max_repeat) {
		list($minute, $times) = $max_repeat;
		if ($times > $max_repeat_time) {
			$frequent_minute = $minute;
			$frequent_guard_id = $guard_id;
			$max_repeat_time = $times;
		}
	}
	//list($frequent_guard, $frequent_time) = get_max_key_vey_2($guard_frequent_minute);

	\var_dump($frequent_guard_id, $frequent_minute);
	if ($frequent_guard_id !== null && $frequent_minute !== null) {
		\var_dump($frequent_guard_id * $frequent_minute);
	}
	exit(0);
}

function get_max_key_vey_2(dict<int, int> $d): (int, int) {
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
async function read_file_4_2(string $file_name): Awaitable<string> {
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
