<?php

namespace App;

require 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;

class Scrape
{
    private array $products = [];
    private string $productPage = 'https://www.magpiehq.com/developer-challenge/smartphones';

    /**
     * Runs the scrape
     *
     * Fetches the initial page, extracts products from it, then crawls the pagination
     * and saves the output to a file named 'output.json'
     */
    public function run(): void
    {
        $document = ScrapeHelper::fetchDocument($this->productPage);

        $this->extractProducts($document);

        $this->crawlePagination($document);

        file_put_contents('output.json', json_encode(array_values($this->products), JSON_PRETTY_PRINT));

        print_r('Scrap successful, ' . count($this->products) . ' products fetched.');
    }

    /**
     * Crawls the pagination of the product pages
     *
     * Given a $document from the first page, it will try to extract the last page number
     * from the pagination. If it can't find one, it will assume there is only one page.
     * If there is more than one page, it will loop through the pages and call extractProducts
     * on each page.
     *
     * @param Crawler $document The document of the first page
     */
    private function crawlePagination(Crawler $document): void
    {
        $lastPage = 1;
        try {
            $lastPage = (int) $document->filter('#pages > div')->children()->last()->text();
        } catch (\InvalidArgumentException $exception) {
            // no needed there is only one page.
        }


        if ($lastPage > 1) {
            for ($i = 2; $i <= $lastPage; $i++) {
                $document = ScrapeHelper::fetchDocument($this->productPage . '?page=' . $i);
                $this->extractProducts($document);
            }
        }
    }

    /**
     * Extracts all products from a given document
     *
     * It takes a document, filters out all the products, and then loops through each product.
     * For each product it will loop through all the colors and get the product details.
     * The product details will then be stored in the $this->products array with a unique key.
     * The unique key is the color and the product title concatenated together.
     *
     * @param Crawler $document The document to extract the products from
     */
    private function extractProducts(Crawler $document): void
    {
        $products = $document->filter('#products > div')->children('.product');

        foreach ($products as $product) {
            $product = new Crawler($product);

            $colors = $product->filter('.border.border-black.rounded-full.block');

            foreach ($colors as $color) {
                $color = new Crawler($color);

                $productDetail = (new Product())->extractProductDetails($product, $color);

                $uniqueKey = trim($productDetail['colour']) . trim($productDetail['title']);

                $this->products[$uniqueKey] = $productDetail;
            }
        }
    }
}

$scrape = new Scrape();
$scrape->run();
