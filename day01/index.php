<?php

$input = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if ($f !== false) {
        $input[] = (int) $f;
    }
} while ($f !== false);


$result = [];
// PUZZLE 1
foreach($input as $first){
    foreach($input as $second){
        if($first + $second === 2020){
            $result['puzzle_1'] = $first*$second;
            break;
        }
    }
}

// PUZZLE 3
foreach($input as $first){
    foreach($input as $second){
        foreach($input as $third){
            if($first + $second + $third === 2020){
                $result['puzzle_2'] = $first*$second*$third;
            }
        }
    }
}

error_log(var_export($result, true));
