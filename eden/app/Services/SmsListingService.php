<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsListingService
{
    public function parseMakeCommand(String $from, array $attributes)
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

    public function parseShowCommand(array $attributes)
    {
        // Expected format for attributes: of <produce: nullable> in <location: nullable> by <name> in <location>
        // example: of tomatoes in Laguna
        // example: by Juan
        // example: in Juan

        $showRequest = [
            'produce' => null,
            'location' => null,
            'farmer_name' => null,
        ];

        if (in_array('of', $attributes)) {
            $showRequest['produce'] = $attributes[array_search('of', $attributes) + 1] ?? null;
        }

        if (in_array('in', $attributes)) {
            $showRequest['location'] = $attributes[array_search('in', $attributes) + 1] ?? null;
        }

        if (in_array('by', $attributes)) {
            $showRequest['farmer_name'] = $attributes[array_search('by', $attributes) + 1] ?? null;
        }

        return $showRequest;
    }

    public function parseUpdateCommand(array $attributes)
    {
        // Expected format for attributes: <listing_id> UnitQuantity: <<quantity><unit>: nullable> price: <price: nullable> location: <location: nullable>
        // example: ListingId 12 UnitQuantity: 100kg price: 20php location: Laguna
        // example: ListingId 12 UnitQuantity: 9000g
        // example: ListingId 12 price: 20php location: Laguna
        // example: ListingId 12 location: Laguna

        $data = [];

        if (in_array('unitquantity:', $attributes)) {
            preg_match(
                '/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/',
                $attributes[array_search('unitquantity:', $attributes) + 1] ?? '',
                $quantityUnit
            );
            $data['quantity'] = intval($quantityUnit[1] ?? null);
            $data['unit'] = $quantityUnit[2] ?? null;
        }

        if (in_array('price:', $attributes)) {
            preg_match(
                '/^(\d+(?:\.\d+)?)([a-zA-Z]+)?$/',
                $attributes[array_search('price:', $attributes) + 1] ?? '',
                $priceCurrency
            );
            $data['price_per_unit'] = floatval($priceCurrency[1] ?? null);
        }

        if (in_array('location:', $attributes)) {
            $data['location'] = $attributes[array_search('location:', $attributes) + 1] ?? null;
        }

        $updateRequest = ["id" => $attributes[0], "data" => $data];

        return $updateRequest;
    }
}
