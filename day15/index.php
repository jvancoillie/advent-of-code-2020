<?php
ini_set("memory_limit", "-1");
$numbers = explode(',', stream_get_line(STDIN, 10000, PHP_EOL));

echo "Puzzle A : ".play($numbers, 2020)."\n";
echo "Puzzle B : ".play($numbers, 30000000)."\n";

function play($numbers, $turn)
{
    foreach ($numbers as $key => $num) {
        $played[(int)$num] = $key + 1 ;
    }

    $nextValue = 0;

    for($i=count($numbers) + 1 ; $i<$turn; $i++ ){
        if(isset($played[$nextValue])){
            $offset = $i - $played[$nextValue];
            $played[$nextValue] = $i;
            $nextValue = $offset;
        }else{
            $played[$nextValue] = $i;
            $nextValue = 0;
        }
    }

    return $nextValue;
}


function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}

