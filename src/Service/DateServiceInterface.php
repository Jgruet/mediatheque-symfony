<?php

namespace App\Service;


interface DateServiceInterface
{
    public function getCurrentDay(): string;
    public function daysSinceNewYearsDay(): string;
}
