<?php

namespace App\Http\Controllers;

use App\Services\ProduceService;
use App\Services\SmsListingService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class SmsController extends Controller
{
    protected $twilio;
    protected $produceService;
    protected $smsListingService;

    public function __construct(TwilioService $twilio, ProduceService $produceService, SmsListingService $smsListingService)
    {
        $this->twilio = $twilio;
        $this->produceService = $produceService;
        $this->smsListingService = $smsListingService;
    }

    protected function controlListings($from, $command, $attributes)
    {
        if ($command === 'make') {
            // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
            // example: tomatoes 100kg 20php Laguna listed by Juan

            $listingData = $this->smsListingService->parseMakeCommand($from, $attributes);
            $response = $this->produceService->createListing($listingData);

            return $response;
        }

        if ($command === 'show') {
            // Expected format for attributes: <produce> <location: nullable> by <name: nullable>
            // example: tomatoes Laguna by Juan

            $showRequest = $this->smsListingService->parseShowCommand($attributes);

        }
    }

    public function incoming(Request $request)
    {

        $from = $request->input('From');

        $body = strtolower($request->input('Body'));

        Log::info("=== SMS Conversation Start ===");

        // Expected format: command listing attributes
        // example: make listing tomatoes 100kg 20php Laguna listed by Juan
        $words = explode(' ', $body);
        $tokens = [
            'command' => $words[0] ?? null,
            'attributes' => array_slice($words, 2) ?? null,
        ];
        $senderName = end($tokens['attributes']);

        Log::info("$from ($senderName): $body");

        if (str_contains($body, 'listing')) {
            $response = $this->controlListings($from, $tokens['command'], $tokens['attributes']);
            if (!$response['success']) {
                Log::error("Eden: Failed to create listing for farmer $from");
                Log::error("=== SMS Conversation End ===");
                return;
            }
            $listing = $response['listing'];
            $message = <<<EOT
            Listing created successfully!
                Produce: {$listing->produce}
                Quantity: {$listing->quantity} {$listing->unit}
                Price per unit: {$listing->price_per_unit}
                Location: {$listing->location}
                Farmer Name: {$listing->farmer_name}
                Farmer Phone: {$listing->farmer_phone}
            EOT;
            $response = new MessagingResponse();
            $response->message($message);
            foreach (explode("\n", $message) as $line) {
                Log::info("Eden: $line");
            }
            Log::info("=== SMS Conversation End ===");
            return response($response)->header('Content-Type', 'text/xml');
        }
    }
}
