<?php
class Counter
{
    private $agent;
    private $current_serving = null;
    private $timeCreated;


    function __construct()
    {
        $this->timeCreated = time();
    }

    function set_agent($agent)
    {
        $this->agent = $agent;
    }
    function set_current_serving($current_serving, &$array, $index) // passing the array by refrence
    {
        $this->current_serving = $current_serving;
        $array[$index] = $current_serving;
    }

    function get_agent()
    {
        return $this->agent;
    }
    function get_current_serving()
    {
        return $this->current_serving;
    }

    // this function set the current serving to null if time exceed
    // we need cookie to implement this with ajax
    function get_timer($timeCreated, $index, &$current_serving)
    {
        if ($timeCreated - $this->timeCreated >= 300) //300sec == 5mins
        {
            $this->set_current_serving(null, $current_serving, $index);
            echo " " . $timeCreated - $this->timeCreated;
        }
    }

    function get_timeCreated()
    {
        return $this->timeCreated;
    }

    function getJson()
    {
        $array = array($this->get_agent(), $this->get_current_serving());
        return json_encode($array);
    }
}
