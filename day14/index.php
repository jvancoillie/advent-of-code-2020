<?php

$input = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {

                if(preg_match('/^mask\s=\s(?P<mask>.*)$/', $f, $matches, PREG_OFFSET_CAPTURE)){
                    $input[] = [ 'mask', $matches['mask'][0]];
                }
                if(preg_match('/^mem\[(?P<index>\d+)\]\s=\s(?P<value>.*)$/', $f, $matches, PREG_OFFSET_CAPTURE)){
                    $input[] = [ (int) $matches['index'][0] , (int) $matches['value'][0] ];
                }
    }
} while ($f !== false);

$mask=null;
$memoryA = [];
$memoryB = [];

foreach($input as [$key, $value]){
    if($key === 'mask'){
        $mask = $value;
        continue;
    }

    $memoryA[$key] = applyMaskValue($value, $mask);

    $comb = applyMaskKey($key, $mask);

    foreach ($comb as $keyB){
        $memoryB[$keyB] = $value;
    }
}

function applyMaskValue($value, $mask){

    $str = str_pad(decbin($value), 36, "0", STR_PAD_LEFT);

    for($i=strlen($str)-1;$i>=0;$i--){
        if($mask[$i] === '0' || $mask[$i] === '1'){
            if($str[$i] !== $mask[$i]){
                $str[$i] = $mask[$i];
            }
        }
    }

    return bindec($str);
}

function applyMaskKey($value, $mask){

    $str = str_pad(decbin($value), 36, "0", STR_PAD_LEFT);

    for($i=strlen($str)-1;$i>=0;$i--){
        if($mask[$i] === '1' || $mask[$i] === 'X'){
                $str[$i] = $mask[$i];
        }
    }

    return combinations($str, strlen($str)-1);
}

function combinations ($str, $index){
        for($i=$index; $i >= 0; $i--){
            if($str[$i] === "X"){
                $tmpA = $str;
                $tmpB = $str;
                $tmpA[$i] = '1';
                $tmpB[$i] = '0';
                    $with1 = combinations($tmpA, $i-1);
                    $with0 = combinations($tmpB, $i-1);
                return array_merge($with0, $with1);
            }
        }

        return [bindec($str)];
}

echo "Puzzle A : ".array_sum($memoryA)."\n";
echo "Puzzle B : ".array_sum($memoryB)."\n";

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}

