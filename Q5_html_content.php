<?php

function fetch_table_data($url) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);

    // Load the URL's contents into the DOM
    if (!$dom->loadHTMLFile($url)) {
        throw new Exception("Failed to load HTML content from the URL.");
    }

    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $rows = $xpath->query("//table/tr");

    if ($rows->length === 0) {
        throw new Exception("No table rows found in the HTML content.");
    }

    $tableData = [];
    foreach ($rows as $index => $row) {
        if($index === 0){
            continue;
        }
        $cells = $row->getElementsByTagName('td');
        if ($cells->length === 3) { // Ensure the row has 3 columns
            $tableData[] = [
                'Company' => trim($cells->item(0)->textContent),
                'Contact' => trim($cells->item(1)->textContent),
                'Country' => trim($cells->item(2)->textContent),
            ];
        }
    }

    return $tableData;
}

function save_to_csv($tableData, $fileName) {
    if (empty($tableData)) {
        throw new Exception("No data available to write to CSV.");
    }

    $file = fopen($fileName, 'w');
    if ($file === false) {
        throw new Exception("Unable to open or create the CSV file.");
    }

    // Write headers
    fputcsv($file, array_keys($tableData[0]));

    // Write table data
    foreach ($tableData as $row) {
        fputcsv($file, $row);
    }

    fclose($file);
}

function main() {
    $url = "https://bitbucket.org/!api/2.0/snippets/grepsr/nE754R/ed2c70b738942f466ef75e0ca8a72a28556b3b80/files/tables.html";
    $csvFileName = "table_data.csv";

    try {
        $tableData = fetch_table_data($url);
        save_to_csv($tableData, $csvFileName);
        echo "Data successfully written to $csvFileName.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

main();

?>
