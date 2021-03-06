<?php
include 'src/counter.php';

static $current_serving = array(-1); // serving by counter 1 5 that -1 is genesis block
static $waiting = array(); // waiting ppl


$counter1 = new Counter();
$counter2 = new Counter();
$counter3 = new Counter();
$counter4 = new Counter();
$counter5 = new Counter();
$counter_array = array($counter1, $counter2, $counter3, $counter4, $counter5);

function assignToCounter()
{
    global $counter_array;
    global $current_serving;
    global $waiting;
    for ($i = 0; $i < count($current_serving) - 1; $i++) {
        if ($counter_array[$i]->get_current_serving() == null) {
            if (count($waiting) >= 1) {
                foreach ($waiting as $value) {
                    checkSlot($value);
                }
            }
            $counter_array[$i]->set_current_serving($current_serving[$i + 1], $current_serving, ($i + 1));
        }
    }
}

function generate()
{

    $random_number = rand(1000, 9999);
    global $current_serving;
    global $waiting;
    if (check($random_number) && count($current_serving) <= 5) {
        array_push($current_serving, $random_number);
        return $random_number;
    } else {
        array_push($waiting, $random_number);
        return $random_number;
    }
}

function check($generated)
{
    global $current_serving;
    foreach ($current_serving as $value) {
        if ($generated == $value) {
            $generated = rand(1000, 9999);
            check($generated);
        } else {
            return $generated;
        }
    }
}

function checkSlot($value) // basically to ensure we either add or not
{
    global $current_serving;
    global $waiting;
    for ($i = 0; $i < count($current_serving); $i++) {
        if ($current_serving[$i] == null) {
            $current_serving[$i] = $value;
            $toreplace = array_search($value, $waiting);
            $waiting[$toreplace] = null;
            return true;
        }
    }
    return false;
}

function get_counter(&$number, &$counter_array)
{
    foreach ($counter_array as $value) {
        if ($value->get_current_serving() == $number) {
            echo "You're assinged to Counter: " . (array_search($value, $counter_array) + 1) . "\n";
            return true;
        }
        // else echo "All counters are full, You're assigned to waiting Que\n";
    }
    echo "All counters are full, You're assigned to waiting Que\n\n";
    return false;
}

function get_avg_time(&$counter_array)
{
    global $current_serving;
    global $waiting;
    static $temp = 0;
    foreach ($counter_array as $value) {
        $temp += ($value->get_timeCreated() / 3000); //miliseconds
    }
    // this array has -1 as its genesis 
    if (count($current_serving) <= 5 && count($waiting) == 0) {
        echo "The waiting time is: 0. Theres available Counter For you\n\n";
        return false;
    } else {
        foreach ($waiting as $value) // we are basically assuming the wating Q will use the max time which is 5mins
        {
            $temp += 3000000; //milisec
        }
    }

    $temp /= count($counter_array);
    $temp /= 60; // seconds
    $temp /= 60; // minutes
    $temp /= 30; // 30 sec *
    return floor($temp); // return how many mins you should wait

}

function show_avCounters(&$counter_array)
{
    foreach ($counter_array as $value) {
        if ($value->get_current_serving() == null) {
            echo "Counter: " . array_search($value, $counter_array) + 1;
        }
    }
}

function showAllCounterDetails(&$counter_array)
{
    foreach ($counter_array as $value) {
        if ($value->get_current_serving() != null) {
            echo "\nAgent Number: " . $value->get_agent();
            echo "\nCurrently serving: " . $value->get_current_serving();
            echo "\nTime started: " . $value->get_timeCreated();
            echo "\nTime Left: " . time() - $value->get_timeCreated()/2;
            echo "\nJson format: " . $value->getJson(). "\n";
        }
    }
}

$counter1->set_agent("1821881");
$counter2->set_agent("1829957");
$counter3->set_agent("1820833");
$counter4->set_agent("1920218");
$counter5->set_agent("1711575");

do {
    echo "Welcome to Counter Line Up System\n";
    echo "1. Get Number\n2. Show Average waiting time\n3. Show waiting \n4. Show available Counters\n5. Show Counter details\n6. Enter 999 to exit\n\nEnter Choice: ";

    $option = fgets(STDIN);
    global $counter_array;


    switch ($option) {
        case 1: {
                $number = generate();
                echo "Your Number is: " . $number . "\n";
                assignToCounter();
                get_counter($number, $counter_array);
                echo "\n";
                break;
            }
        case 2: {
                if (get_avg_time($counter_array)) {
                    echo "Average waiting time is: " . get_avg_time($counter_array) . " minutes\n";
                }
                break;
            }
        case 3: {
                if (count($waiting) != 0) {
                    echo "waiting Q: ";
                    foreach ($waiting as $value) {
                        echo $value .= " ";
                    }
                    echo "\n";
                } else {
                    echo "The waiting Q is Empty\n\n";
                }
                break;
            }
        case 4: {
                show_avCounters($counter_array);
                break;
            }
        case 5: {
                showAllCounterDetails($counter_array);
                break;
            }
            default: echo "Choose a valid Options \n"; break;
    }
} while ($option != 999);
