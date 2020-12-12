<?php

$instructions = [];

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        $instructions[] = [substr($f, 0, 1), substr($f, 1)];
    }
} while ($f !== false);
$positions = ['N' => 0, 'S' => 0, 'E' => 0, 'W' => 0];


$curDirDegre = 90;

class Navigation
{
    private $x = 0;
    private $y = 0;

    private $waypointX = 10;
    private $waypointY = 1;

    private $degre;
    private $instructions;

    /**
     * Navigation constructor.
     * @param $instructions
     */
    public function __construct($instructions)
    {
        $this->instructions = $instructions;
        $this->degre = 90;
    }

    public function navigate($withWaypoint = false)
    {
        $this->x = $this->y = 0;
        foreach ($this->instructions as $key => [$d, $v]) {
//            dump('=============='.$key.'=============');
//            dump(sprintf('INSTRUCTION %s : %d.', $d, $v));
//            dump(sprintf('   START POSITION %d,%d', $this->x, $this->y, $this->degre));
//            if ($withWaypoint) {
//                dump(sprintf('   WAYPOINTS %d,%d ', $this->waypointX, $this->waypointY));
//            } else {
//                dump(sprintf('   DEGRE %d ', $this->degre));
//            }
            switch ($d) {
                case 'N':
                    if ($withWaypoint) {
                        $this->waypointY += $v;
                    } else {
                        $this->y -= $v;
                    }
                    break;
                case 'S':
                    if ($withWaypoint) {
                        $this->waypointY -= $v;
                    } else {
                        $this->y += $v;
                    }
                    break;
                case 'E':
                    if ($withWaypoint) {
                        $this->waypointX += $v;
                    } else {
                        $this->x += $v;
                    }
                    break;
                case 'W':
                    if ($withWaypoint) {
                        $this->waypointX -= $v;
                    } else {
                        $this->x -= $v;
                    }
                    break;
                case 'F':
                    if ($withWaypoint) {
                        $this->forwardToWaypoint($v);
                    } else {
                        $this->forward($v);
                    }

                    break;
                case 'L':
                case 'R':
                    if ($withWaypoint) {
                        $this->rotateWaypoint($d, $v);
                    } else {
                        $this->rotate($d, $v);
                    }
                    break;
            }
        }

        return $this->manhattan();
    }

    public function forward($dist)
    {
        switch ($this->degre) {
            case 0:
                $this->y -= $dist;
                break;
            case 90:
                $this->x += $dist;
                break;
            case 180:
                $this->y += $dist;
                break;
            case 270:
                $this->x -= $dist;
                break;
        }
    }

    public function forwardToWaypoint($dist)
    {
        $this->y += $this->waypointY * $dist;
        $this->x += $this->waypointX * $dist;
    }

    public function rotate($dir, $value)
    {
        if ($dir === 'L') {
            $this->degre -= $value;
        } else {
            $this->degre += $value;
        }
        $this->degre = (360 + $this->degre) % 360;

    }

    public function rotateWaypoint($dir, $value)
    {

        // rotate the way point
        // 90 CW   (x, y) => (y, -x)
        // 90 CCW  (x, y) => (-y, x)

        for ($i = 0; $i < $value / 90; $i++) {
            if ($dir === 'L') {
                $x = $this->waypointX;
                $this->waypointX = -$this->waypointY;
                $this->waypointY = $x;
            } else {
                $x = $this->waypointX;
                $this->waypointX = $this->waypointY;
                $this->waypointY = -$x;
            }
        }
    }

    public function manhattan()
    {
        return (abs($this->x) + abs($this->y));
    }

    public function manhattanWaypoint()
    {
        return (abs($this->waypointX) + abs($this->waypointY));
    }

}

$navigation = new Navigation($instructions);

$manhattan = $navigation->navigate();
//dump('===========================');
//dump('==========waypoint=========');
//dump('===========================');
$manhattanWaypoint = $navigation->navigate(true);
error_log(var_export(sprintf('puzzle A : %d', $manhattan), true));
error_log(var_export(sprintf('puzzle B : %d', $manhattanWaypoint), true));

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}