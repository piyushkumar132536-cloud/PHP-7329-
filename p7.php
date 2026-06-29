<?php
function adder(&$str2)
{
   $str2+=10;

}
$str1=90;
adder($str1);
echo $str1;


?>