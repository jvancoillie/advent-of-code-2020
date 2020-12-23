<?php

$startTime = microtime(true);

$input = stream_get_contents(STDIN);
$input = array_map('intval', str_split($input));

$game = new GameA($input);
$part1 = $game->play();

echo "Part 1 : ".$part1." \n";

$game = new GameB($input);
$part2 = $game->play();

echo "Part 2 : ".$part2." \n";

echo "\ntotal time: ", (microtime(true) - $startTime), "\n";

class GameA
{
    private $cups;
    private $currentPosition;
    private $length;
    private $rounds;

    /**
     * Game constructor.
     * @param $cups
     */
    public function __construct($cups)
    {
        $this->cups = $cups;
        $this->currentPosition = 0;
        $this->length = count($cups);
        $this->rounds = 100;
    }

    public function move()
    {
        $cups = $this->cups;
        $currentNumber = array_shift($cups);
        $pickUp = array_slice($cups, 0, 3);
        $remaining = array_slice($cups, 3);
        $destination = $currentNumber;

        do {
            $destination--;
            if ($destination === 0) {
                $destination = $this->length;
            }
        } while (!in_array($destination, $remaining));

        $nextPosition = array_search($destination, $remaining);
        $part1 = array_slice($remaining, 0, $nextPosition + 1);
        $part2 = array_slice($remaining, $nextPosition + 1);
        $this->cups = array_merge($part1, $pickUp, $part2, [$currentNumber]);
    }

    public function play()
    {
        for ($i = 1; $i <= $this->rounds; $i++) {
            $this->move();
        }

        $p = array_search(1, $this->cups);
        $part1 = array_slice($this->cups, 0, $p);
        $part2 = array_slice($this->cups, $p + 1);

        return implode('', $part2).''.implode('', $part1);
    }

}

class GameB
{
    private $cups;
    private $current;
    private $length;
    private $next = [];
    private $prev = [];
    private $rounds;

    /**
     * Game constructor.
     * @param $cups
     */
    public function __construct($cups)
    {
        $this->cups = $cups;
        $this->rounds = 10000000;
        $this->length = 1000000;

        $this->init();
    }

    public function init()
    {
        for ($i = 0; $i < $this->length; $i++) {
            $this->next[$i] = $i + 1;
            $this->prev[$i] = $i - 1;
        }
        $this->next[$this->length - 1] = 0;
        $this->prev[0] = $this->length - 1;
        $this->current = $this->length - 1;

        foreach ($this->cups as $cup) {
            $cup -= 1;
            $this->next[$this->prev[$cup]] = $this->next[$cup];
            $this->prev[$this->next[$cup]] = $this->prev[$cup];
            $this->next[$cup] = $this->next[$this->current];
            $this->prev[$cup] = $this->current;
            $this->prev[$this->next[$this->current]] = $cup;
            $this->next[$this->current] = $cup;
            $this->current = $cup;
        }

        $this->current = $this->length - 1;
    }

    public function move()
    {
        $this->current = $this->next[$this->current];
        $a = $this->next[$this->current];
        $b = $this->next[$a];
        $c = $this->next[$b];
        $this->next[$this->current] = $this->next[$c];
        $this->prev[$this->next[$c]] = $this->current;
        $destination = $this->current - 1 < 0 ? $this->length - 1 : $this->current - 1;

        while ($destination === $a
            || $destination === $b
            || $destination === $c
        ) {
            $destination--;
            if ($destination < 0) {
                $destination = $this->length - 1;
            }
        }

        $this->next[$c] = $this->next[$destination];
        $this->prev[$a] = $destination;
        $this->prev[$this->next[$destination]] = $c;
        $this->next[$destination] = $a;
    }

    public function play()
    {

        for ($i = 0; $i < $this->rounds; $i++) {
            $this->move();
        }

        return ($this->next[0] + 1) * ($this->next[$this->next[0]] + 1);
    }
}

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}


