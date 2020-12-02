<?php

$input = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if ($f !== false) {
        // [0 => range, 1 => letter, 2 => password ]
        $input[] = explode(' ', $f);
    }
} while ($f !== false);

// error_log(var_export($input, true));
$validPuzzle1Password = 0;
$validPuzzle2Password = 0;

foreach( $input as [$range, $letter, $password]){
    [$min, $max] = explode('-', $range);
    $char = substr($letter, 0, -1);

    // puzzle 1
    $count = substr_count($password, $char);

    if($count >= $min && $count <= $max){
        // this is a correct password
        $validPuzzle1Password++;
    }

    // puzzle 2
    if(
        ($password[$min-1] === $char && $password[$max-1] !== $char) ||
        ($password[$min-1] !== $char && $password[$max-1] === $char)
    ){
        $validPuzzle2Password++;
    }
}

error_log(var_export(sprintf('valid puzzle 1 : %d',$validPuzzle1Password), true));
error_log(var_export(sprintf('valid puzzle 2 : %d',$validPuzzle2Password), true));
