<?php
ini_set("memory_limit", "-1");
$adapters = [];
$adapters[] = 0;

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $adapters[] = (int) $f;
    }
} while ($f !== false);

sort($adapters);

$jolts=[0  => 0, 1  => 0, 2  => 0, 3 => 1];

for($i=0;$i < count($adapters) -1;$i++){
    $diff = $adapters[$i + 1] - $adapters[$i];
    $jolts[$diff]++;
}

$counts = [];
function distinct($i, $adapters){
    global $counts;

    if($i === count($adapters) - 1){
        return 1;
    }

    if(isset($counts[$i])){
        return $counts[$i];
    }

    $count = 0;
    for($j=$i+1;$j < count($adapters);$j++){
        if(($adapters[$j] - $adapters[$i]) < 4){
            $count+= distinct($j, $adapters);
        }
    }
    $counts[$i] = $count;

    return $count;
}

error_log(var_export(sprintf('puzzle A : %d',$jolts[1] * $jolts[3]), true));
error_log(var_export(sprintf('puzzle B : %d',distinct(0, $adapters)), true));

