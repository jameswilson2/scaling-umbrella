<?php
class Captcha {
    public static function generateQuestion() {
        $value1 = rand(1, 10);
        $value2 = rand(1, 10);
        $operator = rand(0, 1); // 0 represents addition, 1 represents subtraction

        // Ensure that value2 is not greater than value1 for subtraction
        if ($operator === 1 && $value2 > $value1) {
            [$value1, $value2] = [$value2, $value1]; // Swap values
        }

        $answer = ($operator === 0) ? $value1 + $value2 : $value1 - $value2;

        // Generate question string
        $question = $value1 . ($operator === 0 ? " + " : " - ") . ($value2 > 10 ? $value2 : self::numberToWords($value2));
        
        return [
            "question" => $question,
            "answer" => $answer
        ];
    }

    public static function numberToWords($number) {
        $numberWords = [
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten'
        ];
        
        return $numberWords[$number];
    }
}
?>
