<?php
$startTime = microtime(true);

$input = stream_get_contents(STDIN);
$deckPlayers = [];

foreach (explode("\n\n", $input) as $player) {
    foreach (explode("\n", $player) as $line) {
        if (preg_match("/^Player\s(?<id>.*):$/", $line, $matches)) {
            $playerId = $matches['id'];
            $deckPlayers[$playerId] = new Deck($playerId);
        } else {
            $card = (int)$line;
            $deckPlayers[$playerId]->addCard($card);
        }
    }
}

$game = new Game($deckPlayers);
$part1 = $game->playPart1();
$part2 = $game->playPart2();

echo "Part 1 : ".$part1." \n";
echo "Part 2 : ".$part2." \n";

class Game
{
    /**
     * @var Deck[]
     */
    private $deckPlayers = [];

    /**
     * Game constructor.
     * @param array $deckPlayers
     */
    public function __construct(array $deckPlayers)
    {
        $this->deckPlayers = $deckPlayers;
    }

    public function playPart1()
    {
        $player1 = clone $this->deckPlayers[1];
        $player2 = clone $this->deckPlayers[2];
        $round = 0;
        while ($player1->hasCards() && $this->deckPlayers[2]->hasCards()) {
            $round++;
            $cardPlayer1 = $player1->nextCard();
            $cardPlayer2 = $player2->nextCard();
            if ($cardPlayer1 > $cardPlayer2) {
                $player1->addCard($cardPlayer1)->addCard($cardPlayer2);
            } elseif ($cardPlayer2 > $cardPlayer1) {
                $player2->addCard($cardPlayer2)->addCard($cardPlayer1);
            }
        }

        return $player1->hasCards() ? $player1->getPoints() : $player2->getPoints();
    }

    public function playPart2()
    {
        $player1 = clone $this->deckPlayers[1];
        $player2 = clone $this->deckPlayers[2];

        $winner = $this->playRecurseGame($player1, $player2);

        return $winner->getPoints();
    }

    public function playRecurseGame(Deck $player1, Deck $player2, $game=0)
    {
        $player1Rounds = $player2Rounds = [];
        $round = 1;
        $game++;

        while (true) {
            if (!$player1->hasCards()) {
                return $player2;
            }

            if (!$player2->hasCards()) {
                return $player1;
            }
            $hash1 = $player1->hashDeck();
            $hash2 = $player2->hashDeck();
            if (in_array($hash1, $player1Rounds) || in_array($hash2, $player2Rounds)) {
                return  $player1;
            }

            $player1Rounds[] = $hash1;
            $player2Rounds[] = $hash2;

            $cardPlayer1 = $player1->nextCard();
            $cardPlayer2 = $player2->nextCard();

            if ($player1->countCards() >= $cardPlayer1   && $player2->countCards() >= $cardPlayer2) {
                $subPlayer1 = clone $player1;
                $subPlayer2 = clone $player2;
                $subPlayer1->setDeckLimit($cardPlayer1);
                $subPlayer2->setDeckLimit($cardPlayer2);

                $recurseWinner = $this->playRecurseGame($subPlayer1, $subPlayer2, $game);
                if($recurseWinner->getPlayerId() === 1){
                    $player1->addCard($cardPlayer1)->addCard($cardPlayer2);
                }elseif($recurseWinner->getPlayerId() === 2){
                    $player2->addCard($cardPlayer2)->addCard($cardPlayer1);
                }

            } else {
                if ($cardPlayer1 > $cardPlayer2) {
                    $player1->addCard($cardPlayer1)->addCard($cardPlayer2);
                } elseif ($cardPlayer2 > $cardPlayer1) {
                    $player2->addCard($cardPlayer2)->addCard($cardPlayer1);
                }

            }
            $round++;
        }
    }
}

class Deck
{
    private $cards = [];
    private $playerId;

    public function __construct($playerId)
    {
        $this->cards = [];
        $this->playerId = (int)$playerId;
    }

    public function addCard($card)
    {
        $this->cards[] = $card;

        return $this;
    }

    public function nextCard()
    {
        return array_shift($this->cards);
    }

    public function hasCards()
    {
        return count($this->cards) > 0;
    }

    public function countCards()
    {
        return count($this->cards);
    }

    public function getPoints()
    {
        $total = 0;
        foreach (array_reverse($this->cards) as $key => $card) {
            $total += ($key + 1) * $card;
        }

        return $total;
    }

    public function hashDeck()
    {
        return implode('-', $this->cards);
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function setDeckLimit($limit)
    {
        $this->cards = array_slice($this->cards, 0, $limit);
    }

}

echo "\ntotal time: ", (microtime(true) - $startTime), "\n";

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}
