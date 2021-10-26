<?php

namespace App\Service;

//use DateTime;

class DateService implements DateServiceInterface
{

    private $date;

    public function __construct()
    {
        // Le backslash sert à notifier qu'on se sert d'une classe native php - évite le use au dessus
        $this->date = new \DateTime();
    }

    public function getCurrentDay(): string
    {
        return $this->date->format('D-d-F-Y');
    }

    public function daysSinceNewYearsDay(): string
    {
        $origin = new \DateTime('first day of january');
        $target = $this->date;
        $interval = $origin->diff($target);
        return $interval->format('%R%a days');
    }
}
