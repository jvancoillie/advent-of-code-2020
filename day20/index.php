<?php
$startTime = microtime(true);

$input = stream_get_contents(STDIN);

$tilesData = explode("\n\n", $input);

$tiles = [];
foreach ($tilesData as $data) {
    [$idLine, $tileData] = explode(':', $data);
    preg_match('/^Tile\s+?(?<id>\d+)$/', $idLine, $mactches);
    $tile = new Tile($mactches['id'], trim($tileData));
    $tiles[$mactches['id']] = $tile;
}

$puzzle = new Puzzle($tiles);
$response = $puzzle->resolve();
//dump($puzzle->getSeaMonsterPattern());


echo "Puzzle A : $response\n";
$puzzle->findSeaMonster(false);


class Tile
{

    private $positions = [];
    private $neighbors = [];
    private $grids = [];
    public $id;
    private $width;

    /**
     * Tile constructor.
     */
    public function __construct($id, $data)
    {
        $this->id = $id;
        $this->createBorders($data);
        $this->createAllPositions();
    }

    public function createAllPositions()
    {
        $this->positions[Position::FRONT_ROTATE90] = $this->rotate90($this->positions[Position::FRONT]);
        $this->positions[Position::FRONT_ROTATE180] = $this->rotate90($this->positions[Position::FRONT_ROTATE90]);
        $this->positions[Position::FRONT_ROTATE270] = $this->rotate90($this->positions[Position::FRONT_ROTATE180]);

        $this->positions[Position::BACK] = $this->flip($this->positions[Position::FRONT]);
        $this->positions[Position::BACK_ROTATE90] = $this->rotate90($this->positions[Position::BACK]);
        $this->positions[Position::BACK_ROTATE180] = $this->rotate90($this->positions[Position::BACK_ROTATE90]);
        $this->positions[Position::BACK_ROTATE270] = $this->rotate90($this->positions[Position::BACK_ROTATE180]);

        $this->grids[Position::FRONT_ROTATE90] = $this->rotateGrid($this->grids[Position::FRONT]);
        $this->grids[Position::FRONT_ROTATE180] = $this->rotateGrid($this->grids[Position::FRONT_ROTATE90]);
        $this->grids[Position::FRONT_ROTATE270] = $this->rotateGrid($this->grids[Position::FRONT_ROTATE180]);

        $this->grids[Position::BACK] = $this->flipGrid($this->grids[Position::FRONT]);
        $this->grids[Position::BACK_ROTATE90] = $this->rotateGrid($this->grids[Position::BACK]);
        $this->grids[Position::BACK_ROTATE180] = $this->rotateGrid($this->grids[Position::BACK_ROTATE90]);
        $this->grids[Position::BACK_ROTATE270] = $this->rotateGrid($this->grids[Position::BACK_ROTATE180]);
    }

    public function createBorders($data)
    {
        $exploded = explode("\n", $data);

        $this->width = strlen($exploded[0]) - 1;
        $height = count($exploded) - 1;

        $leftBorder = '';
        $rightBorder = '';
        $grid = [];
        $borders = [];
        foreach ($exploded as $key => $line) {
            $split = str_split($line);
            $grid[] = $split;
            if ($key === 0) {
                $borders[Border::TOP] = $line;
            }
            if ($key === $height) {
                $borders[Border::BOTTOM] = $line;
            }

            $leftBorder .= $split[0];

            $rightBorder .= $split[$this->width];
        }
        $borders[Border::RIGHT] = $rightBorder;
        $borders[Border::LEFT] = $leftBorder;

        $this->grids[Position::FRONT] = $grid;
        $this->positions[Position::FRONT] = $borders;

    }

    public function flip($borders)
    {
        $flippedBorders = [];

        $flippedBorders[Border::TOP] = $borders[Border::BOTTOM];
        $flippedBorders[Border::BOTTOM] = $borders[Border::TOP];
        $flippedBorders[Border::LEFT] = strrev($borders[Border::LEFT]);
        $flippedBorders[Border::RIGHT] = strrev($borders[Border::RIGHT]);

        return $flippedBorders;
    }

