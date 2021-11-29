<?php

namespace App\Service;

use Exception;

//use DateTime;

class AdminStatService
{

    /**
     * Prend une liste de date et renvoi les dates dont c'est le centenaire
     *
     * @param array $dateList
     * @return array|bool
     */
    public function getCentenaryThisYear(array $dateList): array | bool
    {
        $centenaryElements = [];
        $currentYear = date('y');

        foreach ($dateList as $date) {
            $dateObj = new \DateTime($date);
            $dateYear = $dateObj->format('y');
            if ($currentYear === $dateYear) {
                $centenaryElements[] = $date;
            }
        }

        if (!empty($centenaryElements)) {
            return $centenaryElements;
        } else {
            return false;
        }
    }

    public function getAnniversary(array $dateList): array|bool
    {
        // renvoi des éléments dont c'est l'anniversaire
        $anniversaryElements = [];
        $today = date('m - d');

        foreach ($dateList as $date) {
            $dateObj = new \DateTime($date);
            $dateYear = $dateObj->format('m - d');
            if ($today === $dateYear) {
                $anniversaryElements[] = $date;
            }
        }

        if (!empty($anniversaryElements)) {
            return $anniversaryElements;
        } else {
            return false;
        }
    }

    public function strResearch(array $strings, string $text): string|bool
    {
        foreach ($strings as $string) {
            if (!str_contains($text, $string)) {
                throw new \Exception("Chaine non trouvée", 666);
            }
        }
        return $text;
    }

    public function commonWords(string $text1, string $text2): array|bool
    {
        // pour avoir les mots dans un tableau directement tu peux tenter str_word_count => https://www.php.net/manual/fr/function.str-word-count.php

        $explodedText1 = explode(' ', $text1);
        $explodedText2 = explode(' ', $text2);

        $wordArray1 = str_word_count($text1, 1, 0. . .3);
        $wordArray2 = str_word_count($text2, 1, 0. . .3);

        var_dump($explodedText1);
        var_dump($explodedText2);
        var_dump($wordArray1);
        var_dump($wordArray2);

        $characters = " ,.;";
        foreach ($explodedText1 as $string) {
            trim($string, $characters);
        }
        var_dump($explodedText1);


        foreach ($explodedText2 as $string) {
            trim($string, $characters);
        }
        var_dump($explodedText2);
        $commonStrings = array_intersect($explodedText1, $explodedText2);

        if (!empty($commonStrings)) {
            return $commonStrings;
        } else {
            return false;
        }
    }
}
