<?php

$input = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $input[] = $f;
    }
} while ($f !== false);

foreach ($input as $suite) {

    $rowRange = [0, 127];
    foreach (str_split(substr($suite, 0, 7)) as $l) {
        $middle = round(($rowRange[1] - $rowRange[0]) / 2);
        if ($l === 'F') {
            $rowRange[1] = $rowRange[1] - $middle;
        } else {
            $rowRange[0] = $rowRange[0] + $middle;
        }
    }
    $colRange = [0, 7];
    foreach (str_split(substr($suite, 7)) as $l) {
        $middle = round(($colRange[1] - $colRange[0]) / 2);

        if ($l === 'L') {
            $colRange[1] = $colRange[1] - $middle;
        } else {
            $colRange[0] = $colRange[0] + $middle;
        }
    }

    $places[] = $rowRange[0] * 8 + $colRange[0];

}
sort($places);
$previous = $myPlace = null;

foreach ($places as $key => $value) {
    if ($key === 0) {
        $previous = $value;
        continue;
    }
    if($value - 1 !== $previous){
        $myPlace = $value-1;
    }
    $previous = $value;
}

error_log(var_export(sprintf("Max place ID: %d",max($places)), true));
error_log(var_export(sprintf(" My place ID: %d",$myPlace), true));