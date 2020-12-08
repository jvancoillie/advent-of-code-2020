<?php

$input = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $input[] = explode(' ', $f);
    }
} while ($f !== false);

$accumulatorA = $accumulator = 0;

foreach($input as $key => $action){
    $changed = $input;

    if($action[0] === 'nop'){
        $changed[$key] = ['jmp', $action[1]];
    }
    if($action[0] === 'jmp'){
        $changed[$key] = ['nop', $action[1]];
    }

    $accumulator = 0;
    if(execute($changed, $accumulator)){
        break;
    }

    if($key === 0){
        $accumulatorA = $accumulator;
    }
}

function execute($commands, &$accumulator){
    $isDone = true;
    $done = [];
    for($i=0;$i< count($commands); $i++){
        if(in_array($i, $done)){
            $isDone = false;
            break;
        }

        $done[] = $i;
        $action = $commands[$i][0];
        $nb = (int) substr($commands[$i][1],1);
        $sign = substr($commands[$i][1],0,1);
        switch ($action){
            case 'acc':
                if($sign === '+'){
                    $accumulator += $nb;
                }else{
                    $accumulator -= $nb;
                }
                break;
            case 'jmp':
                if($sign === '+'){
                    $i += ($nb-1);
                }else{
                    $i -= ($nb+1);
                }
                break;
        }
    }

    return $isDone;
}

error_log(var_export(sprintf('puzzle A acc: %d',$accumulatorA), true));
error_log(var_export(sprintf('puzzle B acc: %d',$accumulator), true));
