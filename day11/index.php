<?php
ini_set("memory_limit", "-1");
$ferry = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $ferry[] = str_split($f);
    }
} while ($f !== false);

error_log(var_export(sprintf('puzzle A : %d', seatsOccupied($ferry)), true));
error_log(var_export(sprintf('puzzle B : %d', seatsOccupied($ferry, true, 5)), true));

function seatsOccupied($ferry, $forward = false, $numberOfPlacesOccupied = 4)
{
    while (true) {
        $round = $ferry;
        //dump($round);
        for ($x = 0; $x < count($ferry); $x++) {
            for ($y = 0; $y < count($ferry[$x]); $y++) {
                $type = $ferry[$x][$y];
                if ($type !== "." && needChange($x, $y, $ferry, $forward, $numberOfPlacesOccupied)) {
                    $round[$x][$y] = $type === "#" ? "L" : "#";
                }
            }
        }

        if ($ferry == $round) {
            return countOccupied($ferry);
        }

        $ferry = $round;
    }
}

function countOccupied($array)
{
    $total = 0;
    foreach ($array as $key => $lines) {
        $counts = array_count_values($lines);
        $total += $counts['#'] ?? 0;
    }

    return $total;
}

function needChange($x, $y, $ferry, $forwarded = false, $numberOfPlacesOccupied = 4)
{
    $directions = [
        [-1, -1],
        [-1, 0],
        [-1, 1],
        [0, -1],
        [0, 1],
        [1, -1],
        [1, 0],
        [1, 1],
    ];
    $type = $ferry[$x][$y];
    $count = 0;
    /**
     * @var int $dx
     * @var int $dy
     */
    foreach ($directions as $key => [$dx, $dy]) {
        $tryNext = $forwarded;
        $i = 1;
        do {
            $nx = $x + $dx * $i;
            $ny = $y + $dy * $i;
            if (isset($ferry[$nx][$ny])) {
                if ($ferry[$nx][$ny] === ".") {
                    $i++;
                } else {
                    if ($type === "#" && $ferry[$nx][$ny] === $type) {
                        $count++;
                    } else {
                        if ($type === "L") {
                            if ($ferry[$nx][$ny] !== $type) {
                                return false;
                            }
                        }
                    }
                    $tryNext = false;
                }
            } else {
                $tryNext = false;
            }
        } while ($tryNext);

    }

    if ($type === "#" && $count < $numberOfPlacesOccupied) {
        return false;
    }

    return true;
}

function dump($array)
{
    foreach ($array as $key => $lines) {
        error_log(var_export(''.implode('', $lines), true));
    }
}