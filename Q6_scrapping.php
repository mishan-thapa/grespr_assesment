<?php

function getCurlContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
        return null;
    }

    curl_close($ch);
    return $response;
}

function getBookDetails($bookUrl) {
    $html = getCurlContent($bookUrl);
    if (!$html) {
        return null;
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // Extract details
    $bookName = $xpath->query("//h1")->item(0)->nodeValue ?? "";
    $price = $xpath->query("//p[contains(@class, 'price_color')]")->item(0)->nodeValue ?? "";
    $ratingClass = $xpath->query("//p[contains(@class, 'star-rating')]")->item(0)->getAttribute('class');
    $rating = str_replace("star-rating ", "", $ratingClass);
    $breadcrumbs = [];
    foreach ($xpath->query("//ul[@class='breadcrumb']/li/a") as $breadcrumb) {
        $breadcrumbs[] = trim($breadcrumb->nodeValue);
    }
    $breadcrumbsText = implode(" > ", $breadcrumbs);
    // print_r($breadcrumbsText);
    // die;

    $description = $xpath->query("//div[@id='product_description']/following-sibling::p")->item(0)->nodeValue ?? "";

    $productInfo = [];
    foreach ($xpath->query("//table[@class='table table-striped']//tr") as $row) {
        $key = trim($xpath->query(".//th", $row)->item(0)->nodeValue ?? "");
        $value = trim($xpath->query(".//td", $row)->item(0)->nodeValue ?? "");
        $productInfo[$key] = $value;
    }

    return array_merge([
        "Book Name" => $bookName,
        "Price" => $price,
        "Rating" => $rating,
        "Breadcrumbs" => $breadcrumbsText,
        "Product Description" => $description,
    ], $productInfo);
}

function getBooksFromPage($pageUrl) {
    $html = getCurlContent($pageUrl);
    if (!$html) {
        return [];
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($html); // surpasses some errors
    $xpath = new DOMXPath($dom);

    $bookUrls = [];
    foreach ($xpath->query("//h3/a") as $book) {
        $bookUrls[] = "https://books.toscrape.com/catalogue/" . $book->getAttribute('href');
    }

    $books = [];
    foreach ($bookUrls as $bookUrl) {
        echo "Scraping: $bookUrl\n";
        $bookDetails = getBookDetails($bookUrl);
        // print_r($bookDetails);
        // die;
        if ($bookDetails) {
            $books[] = $bookDetails;
        }
    }

    return $books;
}

function saveToCSV($data, $filename) {
    $fp = fopen($filename, 'w');

    if (count($data) > 0) {
        // Write the header
        fputcsv($fp, array_keys($data[0]));
        // Write the data
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
    }

    fclose($fp);
}

// Main function
function main() {
    $baseUrl = "https://books.toscrape.com/catalogue/page-%d.html";
    $allBooks = [];
    $file_name = "books_data.csv";

    for ($page = 1; $page <= 3; $page++) {
        $pageUrl = sprintf($baseUrl, $page);
        echo "Scraping page: $pageUrl\n";
        $pageBooks = getBooksFromPage($pageUrl);
        $allBooks = array_merge($allBooks, $pageBooks);
    }

    saveToCSV($allBooks, $file_name);
    echo "Data saved to books_data.csv\n";
}

main();
