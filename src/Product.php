<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

class Product
{
    private string $baseUrl = 'https://www.magpiehq.com/developer-challenge';

    public function extractProductDetails(Crawler $product, Crawler $color): array
    {
        return [
            'title' => $this->extractTitle($product),
            'price' => $this->extractPrice($product),
            'imageUrl' => $this->extractImage($product),
            'capacityMB' => $this->extractCapacity($product),
            'colour' => $this->extractColour($color),
            'availabilityText' => $this->extractAvailabilityText($product),
            'isAvailable' => $this->checkAvailability($product),
            'shippingText' => $this->extractShippingText($product),
            'shippingDate' => $this->extractShippingDate($product),
        ];
    }

    private function extractTitle(Crawler $productElement): string
    {
        return $productElement->filter('h3')->text();
    }

    private function extractColour(Crawler $colourElement): string
    {
        $color = $colourElement->attr('data-colour');

        return is_null($color) ? 'N/A' : $color;
    }

    private function extractPrice(Crawler $productElement): float
    {
        return (float) ltrim($productElement->filter('.my-8.block.text-center.text-lg')->text(), '£');
    }

    private function extractShippingDate(Crawler $productElement): string
    {
        $shippingText = $this->extractShippingText($productElement);

        $date = ScrapeHelper::extractDateFromText($shippingText);

        return is_null($date) ||  strtotime($date) === false ? 'N/A' : (string) date('Y-m-d', strtotime($date)) ;
    }

    private function extractImage(Crawler $productElement): string
    {
        $imgSrc = $productElement->filter('img')->first()->attr('src');

        return is_null($imgSrc) ? 'N/A' : $this->baseUrl . ltrim($imgSrc, '.');
    }

    private function extractCapacity(Crawler $productElement): int
    {
        $capacity = $productElement->filter('h3 > .product-capacity')->text();

        return ScrapeHelper::convertToMB($capacity);
    }

    private function extractAvailabilityText(Crawler $productElement): string
    {
        $availabilityAndShipping = $productElement->filter('.my-4.text-sm.block.text-center');

        return ltrim($availabilityAndShipping->first()->text(), 'Availability: ');
    }

    private function checkAvailability(Crawler $productElement): bool
    {
        return str_contains($this->extractAvailabilityText($productElement), 'In Stock');
    }

    private function extractShippingText(Crawler $productElement): string
    {
        $shippingText = $productElement->filter('.my-4.text-sm.block.text-center');

        return $shippingText->count() > 1 ? $shippingText->last()->text() : 'N/A';
    }
}
