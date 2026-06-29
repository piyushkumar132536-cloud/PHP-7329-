<?php
$month = date("n");   // Current month number

// Using if-else
echo "<h3>Using If-Else:</h3>";

if($month == 1)
    echo "January";
else if($month == 2)
    echo "February";
else if($month == 3)
    echo "March";
else if($month == 4)
    echo "April";
else if($month == 5)
    echo "May";
else if($month == 6)
    echo "June";
else if($month == 7)
    echo "July";
else if($month == 8)
    echo "August";
else if($month == 9)
    echo "September";
else if($month == 10)
    echo "October";
else if($month == 11)
    echo "November";
else
    echo "December";

echo "<br><br>";

// Using switch-case
echo "<h3>Using Switch-Case:</h3>";

switch($month)
{
    case 1: echo "January"; break;
    case 2: echo "February"; break;
    case 3: echo "March"; break;
    case 4: echo "April"; break;
    case 5: echo "May"; break;
    case 6: echo "June"; break;
    case 7: echo "July"; break;
    case 8: echo "August"; break;
    case 9: echo "September"; break;
    case 10: echo "October"; break;
    case 11: echo "November"; break;
    case 12: echo "December"; break;
    default: echo "Invalid Month";
}
?>