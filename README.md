# Advent of Code 2020

 https://adventofcode.com/2020/
 
## Docker env
 * build: docker-compose build
 * up: docker-compose up -d
 * exec: docker-compose exec advent bash
 * run aoc day challenge: sh run -d 01
 
 ## run file 
 `sh run` play the current day `php day01/index.php < day01/input.txt`
 
 options :
  
  `-t` : test mode (use test.txt input file)

  `-d` : day to run (-d 01) 
  
```
sh run -d 01 -t // run day 01 in test mode (php day01/index.php < day01/test.txt)
```
```
sh run -d 10 // run current day 10 (php day10/index.php < day10/input.txt)
```
```
sh run -t // run current day in test mode (php day${DOW}/index.php < day${DOW}/test.txt)
```