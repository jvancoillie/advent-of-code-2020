<?php

$input = [];
$graph = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        [$currentBag, $contains] = explode(' contain ', $f);
        $currentBag = trim(str_replace(['bags', 'bag'],'', $currentBag));
        $contains = str_replace(['bags', 'bag'],'', $contains);
        $list = explode(', ', substr($contains,0,-1));
        foreach($list as $l){
            if(trim($l) !== 'no other'){
                $q = trim(substr($l, 0, 1));
                $bag = trim(substr($l, 1));
                $input[$currentBag][$bag] = $q;
                $graph[$bag][] = $currentBag;
            }
        }


    }
} while ($f !== false);

$goldBag = 'shiny gold';
$colors = [];

$path = bfs($graph, $goldBag);
$cost = cost($input, $goldBag);


function bfs($graph, $start) {
    $queue = new SplQueue();
    # Enqueue the path
    $queue->enqueue($start);
    $visited = [$start];
    $path=[];
    while ($queue->count() > 0) {
        $node = $queue->dequeue();
        if(isset($graph[$node])){
            foreach ($graph[$node] as $neighbour) {
                if (!in_array($neighbour, $visited)) {
                    $visited[] = $neighbour;

                    # Build new path appending the neighbour then and enqueue it
                    $path[] = $neighbour;

                    $queue->enqueue($neighbour);
                }
            };
        }

    }

    return $path;
}

function cost($graph, $node) {
    $cost = 1;
    if(isset($graph[$node])){
        foreach ($graph[$node] as $neighbor => $q){
            $cost +=  $q * cost($graph, $neighbor);
        }
        return $cost;
    }else{
        return 1;
    }

}

error_log(var_export(sprintf('puzzle A : %d',count($path)), true));
error_log(var_export(sprintf('puzzle B : %d',$cost-1), true));