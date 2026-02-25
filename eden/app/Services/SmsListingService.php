<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsListingService
{
    public function parseMakeCommand($from, $attributes)
    {
        // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
        // example: tomatoes 100kg 20php Laguna listed by Juan

        // Log::info("Parsing listing attributes: " . json_encode($attributes));
        preg_match('/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/', $attributes[1] ?? '', $quantityUnit);
        preg_match('/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/', $attributes[2] ?? '', $priceCurrency);

        $listingData = [
            'farmer_phone' => $from,
            'produce' => $attributes[0] ?? null,
            'quantity' => intval($quantityUnit[1] ?? 0),
            'unit' => $quantityUnit[2] ?? null,
            'price_per_unit' => floatval($priceCurrency[1] ?? 0),
            'location' => $attributes[3] ?? null,
            'farmer_name' => end($attributes) ?? null,
        ];

        // Log::info("Parsed listing data: " . json_encode($quantityUnit));
        Log::info("Creating listing: " . json_encode($listingData));
        return $listingData;
    }

    public function parseShowCommand($attributes)
    {
        // Expected format for attributes: <produce> <location: nullable> || by <name>
        // example: tomatoes Laguna
        // example: by Juan

        if (in_array('by', $attributes)) {
            $nameIndex = array_search('by', $attributes) + 1;
            $showRequest = ['farmer_name' => $attributes[$nameIndex] ?? null];
        }

        $showRequest = [
            'produce' => $attributes[0] ?? null,
            'location' => $attributes[1] ?? null,
        ];

        return $showRequest;
    }
}
