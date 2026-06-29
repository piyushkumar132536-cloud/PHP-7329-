<?php
$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
echo "<h3>Numeric Array (Days of the Week)</h3>";
foreach ($days as $day) {
    echo $day . "<br>";
}


$months = array(
    "January" => 31,
    "February" => 28,
    "March" => 31,
    "April" => 30,
    "May" => 31,
    "June" => 30,
    "July" => 31,
    "August" => 31,
    "September" => 30,
    "October" => 31,
    "November" => 30,
    "December" => 31
);
echo "<h3>Associative Array (Months and Days)</h3>";
foreach ($months as $month => $days) {
    echo "$month => $days days<br>";
}
$laptops = array(
    "Dell" => array(
        "Model" => "Inspiron 15",
        "Price" => "55,000"
    ),
    "HP" => array(
        "Model" => "Pavilion x360",
        "Price" => "62,000"
    )
);
echo "<h3>Multidimensional Array (Laptop Details)</h3>";
foreach ($laptops as $company => $details) {
    echo "<b>Company:</b> $company<br>";
    echo "Model: " . $details["Model"] . "<br>";
    echo "Price: " . $details["Price"] . "<br><br>";
}
?>