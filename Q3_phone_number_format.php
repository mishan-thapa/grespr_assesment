<?php
function normalizePhoneNumbers($inputText) {
    $resultList = [];

    // phone number formats
    $regex = '/(\+1[\s-]?)?(\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4})/';

    // Extract matches
    preg_match_all($regex, $inputText, $foundMatches);

    foreach ($foundMatches[0] as $potentialNumber) {
        // Remove any "+1" prefix
        $cleanedNumber = preg_replace('/^\+1[\s-]?/', '', $potentialNumber);

        // Extract only digits
        $digitsOnly = preg_replace('/\D/', '', $cleanedNumber);

        if (strlen($digitsOnly) === 10) {
            // structure
            $formattedNumber = '(' . substr($digitsOnly, 0, 3) . ') ' 
                               . substr($digitsOnly, 3, 3) . '-' 
                               . substr($digitsOnly, 6);

            $resultList[] = $formattedNumber;
        }
    }

    return $resultList;
}

// Example
$exampleInput = "numbers: +1 (123) 456-7890, 123-456-7890, 123.456.7890, 123 456 7890, and invalid numbers: 12345.";
$phoneNumbers = normalizePhoneNumbers($exampleInput);

print_r($phoneNumbers);
?>
