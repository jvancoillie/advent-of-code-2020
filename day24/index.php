<?php

$startTime = microtime(true);

$lobby = new Lobby(stream_get_contents(STDIN));

echo "Part 1 : ".$lobby->part1()." \n";
echo "Part 2 : ".$lobby->part2()." \n";
echo "\ntotal time: ", (microtime(true) - $startTime), "\n";

class Lobby
{
    const BLACK = '#';
    const WHITE = 'O';
    private $directions = [
        [-1, 0],
        [1, 0],
        [0, 1],
        [-1, 1],
        [1, -1],
        [0, -1],
    ];
    private $minX = 0;
    private $minY = 0;
    private $maxX = 0;
    private $maxY = 0;
    private $floor = [];
    private $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function part1()
    {
        foreach (explode("\n", $this->input) as $data) {
            $moves = $this->parseLine($data);
            $this->doMoves($moves);

        }

        return $this->countBlackTiles();
    }

    /**
     * e, se, sw, w, nw, and ne
     */
    public function parseLine($string)
    {
        $split = str_split($string);
        $moves = [];
        for ($i = 0; $i < count($split); $i++) {
            $move = $split[$i];
            if ($split[$i] === "s" || $split[$i] === "n") {
                if (isset($split[$i + 1]) && ($split[$i + 1] === "e" || $split[$i + 1] === "w")) {
                    $move .= $split[$i + 1];
                    $i++;
                }
            }
            $moves[] = $move;
        }

        return $moves;
    }

    public function doMoves($moves)
    {
        $x = $y = 0;
        foreach ($moves as $move) {
            switch ($move) {
                case 'w':
                    $x -= 1;
                    break;
                case 'e':
                    $x += 1;
                    break;
                case 'ne':
                    $y += 1;
                    break;
                case 'nw':
                    $y += 1;
                    $x -= 1;
                    break;
                case 'se':
                    $y -= 1;
                    $x += 1;
                    break;
                case 'sw':
                    $y -= 1;
                    break;
            }
        }
        if ($x > $this->maxX) {
            $this->maxX = $x;
        }
        if ($x < $this->minX) {
            $this->minX = $x;
        }
        if ($y > $this->maxY) {
            $this->maxY = $y;
        }
        if ($y < $this->minY) {
            $this->minY = $y;
        }
        if (isset($this->floor[$x][$y])) {
            $this->floor[$x][$y] = ($this->floor[$x][$y] === self::BLACK) ? self::WHITE : self::BLACK;
        } else {
            $this->floor[$x][$y] = self::BLACK;
        }

    }

    public function countBlackTiles()
    {
        $sum = 0;
        foreach ($this->floor as $line) {
            foreach ($line as $tile) {
                if ($tile === self::BLACK) {
                    $sum++;
                }
            }
        }

        return $sum;
    }

    public function part2()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->dayFlipping();
        }

        return $this->countBlackTiles();
    }

    public function dayFlipping()
    {
        $newFloor = [];

        $this->extendFloor();

        for ($x = $this->minX; $x <= $this->maxX; $x++) {
            for ($y = $this->minY; $y <= $this->maxY; $y++) {
                $colors = $this->countNeighborsColors($x, $y);
                $tile = $this->floor[$x][$y] ?? self::WHITE;
                if ($tile === self::WHITE && $colors[self::BLACK] === 2) {
                    $newFloor[$x][$y] = self::BLACK;
                } elseif ($tile === self::BLACK && ($colors[self::BLACK] === 0 || $colors[self::BLACK] > 2)) {
                    $newFloor[$x][$y] = self::WHITE;
                } else {
                    $newFloor[$x][$y] = $tile;
                }
            }
        }

        $this->floor =  $newFloor;
    }

    public function countNeighborsColors($x, $y)
    {
        $colors = [self::BLACK => 0, self::WHITE => 0];
        foreach ($this->directions as [$ax, $ay]) {
            $nx = $x + $ax;
            $ny = $y + $ay;
            if (isset($this->floor[$nx][$ny])) {
                $colors[$this->floor[$nx][$ny]]++;
            } else {
                $colors[self::WHITE]++;
            }
        }

        return $colors;
    }

    public function extendFloor()
    {
        $this->minX--;
        $this->maxX++;
        $this->minY--;
        $this->maxY++;
    }
}



