<?php
$num = (int) readline("Enter a number: ");
$original = $num;
$reverse = 0;

while ($num > 0)
 {
    $digit = $num % 10;
    $reverse = ($reverse * 10) + $digit;
    $num = (int)($num / 10);
}

if ($original == $reverse) {
    echo "$original is a Palindrome Number";
} else {
    echo "$original is NOT a Palindrome Number";
}
?>