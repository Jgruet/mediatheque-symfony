<?php

namespace App\Service;

//use DateTime;

class DateServiceCustomizable implements DateServiceInterface
{

    private $date;
    private $origin;
    // paramètre $string du constructeur passé dans le services.yaml (défini dans parameters en tant que constante d'application)
    public function __construct(string $string)
    {
        // Le backslash sert à notifier qu'on se sert d'une classe native php - évite le use au dessus
        $this->date = new \DateTime();
        $this->origin = new \DateTime($string);
    }

    public function getCurrentDay(): string
    {
        return $this->date->format('D-d-F-Y');
    }

    public function daysSinceNewYearsDay(): string
    {
        $target = $this->date;
        $interval = $this->origin->diff($target);
        return $interval->format('%R%a days');
    }
}
