<?php
$startTime = microtime(true);

$input = stream_get_contents(STDIN);
$foods = [];

foreach (explode("\n", $input) as $line) {
    if (preg_match('/(?<ingredients>.*)\s\(contains\s(?<allergens>.*)\)/', $line, $matches)) {
        $foods[] = [explode(' ', $matches['ingredients']), explode(', ', $matches['allergens'])];
    }
}

$allergensList = [];
$ingredientsList = [];
$ingredientCount = [];

foreach ($foods as [$ingredients, $allergens]) {
    // create ingredients and Allergens lists
    $ingredientsList = array_unique(array_merge($ingredientsList, $ingredients));
    $allergensList = array_unique(array_merge($allergensList, $allergens));

    // counts how many times the ingredient appears
    foreach ($ingredients as $ingredient) {
        if (isset($ingredientCount[$ingredient])) {
            $ingredientCount[$ingredient] += 1;
        } else {
            $ingredientCount[$ingredient] = 1;
        }
    }
}

$excluded = [];

foreach ($foods as [$ingredients, $allergens]) {
    //for each ingredient on the list if it is not in the composition, the allergens can be considered to be excluded
    foreach ($ingredientsList as $ingredient) {
        if (!in_array($ingredient, $ingredients)) {
            if (isset($excluded[$ingredient])) {
                $excluded[$ingredient] = array_unique(array_merge($excluded[$ingredient], $allergens));
            } else {
                $excluded[$ingredient] = $allergens;
            }
        }
    }
}

$count = 0;
$composition = [];

// for each ingredient which contains all the allergens excluded, we sum the number of times it appears in the composition
// otherwise we create a table of possible allergen composition of the ingredient
foreach ($excluded as $ingredient => $exclude) {
    $diff = array_diff($allergensList, $exclude);
    if (count($diff) === 0) {
        $count += $ingredientCount[$ingredient];
    } else {
        $composition[$ingredient] = $diff;
    }
}

$done = false;
// loop as long as the composition of an ingredient is not unique
while (!$done) {
    $done = true;
    foreach ($composition as $ingredient => $possible) {
        if (count($possible) > 1) {
            $done = false;
            continue;
        } else {
            foreach ($composition as $rIngredient => $rPossible) {
                if (count($rPossible) > 1) {
                    $composition[$rIngredient] = array_diff($rPossible, $possible);
                }
            }
        }
    }
}

$sorted = [];
// reverse and sort ingredient alphabetically by their allergen
foreach ($composition as $ingredient => $allergens) {
    $sorted[array_shift($allergens)] = $ingredient;
}

ksort($sorted);

// Parts response
echo "Part 1  : $count \n";
echo "Part 2 : ".implode(',', $sorted);

echo "\ntotal time: ", (microtime(true) - $startTime), "\n";

function dump($args)
{
    foreach (func_get_args() as $arg) {
        error_log(var_export($arg, true));
    }
}
