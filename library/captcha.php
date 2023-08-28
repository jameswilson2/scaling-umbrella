<?php
class Captcha {
    public static function generateQuestion() {
        $value1 = rand(1, 10);
        $value2 = rand(1, 10);
        $answer = $value1 + $value2;
        $question = "$value1 + $value2";
        
        return [
            "question" => $question,
            "answer" => $answer
        ];
    }
}
?>
