<?php

$startTime = microtime(true);

$data = explode("\n", stream_get_contents(STDIN));

$cardSubjectNumber = (int)$data[0];
$doorSubjectNumber = (int)$data[1];

$cardLoopSize = findLoopSize($cardSubjectNumber);
$doorLoopSize = findLoopSize($doorSubjectNumber);

$cardKey = transformNumber($cardSubjectNumber, $doorLoopSize);
$doorKey = transformNumber($doorSubjectNumber, $cardLoopSize);

echo "Part 1 : ".$cardKey." \n";

echo "\ntotal time: ", (microtime(true) - $startTime), "\n";

function findLoopSize($key)
{
    $loopSize = 0;
    $subjectNumber = 7;
    $value = 1;

    while ($key !== $value) {
        $loopSize++;
        $value *= $subjectNumber;
        $value %= 20201227;
    }

    return $loopSize;
}

function transformNumber($subjectNumber, $loopSize)
{
    $n = $subjectNumber;
    $value = 1;

    for ($i = 0; $i < $loopSize; $i++) {
        $value *= $n;
        $value %= 20201227;
    }

    return $value;
}

