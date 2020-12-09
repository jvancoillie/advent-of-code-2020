<?php

$numbers = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $numbers[] = (int) $f;
    }
} while ($f !== false);

$preambleLength = 25;

$invalid = $xmas = 0;

for($i=0; $i<count($numbers); $i++){
    if($i < $preambleLength){
        continue;
    }

    $preamble = array_slice($numbers, $i-$preambleLength, $preambleLength);

    if(!is_valid($numbers[$i], $preamble)){
        $invalid = $numbers[$i];
        $xmas = xmas($numbers[$i], $numbers);
        break;
    }
}

function is_valid($number, $preamble){

    foreach ($preamble as $n1){
        foreach ($preamble as $n2){
            if($n1 === $n2){
                continue;
            }

            if($n1+$n2 === $number){
                return true;
            }
        }
    }

    return false;
}

function xmas($number, $numbers){
    $list = false;
    for($i=0; $i<count($numbers); $i++){
        $sum = 0;
        for($j=$i; $j<count($numbers); $j++){
            $sum += $numbers[$j];

            if($sum > $number){
                break;
            }

            if($sum === $number){
                $list = array_slice($numbers, $i, $j-$i+1);
                break 2;
            }
        }
    }
    return ($list)?max($list)+min($list):false;
}

error_log(var_export(sprintf('puzzle A invalid number : %d',$invalid), true));
error_log(var_export(sprintf('puzzle B xmas of invalid number: %d',$xmas), true));

