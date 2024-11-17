<?php
// Q1. Given an integer array of size greater than 3, find the third largest integer in the array without using sort operation.
function getThirdLargest($numbers) {

    if (count($numbers) < 3) {
        return "The array must contain at least three elements.";
    }

    $max1 = $max2 = $max3 = null;

    foreach ($numbers as $value) {
        if ($value === $max1 || $value === $max2 || $value === $max3) {
            continue;
        }

        if ($max1 === null || $value > $max1) {
            $max3 = $max2;
            $max2 = $max1;
            $max1 = $value;
        } elseif ($max2 === null || $value > $max2) {
            $max3 = $max2;
            $max2 = $value;
        } elseif ($max3 === null || $value > $max3) {
            $max3 = $value;
        }
    }

    return $max3;
}

// Example 
$inputArray = [5,1,3,2,4];
echo "The third largest number is: " . getThirdLargest($inputArray);
?>
