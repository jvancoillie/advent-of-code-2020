<?php

$timestamp = '';
$busIds = [];
$i=0;
do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        if($i===0){
            $timestamp = (int) $f;
        }else{
            $busIds = explode(',', $f);
        }
        $i++;
    }
} while ($f !== false);

$bestTime = INF;
$bestId = null;
$pairs = [];
foreach($busIds as $key => $id){

    if($id !== 'x'){
        $next = $timestamp + ($id - $timestamp%$id);
        $pairs[] = [$id - $key, $id];
        if($next < $bestTime){
            $bestTime = $next ;
            $bestId = $id * ($next - $timestamp);
        }
    }
}


//https://en.wikipedia.org/wiki/Chinese_remainder_theorem
function crt($pairs)
{
    $m = 1;
    foreach ($pairs as [$x, $mx]) {
        $m *= $mx;
    }
    $total = 0;
    foreach ($pairs as [$x, $mx]) {
        $b = $m / $mx;

        $a = gmp_strval(gmp_mul($x, $b));
        $pow = gmp_strval(gmp_powm($b, $mx - 2, $mx));
        $res = gmp_strval(gmp_mul($a, $pow));

        $total = gmp_strval(gmp_add($total, $res));
        $total = gmp_strval(gmp_mod($total, $m));
    }

    return $total;
}

error_log(var_export(sprintf('puzzle A : %s', $bestId), true));
error_log(var_export(sprintf('puzzle B : %d', crt($pairs)), true));

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}