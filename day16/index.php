<?php

$rules = [];
$myTicket = [];
$nearbyTickets = [];
$i = 0;

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);
    if ($f !== false) {
        if ($f === '') {
            $i++;
            continue;
        }
        if ($f === 'nearby tickets:' || $f === 'your ticket:') {
            continue;
        }
        if ($i === 0) {
            [$name, $rule] = createRule($f);
            $rules[$name] = $rule;

        } elseif ($i === 1) {
            $myTicket = explode(',', $f);
        } else {
            $nearbyTickets[] = explode(',', $f);
        }
    }
} while ($f !== false);

$invalid = [];
$validTickets = [];

foreach ($nearbyTickets as $ticket) {
    $isValidTicket = true;
    foreach ($ticket as $n) {
        if (!isValidateByRules($n, $rules)) {
            $invalid[] = $n;
            $isValidTicket = false;
        }
    }
    if ($isValidTicket) {
        $validTickets[] = $ticket;
    }
}
$fieldList = [];

foreach ($rules as $name => $rule) {
    for ($i = 0; $i < count($myTicket); $i++) {
        $validField = true;
        foreach ($validTickets as $ticket) {
            $isValidTicket = true;
            if (!isValidateByRule($ticket[$i], $rule)) {
                $validField = false;
                break;
            }
        }
        if ($validField) {
            $fieldList[$name][] = $i;
        }
    }
}
uasort(
    $fieldList,
    function ($a, $b) {
        return count($a) - count($b);
    }
);
$check = [];
foreach ($fieldList as $name => $possibles) {
    $diff = array_diff($possibles, $check);
    $fieldList[$name] = array_shift($diff);
    $check = array_merge($check, $possibles);
}

$t = 1;

foreach ($fieldList as $name => $possibles) {
    if (preg_match('/^departure/', $name)) {
        $t *= $myTicket[$possibles];
    }
}

echo "Puzzle A : ".array_sum($invalid)."\n";
echo "Puzzle B : ".$t."\n";

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}

function createRule($str)
{
    [$name, $rule] = explode(': ', $str);

    $exp = explode(' or ', $rule);

    $exp = array_map(
        function ($r) {
            return explode('-', $r);
        },
        $exp
    );

    return [$name, $exp];
}

function isValidateByRules($number, $rules)
{
    foreach ($rules as $name => $r) {
        if (isValidateByRule($number, $r)) {
            return true;
        }
    }

    return false;
}

function isValidateByRule($number, $rules)
{

    foreach ($rules as $rule) {
        if ($number >= $rule[0] && $number <= $rule[1]) {
            return true;
        }
    }

    return false;
}
