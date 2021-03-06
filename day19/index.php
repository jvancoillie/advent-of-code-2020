<?php
$startTime = microtime(true);

$input = stream_get_contents(STDIN);

[$rulesInput, $messagesInput] = explode("\n\n", $input);

$rules = [];

foreach (explode("\n", $rulesInput) as $rule) {
    [$key, $value] = explode(":", $rule);
    if (preg_match('/\|/', $rule)) {
        $sub = [];
        foreach (explode("|", $value) as $subRule) {
            $sub[] = explode(' ', trim($subRule));
        }
    } else {
        $sub = explode(' ', trim(str_replace('"', '', $value)));
    }
    $rules[$key] = $sub;
}


$messages = explode("\n", $messagesInput);


class Decoder
{
    private $rules = [];

    /**
     * Decoder constructor.
     * @param $rules
     */
    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    public function createRegexpFromRuleId($id)
    {
        return $this->createRegexp($this->rules[$id], $this->rules);
    }

    public function createRegexp($rule, $rules)
    {
        if (ctype_alpha($rule[0])) {
            return $rule[0];
        }
        $chain = [];
        if (is_array($rule[0])) {
            // here got piped need to go deep
            $chain[] = $this->createRegexp($rule[0], $rules);
            $chain[] = $this->createRegexp($rule[1], $rules);

            return '('.implode('|', $chain).')';
        } else {
            foreach ($rule as $n) {
                $chain[] = $this->createRegexp($rules[$n], $rules);
            }

            return implode('', $chain);
        }
    }
}


$decoder = new Decoder($rules);
$regexp0 = $decoder->createRegexpFromRuleId(0);

$sum = 0;
foreach ($messages as $message) {
    if(preg_match("/^".$regexp0."$/", $message)){
        $sum++;
    }
}

echo "Puzzle A: $sum \n";

// Thanks to thibpat for his solution i didn't found it
// https://github.com/tpatel/advent-of-code-2020/blob/main/day19.js

$regexp42 = $decoder->createRegexpFromRuleId(42);
$regexp31 = $decoder->createRegexpFromRuleId(31);

$regexp = '/^(?<group42>('.$regexp42.')+)(?<group31>('.$regexp31.')+)$/';
$sum = 0;

foreach ($messages as $message) {
    if(preg_match($regexp, $message, $matches))
    if(isset($matches['group42']) && isset($matches['group31'])){
        preg_match_all('/'.$regexp42.'/', $matches['group42'], $matches42);
        $matches42 = $matches42[0];
        preg_match_all('/'.$regexp31.'/', $matches['group31'], $matches31);
        $matches31 = $matches31[0];
        if(count($matches42) > count($matches31)){
            $sum++;
        }
    }
}

echo "Puzzle B: $sum \n";


echo "\ntotal time: ", (microtime(true) - $startTime), "\n";


function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}


