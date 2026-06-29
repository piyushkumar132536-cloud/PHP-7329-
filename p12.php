<?php
function adder(&$str2)
{
   $str2.=" world";

}
$str1="Hello";
adder($str1);
echo $str1;


?>