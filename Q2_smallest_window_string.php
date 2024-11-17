<?php
// Q2. Given two strings s and t, return the minimum window substring of s that contains all characters of t (including duplicates).
function findMinWindow($source, $target) {
    $sourceLen = strlen($source);
    $targetLen = strlen($target);

    if ($sourceLen < $targetLen) {
        return "";
    }

    $requiredCounts = array_count_values(str_split($target));
    $currentWindow = [];

    $left = 0;
    $shortestWindowLength = PHP_INT_MAX;
    $resultStart = 0;
    $matchedChars = 0;

    for ($right = 0; $right < $sourceLen; $right++) {
        $currentChar = $source[$right];
        $currentWindow[$currentChar] = ($currentWindow[$currentChar] ?? 0) + 1;

        if (isset($requiredCounts[$currentChar]) && $currentWindow[$currentChar] === $requiredCounts[$currentChar]) {
            $matchedChars++;
        }

        while ($matchedChars === count($requiredCounts)) {
            $windowSize = $right - $left + 1;

            // Update result if a smaller window is found
            if ($windowSize < $shortestWindowLength) {
                $shortestWindowLength = $windowSize;
                $resultStart = $left;
            }

            $leftChar = $source[$left];
            $currentWindow[$leftChar]--;

            if (isset($requiredCounts[$leftChar]) && $currentWindow[$leftChar] < $requiredCounts[$leftChar]) {
                $matchedChars--;
            }

            $left++;
        }
    }

    // Return the smallest valid window or an empty string if no such window exists
    return $shortestWindowLength === PHP_INT_MAX ? "" : substr($source, $resultStart, $shortestWindowLength);
}

// Example usage
$source = "ADOBECODEBANC";
$target = "ABC";
echo "Minimum window substring: " . findMinWindow($source, $target);
?>