    public function flipGrid($grid)
    {
        $flippedGrid = [];
        foreach($grid as $key => $line){
            $flippedGrid[$this->width - $key] = $line;
        }
        ksort($flippedGrid);

        return $flippedGrid;
    }

    public function rotate90($borders)
    {
        $rotatedBorders = [];

        $rotatedBorders[Border::TOP] = strrev($borders[Border::LEFT]);
        $rotatedBorders[Border::RIGHT] = $borders[Border::TOP];
        $rotatedBorders[Border::BOTTOM] = strrev($borders[Border::RIGHT]);
        $rotatedBorders[Border::LEFT] = $borders[Border::BOTTOM];

        return $rotatedBorders;
    }

    public function rotateGrid($grid)
    {
        array_unshift($grid, null);
        $grid = call_user_func_array('array_map', $grid);
        $grid = array_map('array_reverse', $grid);

        return $grid;
    }

    public function setPossibleNeighbors(Tile $tile)
    {
        foreach (Position::getPositions() as $currentTilePosition) {
            $currentBorders = $this->getBordersByPosition($currentTilePosition);
            foreach (Position::getPositions() as $position) {
                $tileBorders = $tile->getBordersByPosition($position);

                if ($currentBorders[Border::TOP] === $tileBorders[Border::BOTTOM]) {
                    $this->neighbors[$currentTilePosition][Border::TOP][] = [
                        'tile' => $tile,
                        'position' => $position,
                    ];
                }
                if ($currentBorders[Border::RIGHT] === $tileBorders[Border::LEFT]) {
                    $this->neighbors[$currentTilePosition][Border::RIGHT][] = [
                        'tile' => $tile,
                        'position' => $position,
                    ];
                }
                if ($currentBorders[Border::BOTTOM] === $tileBorders[Border::TOP]) {
                    $this->neighbors[$currentTilePosition][Border::BOTTOM][] = [
                        'tile' => $tile,
                        'position' => $position,
                    ];
                }
                if ($currentBorders[Border::LEFT] === $tileBorders[Border::RIGHT]) {
                    $this->neighbors[$currentTilePosition][Border::LEFT][] = [
                        'tile' => $tile,
                        'position' => $position,
                    ];
                }
            }
        }
    }

    public function getNeighbor($position, $direction)
    {
        return $this->neighbors[$position][$direction] ?? [];
    }

    public function isCorner()
    {
        return (
                $this->isBottomLeftCorner() || $this->isBottomRightCorner()
                || $this->isTopLeftCorner() || $this->isTopRightCorner()
            );
    }

    public function getCornerTopLeftPositions()
    {
        $positions = [];
        foreach ($this->neighbors as $position => $neigbors) {
            if (!isset($neigbors[Border::TOP]) && !isset($neigbors[Border::LEFT])) {
                $positions[] = $position;
            }
        }

        return $positions;
    }

