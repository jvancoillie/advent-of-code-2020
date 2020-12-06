<?php

$input = [];
$group = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        if($f===''){
            $input[] = $group;
            $group = [];
        }else{
            $group[] = str_split($f);
        }
    }
} while ($f !== false);

$input[] = $group;
$answeredA=$answeredB=0;

foreach($input as $group){
    $answeredList = [];
    $uniqueAnsweredList = [];

    foreach($group as $k => $person){
        if($k===0){
            $answeredList = $person;
            $uniqueAnsweredList = $person;
            continue;
        }
        $uniqueAnsweredList = array_merge($uniqueAnsweredList, $person);
        $answeredList = array_intersect($answeredList, $person);
    }
    $answeredA += count(array_unique($uniqueAnsweredList));
    $answeredB += count($answeredList);
}

error_log(var_export(sprintf("Answered A : %d",$answeredA), true));
error_log(var_export(sprintf("Answered B : %d",$answeredB), true));