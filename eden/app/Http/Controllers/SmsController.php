<?php

namespace App\Http\Controllers;

use App\Models\SmsConversation;
use App\Services\ProduceService;
use App\Services\SmsConversationService;
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
    protected $smsConversationService;

    public function __construct(
        TwilioService $twilio,
        ProduceService $produceService,
        SmsListingService $smsListingService,
        SmsConversationService $smsConversationService,
    ) {
        $this->twilio = $twilio;
        $this->produceService = $produceService;
        $this->smsListingService = $smsListingService;
        $this->smsConversationService = $smsConversationService;
    }

    public function incoming(Request $request)
    {
        $from = $request->input('From');

        $body = strtolower($request->input('Body'));

        $conversation = SmsConversation::where('farmer_phone', $from)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (is_null($conversation)) {
            Log::info("=== SMS Conversation Start ===");
        } else {
            $action = $conversation->action;
            $data = $conversation->data;
            $userResponse = $body;

            $conversationResponse = $this->smsConversationService->controlConversations($conversation, $userResponse, $action, $data);

            $message = $conversationResponse['message'] ?? "Sorry, we couldn't process your response.";

            $response = [
                'success' => $conversationResponse['success'],
                'message' => $message,
            ];
        }

        // Expected format: command listing attributes
        // example: make listing tomatoes 100kg 20php Laguna listed by Juan
        $words = explode(' ', $body);
        $tokens = [
            'command' => $words[0] ?? null,
            'attributes' => array_slice($words, 2) ?? null,
        ];


        if (
            str_contains($body, 'listing') ||
            str_contains($body, 'listings') ||
            str_contains($body, 'listingid')
        ) {
            $listingResponse = $this->smsListingService->controlListings($from, $tokens['command'], $tokens['attributes']);
            if (!$listingResponse['success'] || $listingResponse['success'] != 'pending') {
                Log::error("Eden: Failed to process command for farmer $from");
                Log::error("=== SMS Conversation End ===");
            }

            $message = $listingResponse['message'] ?? "Sorry, we couldn't process your request. Please check your command format and try again.";

            $response = [
                'success' => $listingResponse['success'],
                'message' => $message,
            ];
        }

        $reply = new MessagingResponse();
        $reply->message($message);
        foreach (explode("\n", $message) as $line) {
            Log::info("Eden: $line");
        }
        if (is_bool($response['success'])) {
            Log::info("=== SMS Conversation End ===");
        }
        return response($reply)->header('Content-Type', 'text/xml');
    }
}
