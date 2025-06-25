<?php

namespace App\Http\Controllers;

use App\Services\FontneService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $fontneService;
    
    public function __construct(FontneService $fontneService)
    {
        $this->fontneService = $fontneService;
    }
    
    /**
     * Show the test page for Fonnte WhatsApp API
     */
    public function fontneTestPage()
    {
        return view('test-fonnte');
    }
    
    /**
     * Send a test message using Fonnte WhatsApp API
     */
    public function testFonnte()
    {
        try {
            $message = "🧪 *TEST MESSAGE* 🧪\n\n";
            $message .= "This is a test message from your Booking-Room-System.\n\n";
            $message .= "If you're seeing this message, the WhatsApp notification system is working correctly!\n\n";
            $message .= "Time sent: " . now()->format('d M Y H:i:s');
            
            $response = $this->fontneService->sendWhatsAppMessage(
                $this->fontneService->groupId,
                $message
            );
            
            if (!empty($response) && isset($response['status']) && $response['status']) {
                return response()->json([
                    'status' => true,
                    'message' => 'Test message sent successfully!',
                    'response' => $response
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'Failed to send message',
                    'response' => $response
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'response' => null
            ]);
        }
    }
    
    /**
     * Test sending a message with a clickable link
     */
    public function testFontneLink()
    {
        try {
            $response = $this->fontneService->sendTestMessageWithLink();
            
            if (!empty($response) && isset($response['status']) && $response['status']) {
                return response()->json([
                    'status' => true,
                    'message' => 'Test message with clickable link sent successfully!',
                    'response' => $response
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'Failed to send message',
                    'response' => $response
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'response' => null
            ]);
        }
    }
} 