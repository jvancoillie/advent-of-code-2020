<?php
ini_set("memory_limit", "-1");
$input = [];
do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if ($f !== false) {
        $input[] = str_split($f);
    }
} while ($f !== false);

// reset x - y to center 0
$min = -floor(count($input[0]) / 2);
$grid = [];
foreach ($input as $x => $line) {
    foreach ($line as $y => $state) {
        $grid[$min + $x][$min + $y] = $state;
    }
}

$initialCube = [$grid];

$width = count($initialCube[0]);
$cube = $initialCube;
for ($i = 2; $i <= 12; $i += 2) {
    $width += $i;
    $round = [];
    $start = -floor($width / 2);
    $end = floor($width / 2);
    for ($z = $start; $z <= $end; $z++) {
        for ($x = $start; $x <= $end; $x++) {
            for ($y = $start; $y <= $end; $y++) {
                $round[$z][$x][$y] = toggle3D($z, $x, $y, $cube);
            }
        }
    }

    $cube = $round;
}

echo "Puzzle A : ".countActive3D($cube)."\n";

//--------- part 2
$width = count($initialCube[0]);
$cube = [$initialCube];
for ($i = 2; $i <= 12; $i += 2) {
    $width += $i;
    $round = [];
    $start = -floor($width / 2);
    $end = floor($width / 2);
    for ($z = $start; $z <= $end; $z++) {
        for ($x = $start; $x <= $end; $x++) {
            for ($y = $start; $y <= $end; $y++) {
                for ($w = $start; $w <= $end; $w++) {
                    $round[$w][$z][$x][$y] = toggle4D($w, $z, $x, $y, $cube);
                }
            }
        }
    }

    $cube = $round;
}

echo "Puzzle B: ".countActive4D($cube)."\n";

function toggle3D($z, $x, $y, $cube)
{
    $directions = [
        [-1, -1, -1],
        [-1, -1, 0],
        [-1, -1, 1],
        [-1, 0, -1],
        [-1, 0, 0],
        [-1, 0, 1],
        [-1, 1, -1],
        [-1, 1, 0],
        [-1, 1, 1],

        [0, -1, -1],
        [0, -1, 0],
        [0, -1, 1],
        [0, 0, -1],
        [0, 0, 1],
        [0, 1, -1],
        [0, 1, 0],
        [0, 1, 1],

        [1, -1, -1],
        [1, -1, 0],
        [1, -1, 1],
        [1, 0, -1],
        [1, 0, 0],
        [1, 0, 1],
        [1, 1, -1],
        [1, 1, 0],
        [1, 1, 1],
    ];
    $type = $cube[$z][$x][$y] ?? '.';
    $rules = ['#' => [2, 3], '.' => [3]];
    $activeCount = 0;
    // echo "search 3 from  $z,$y,$x   => $type   \n";

    /**
     * @var int $dx
     * @var int $dy
     */
    foreach ($directions as $key => [$dz, $dx, $dy]) {
        $nz = $z + $dz;
        $nx = $x + $dx;
        $ny = $y + $dy;

        $state = $cube[$nz][$nx][$ny] ?? '.';
        // echo "     :  $nz,$nx,$ny   => $state   \n";
        if ($state === '#') {

            $activeCount++;
        }
    }
   return in_array($activeCount, $rules[$type])?'#':'.';
}

function countActive3D($cube)
{
    $active = 0;
    $start = -floor(count($cube[0]) / 2);
    $end = floor(count($cube[0]) / 2);
    for ($z = $start; $z <= $end; $z++) {
        for ($x = $start; $x <= $end; $x++) {
            for ($y = $start; $y <= $end; $y++) {
                if ($cube[$z][$x][$y] === "#") {
                    $active++;
                }
            }
        }
    }

    return $active;
}

function toggle4D($w, $z, $x, $y, $cube)
{
    $type = $cube[$w][$z][$x][$y] ?? '.';
    $rules = ['#' => [2, 3], '.' => [3]];
    $activeCount = 0;

    for ($nw = $w - 1; $nw <= $w + 1; $nw++) {
        for ($nz = $z - 1; $nz <= $z + 1; $nz++) {
            for ($nx = $x - 1; $nx <= $x + 1; $nx++) {
                for ($ny = $y - 1; $ny <= $y + 1; $ny++) {
                    if ($nw === $w && $nz === $z && $nx === $x && $ny === $y) {
                        continue;
                    }
                    $state = $cube[$nw][$nz][$nx][$ny] ?? '.';
                    if ($state === '#') {
                        $activeCount++;
                    }

                    if($activeCount > 3){
                        return '.';
                    }
                }
            }
        }
    }

    return in_array($activeCount, $rules[$type])?'#':'.';
}

function countActive4D($cube)
{
    $active = 0;
    $start = -floor(count($cube[0]) / 2);
    $end = floor(count($cube[0]) / 2);

    for ($z = $start; $z <= $end; $z++) {
        for ($x = $start; $x <= $end; $x++) {
            for ($y = $start; $y <= $end; $y++) {
                for ($w = $start; $w <= $end; $w++) {
                    if ($cube[$w][$z][$x][$y] === "#") {
                        $active++;
                    }
                }
            }
        }
    }

    return $active;
}

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}

function dumpGrid($array)
{
    foreach ($array as $key => $lines) {
        echo implode('', $lines)."\n";
    }
    echo "\n";
}