    public function isTopLeftCorner($position = null)
    {
        if ($position) {
            foreach ($this->neighbors[$position] as $neigbors) {
                if (!isset($neigbors[Border::TOP]) && !isset($neigbors[Border::LEFT])) {
                    return true;
                }
            }
        } else {
            foreach ($this->neighbors as $position => $neigbors) {
                if (!isset($neigbors[Border::TOP]) && !isset($neigbors[Border::LEFT])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isTopRightCorner($position = null)
    {
        if ($position) {
            foreach ($this->neighbors[$position] as $neigbors) {
                if (!isset($neigbors[Border::TOP]) && !isset($neigbors[Border::RIGHT])) {
                    return true;
                }
            }
        } else {
            foreach ($this->neighbors as $position => $neigbors) {
                if (!isset($neigbors[Border::TOP]) && !isset($neigbors[Border::RIGHT])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isBottomRightCorner($position = null)
    {
        if ($position) {
            foreach ($this->neighbors[$position] as $neigbors) {
                if (!isset($neigbors[Border::BOTTOM]) && !isset($neigbors[Border::RIGHT])) {
                    return true;
                }
            }
        } else {
            foreach ($this->neighbors as $position => $neigbors) {
                if (!isset($neigbors[Border::BOTTOM]) && !isset($neigbors[Border::RIGHT])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isBottomLeftCorner($position = null)
    {
        if ($position) {
            foreach ($this->neighbors[$position] as $neigbors) {
                if (!isset($neigbors[Border::BOTTOM]) && !isset($neigbors[Border::LEFT])) {
                    return true;
                }
            }
        } else {
            foreach ($this->neighbors as $position => $neigbors) {
                if (!isset($neigbors[Border::BOTTOM]) && !isset($neigbors[Border::LEFT])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getBordersByPosition($position)
    {
        return $this->positions[$position];
    }

    public function getGridWithoutBorderByPosition($position)
    {
        return $this->removeBorders($this->grids[$position]);
    }

    public function removeBorders($grid)
    {
        $withoutBorders = [];
        for($y=1; $y<count($grid) - 1; $y++) {
            for($x=1;$x<count($grid) -1;$x++){
                $withoutBorders[$y-1][$x-1] = $grid[$y][$x];
            }
        }

        return $withoutBorders;
    }

}

class Puzzle
{
    /**
     * @var Tile[]
     */
    private $tiles;
    private $width;
    private $solutions = [];

    /**
     * Puzzle constructor.
     * @param $tiles
     */
    public function __construct($tiles)
    {
        $this->tiles = $tiles;
        $this->width = sqrt(count($tiles));
    }

    public function resolve()
    {
        foreach ($this->tiles as $currentTile) {
            foreach ($this->tiles as $tile) {
                if ($currentTile->id === $tile->id) {
                    continue;
                }
                $currentTile->setPossibleNeighbors($tile);
            }
        }

        $total = 1;

        foreach ($this->tiles as $tile) {
            if ($tile->isTopLeftCorner()) {
                foreach ($tile->getCornerTopLeftPositions() as $position) {
                    $grid = $this->fillGrid($tile, $position, Border::RIGHT);
                    $this->solutions[] = $grid;
                }

            }

            if ($tile->isCorner()) {
                $total *= $tile->id;
            }
        }

        return $total;
    }

    public function findSeaMonster($withDisplay = false)
    {
        foreach($this->solutions as $key => $solution){
            if(false !== $count = $this->checkSolution($solution, $withDisplay)){
                echo 'Puzzle B : '.$count;
                break;
            }
        }
    }

    public function checkSolution($solution, $withDisplay)
    {
        $grid = [];
        $start = 0;
        for($y=0;$y<$this->width;$y++) {
            for($x=0;$x<$this->width;$x++) {
                /**
                 *@var Tile $tile
                 */
                [$tile,$position] = $solution[$y][$x];
                $merge = $tile->getGridWithoutBorderByPosition($position);
                for($i=0;$i<count($merge);$i++){
                    $c = $grid[$i+$start]??'';
                    $grid[$i+$start] = trim($c.implode('',$merge[$i]));
                }
            }
            $start+=8;
        }
        foreach($grid as $key => $line){
            $grid[$key] = str_split($line);
        }

        if(false !== $points = $this->hasSeaMonster($grid)){
            $this->replacePoints($grid, $points);
            if($withDisplay){
                dumpGrid($grid);
            }
            return $this->countHashtags($grid);
        }

        return false;
    }

    public function replacePoints(&$grid, $points)
    {
        foreach($points as [$y, $x]){
            $grid[$y][$x] ='O';
        }
    }

    public function countHashtags($grid)
    {
        $total = 0;
        for($y=0;$y<count($grid) ; $y++) {
            for($x=0;$x<count($grid);$x++){
                if($grid[$y][$x] === '#'){
                    $total++;
                }
            }
        }

        return $total;
    }

    public function hasSeaMonster($grid)
    {
        $points = [];
        for($y=0;$y<count($grid); $y++) {
            for($x=0;$x<count($grid);$x++){
                if(false !== $monsterPoints = $this->canDrawMonster($grid, $x, $y)){
                    $points = array_merge($points, $monsterPoints);

                }
            }
        }
        return count($points)>0?$points:false;
    }

    public function canDrawMonster($grid, $x, $y)
    {
        $points=[];

        foreach($this->getSeaMonsterPattern() as [$dy, $dx])
        {
            $nx=$x+$dx;
            $ny=$y+$dy;
            if(isset($grid[$ny][$nx]) && $grid[$ny][$nx] === '#'){
                $points[] = [$ny, $nx];
            }else{
                return false;
            }
        }

        return $points;
    }

    /**
     * return point to draw SeaMonster
     *                    #
     *  #    ##    ##    ###
     *  #  #  #  #  #  #
     */
    public function getSeaMonsterPattern()
    {
        $monster = [
            [0, 18],
            [1, 0],
            [1, 5],
            [1, 6],
            [1, 11],
            [1, 12],
            [1, 17],
            [1, 18],
            [1, 19],
            [2, 1],
            [2, 4],
            [2, 7],
            [2, 10],
            [2, 13],
            [2, 16],
        ];

        return $monster;
    }

    public function fillGrid(Tile $tile, $position, $direction, $x = 0, $y = 0, $grid = [])
    {

        if ($y === 0 && $x === 0 && !$tile->isTopLeftCorner($position)) {
            return $grid;
        }

        if ($y === 0 && $x === ($this->width - 1) && !$tile->isTopLeftCorner($position)) {
            return $grid;
        }

        if ($y === ($this->width - 1) && $x === 0 && !$tile->isBottomLeftCorner($position)) {
            return $grid;
        }

        if ($y === ($this->width - 1) && $x === ($this->width - 1) && !$tile->isBottomRightCorner($position)) {
            return $grid;
        }

        $grid[$y][$x] = [$tile, $position];

        if ($x === ($this->width - 1) && $y === ($this->width - 1)) {
            return $grid;
        }

        if ($direction === Border::RIGHT) {
            $x++;
        } elseif ($direction === Border::LEFT) {
            $x--;
        }

        if (0 === $x % ($this->width) && $direction === Border::RIGHT) {
            $x--;
            $direction = Border::BOTTOM;
            $y++;
        } elseif (-1 === $x && $direction === Border::LEFT) {
            $x = 0;
            $direction = Border::BOTTOM;
            $y++;
        } elseif ($direction === Border::BOTTOM) {
            if (0 === $y % 2) {
                $direction = Border::RIGHT;
                $x++;
            } else {
                $direction = Border::LEFT;
                $x--;
            }
        }

        foreach ($tile->getNeighbor($position, $direction) as $n) {
            if ($grid = $this->fillGrid($n['tile'], $n['position'], $direction, $x, $y, $grid)) {
            }
        }

        return $grid;
    }

    public function getTile($id)
    {
        return $this->tiles[$id] ?? null;
    }


}

class Border
{
    const TOP = "top";
    const RIGHT = 'right';
    const LEFT = 'left';
    const BOTTOM = "bottom";
}

class Position
{
    const FRONT = "front";
    const FRONT_ROTATE90 = "front-rotate-90";
    const FRONT_ROTATE180 = "front-rotate-180";
    const FRONT_ROTATE270 = "front-rotate-270";

    const BACK = "back";
    const BACK_ROTATE90 = "back-rotate-90";
    const BACK_ROTATE180 = "back-rotate-180";
    const BACK_ROTATE270 = "back-rotate-270";

    public static function getPositions()
    {
        return [
            self::FRONT,
            self::FRONT_ROTATE90,
            self::FRONT_ROTATE180,
            self::FRONT_ROTATE270,
            self::BACK,
            self::BACK_ROTATE90,
            self::BACK_ROTATE180,
            self::BACK_ROTATE270,
        ];
    }
}

echo "\ntotal time: ", (microtime(true) - $startTime), "\n";

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}

function dumpGrid($array)
{
    foreach ($array as $key => $lines) {
        echo implode('', $lines)."\n";
    }
    echo "\n";
}
