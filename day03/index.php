<?php

$input = [];
$height = -1;
do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $input[] = str_split($f);
        $height++;
    }
} while ($f !== false);

$width = count($input[0]) - 1;

error_log(var_export(sprintf("width: %d, height: %d", $width, $height)));


$slopes = [
      ['x' => 1, 'y' => 1],
      ['x' => 1, 'y' => 3],
      ['x' => 1, 'y' => 5],
      ['x' => 1, 'y' => 7],
      ['x' => 2, 'y' => 1],
];
$total = 1;
foreach($slopes as $slope){
    $x=$y=0;
    $isOut = false;
    $treeCount = 0;
    while(!$isOut){
        $x+=$slope['x'];
        $y+=$slope['y'];
        //error_log(var_export("$x-$y", true));

        if($y > $width){
            $y = ($y - $width) -1;
        }
        if($input[$x][$y] === "#"){
            $treeCount++;
        }

        if($x >= $height){
            $isOut = true;
        }
    }
    error_log(var_export($treeCount, true));
    $total *= $treeCount;
}

//foreach($input as $k => $line){
//error_log(sprintf("%d: %s", $k, implode('', $line)));
//}
//error_log(var_export($input[0], true));
error_log(var_export($total, true));

