<?php

$input = [];
$passport = '';

do {
    $f = stream_get_line(STDIN, 10000, PHP_EOL);

    if ($f !== false) {
        if ($f === '') {
            $input[] = trim($passport);
            $passport = '';
        }

        $passport .= $f . ' ';
    }
} while ($f !== false);
$input[] = trim($passport);
$entries = [
    'byr' => 'required',
    'iyr' => 'required',
    'eyr' => 'required',
    'hgt' => 'required',
    'hcl' => 'required',
    'ecl' => 'required',
    'pid' => 'required',
];

$puzzle1Valid = 0;
$puzzle2Valid = 0;
foreach ($input as $p) {
    $lines = explode(' ', $p);
    $passport = [];
    $test = $entries;
    $t = true;

    foreach ($lines as $l) {
        [$key, $value] = explode(':', $l);
        if ($key !== 'cid') {
            if (!isValid($key, $value)) {
                $t = false;
            }
        }
        $passport[$key] = $value;
        unset($test[$key]);
    }

    if (count($test) === 0) {
        $puzzle1Valid++;
        if ($t) {
            $puzzle2Valid++;
        }
    }
}

function isValid($key, $value)
{
    switch ($key) {
        case 'byr':
            if ($value >= 1920 && $value <= 2002) {
                return true;
            }
            break;
        case 'iyr':
            if ($value >= 2010 && $value <= 2020) {
                return true;
            }
            break;
        case 'eyr':
            if ($value >= 2020 && $value <= 2030) {
                return true;
            }
            break;
        case 'hgt':
            if (preg_match('/cm$/', $value)) {
                $h = substr($value, 0, -2);
                if ($h >= 150 && $h <= 193) {
                    return true;
                }
            }
            if (preg_match('/in$/', $value)) {
                $h = substr($value, 0, -2);
                if ($h >= 59 && $h <= 76) {
                    return true;
                }
            }
            break;
        case 'hcl':
            if (preg_match('/^#[a-f0-9]{6}$/', $value)) {
                return true;
            }
            break;
        case 'ecl':
            if (in_array($value, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth'])) {
                return true;
            }
            break;
        case 'pid':
            if (preg_match('/^[0-9]{9}$/', $value)) {
                return true;
            }
            break;
    }

    return false;
}

error_log(var_export(sprintf('puzzle 1 valid passports: %d', $puzzle1Valid), true));
error_log(var_export(sprintf('puzzle 2 valid passports: %d', $puzzle2Valid), true));

