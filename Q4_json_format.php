<?php

function fetchAndSaveProductDataWithCurl() {
    $apiUrl = "https://dummyjson.com/products/search?q=Laptop";
    $csvFileName = "laptop.csv";

    try {
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception("cURL error: " . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception("API returned a non-200 status code: $httpCode");
        }

        curl_close($ch);

        $data = json_decode($response, true);

        // check if products exist in the response
        if (!isset($data['products']) || !is_array($data['products'])) {
            throw new Exception("No products found in the API response.");
        }

        $productDetails = [];
        foreach ($data['products'] as $product) {
            $productDetails[] = [
                'Title' => $product['title'],
                'Price' => $product['price'],
                'Brand' => $product['brand'],
                'Product SKU' => $product['sku'],
            ];
        }

        // Write to CSV
        $file = fopen($csvFileName, 'w');
        if ($file === false) {
            throw new Exception("Unable to open or create the CSV file.");
        }

        // Write headers
        fputcsv($file, array_keys($productDetails[0]));

        // Write product details
        foreach ($productDetails as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
        echo "Data successfully written to $csvFileName.";

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

fetchAndSaveProductDataWithCurl();
