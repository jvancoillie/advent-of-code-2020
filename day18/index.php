<?php

$input = [];
do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if ($f !== false) {
        $input[] = $f;
    }
} while ($f !== false);


$calculator = new Calculator();
$totalA = 0;
$totalB = 0;
foreach ($input as $line) {
    $totalA += $calculator->eval($line);
    $t = $calculator->eval($line, true);
    $totalB += $t;
}
echo "Puzzle A: $totalA \n";
echo "Puzzle A: $totalB \n";

class Calculator
{
    private $priorityOperator = "+";
    private $withPriority = false;

    public function eval($line, $withPriority = false)
    {
        $this->withPriority  = $withPriority;
        $parsed = $this->parse($line);
        //echo json_encode($parsed)."\n";

        return $this->calculate($parsed);

    }

    public function calculate($array)
    {
        $tmp = null;
        $operator = null;
        $list = [];
        foreach ($array as $entry) {
            if (is_array($entry)) {
                $list[] = $this->calculate($entry);
            } else {
                if (is_numeric($entry)) {
                    $list[] = $entry;
                } else {
                    $list[] = $entry;
                }
            }
        }
        while (count($list) !== 1) {
            $skip = $this->hasPriority($list);
            for ($i = 0; $i < count($list); $i++) {
                if (!is_numeric($list[$i])) {
                    $done = false;
                    $prev = $list[$i - 1];
                    $next = $list[$i + 1];
                    if ($list[$i] === '+') {
                        $res = $prev + $next;
                        $done = true;
                    } elseif (!$skip) {
                        $res = $prev * $next;
                        $done = true;
                    }
                    if($done){
                        $tmp = array_slice($list, 0,$i -1);
                        $tmp[] = $res;
                        foreach(array_slice($list, $i + 2) as $t){
                            $tmp[] = $t;
                        }
                        $list = $tmp;
                        break;
                    }
                }
            }
        }

        return $list[0];
    }

    public function hasPriority($list)
    {
        if (!$this->withPriority) {
            return false;
        }
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i] === $this->priorityOperator) {
                return true;
            }
        }

        return false;
    }

    public function extractParenthesis($input, $index)
    {
        $extract = [];
        for ($i = $index; $i < count($input); $i++) {
            if ($input[$i] === '(') {
                [$i, $sub] = $this->extractParenthesis($input, $i + 1);
                $extract[] = $sub;
            } else {
                if ($input[$i] === ')') {
                    return [$i, $extract];
                } else {
                    $extract[] = $input[$i];
                }
            }

        }

        return $extract;
    }

    public function parse($line)
    {
        $array = str_split($line);
        $parsed = [];
        foreach ($array as $entry) {
            if ($entry == ' ') {
                continue;
            }

            if (is_numeric($entry)) {
                $parsed[] = (int)trim($entry);
            } else {
                $parsed[] = trim($entry);
            }
        }

        return $this->extractParenthesis($parsed, 0);
    }
}


function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}


