<?php

namespace App\Services;

use App\Models\SmsConversation;
use App\Services\ProduceService;
use Illuminate\Support\Facades\Log;

class SmsConversationService
{
    protected $produceService;

    public function __construct(ProduceService $produceService)
    {
        $this->produceService = $produceService;
    }

    protected function deleteListingConversation(String $userResponse, array $data)
    {
        if ($userResponse === 'yes') {
            $this->produceService->deleteListing($data['listing_id']);
            $message = "ListingId {$data['listing_id']} deleted successfully.";
        } elseif ($userResponse === 'no') {
            $message = "Deletion of ListingID {$data['listing_id']} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    public function controlConversations(SmsConversation $conversation, String $userResponse, String $action, array $data)
    {
        Log::info("----------------------------");
        Log::info("user: $userResponse");
        Log::info("----------------------------");

        if ($action === 'delete_listing') {
            $conversationResponse = $this->deleteListingConversation($userResponse, $data);
        }

        if ($conversationResponse['success']) {
            $conversation->update(['status' => 'completed']);
        } elseif ($conversationResponse['success'] === false) {
            $conversationResponse['success'] = 'pending';
        }

        return $conversationResponse ?? [
            'success' => false,
            'message' => "Invalid conversation action.",
        ];
    }
}
