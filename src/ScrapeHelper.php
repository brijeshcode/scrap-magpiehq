<?php

namespace App;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeHelper
{
    public static function fetchDocument(string $url): Crawler
    {
        try {
            $client = new Client();

            $response = $client->get($url);

            return new Crawler($response->getBody()->getContents(), $url);
        } catch (Exception $exception) {
            throw new Exception("Failed to fetch document at url: $url. " . $exception->getMessage());
        }
    }

    /**
     * Converts a given capacity string (e.g. "64GB", "128MB") into an integer in MB
     *
     * @param string $capacity
     *
     * @return int
     */
    public static function convertToMB(string $capacity): int
    {
        return (int) filter_var($capacity, FILTER_SANITIZE_NUMBER_INT) * (strtolower(substr($capacity, -2)) === 'gb' ? 1024 : 1);
    }

    /**
     * Extracts a date string from a given text
     *
     * This method takes a string and looks for a date in one of the following formats:
     * - "21st Sep 2024"
     * - "2024-09-20"
     * - "19 Sep 2024"
     * - "tomorrow"
     *
     * If a date is found, it is returned as a string in the format "Y-m-d". If no date is found, null is returned
     *
     * @param string $text
     *
     * @return string|null
     */
    public static function extractDateFromText(string $text): string|null
    {
        // Define patterns for different date formats
        $patterns = [
            '/\b(\d{1,2}(?:st|nd|rd|th)?\s+\w+\s+\d{4})\b/', // Matches "21st Sep 2024"
            '/\b(\d{4}-\d{2}-\d{2})\b/', // Matches "2024-09-20"
            '/\b(\d{1,2}\s+\w+\s+\d{4})\b/', // Matches "19 Sep 2024"
            '/\b(tomorrow)\b/i', // Matches "tomorrow"
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $dateString = $matches[1];

                // Handle "tomorrow" case
                if (strtolower($dateString) === 'tomorrow') {
                    return (new DateTime('tomorrow'))->format('Y-m-d');
                }

                try {
                    // Attempt to create a DateTime object
                    $date = new DateTime($dateString);

                    return $date->format('Y-m-d');
                } catch (Exception $e) {
                    // Handle exception if date format is invalid
                    continue;
                }
            }
        }

        return null; // Return null if no date is found
    }
}
