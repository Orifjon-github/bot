<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        // Set the Telegram API endpoint and bot token
        $endpoint = 'https://api.telegram.org/bot';
        $token = '6137134311:AAESLLeHd4z3lhIXZcQrIQ51dqG3GaX4Clg';

        // Get the message text and chat ID from the request
        $text = $request->input('message.text');
        $chatId = $request->input('message.chat.id');

        // Check if the message is the /auto command
        if ($text == '/auto') {
            // Call the Telegram API to set the chat member status to "approved"
            $client = new Client(['base_uri' => $endpoint . $token . '/']);
            $response = $client->request('POST', 'setChatMemberStatus', [
                'form_params' => [
                    'chat_id' => $chatId,
                    'user_id' => '298410462',
                    'status' => 'member'
                ]
            ]);

            // Send a confirmation message to the chat
            $message = 'Automatic membership approval is now enabled.';
            $client->request('POST', 'sendMessage', [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            ]);
        }

        // Check if the message is the /noauto command
        if ($text == '/noauto') {
            // Call the Telegram API to set the chat member status to "restricted"
            $client = new Client(['base_uri' => $endpoint . $token . '/']);
            $response = $client->request('POST', 'setChatMemberStatus', [
                'form_params' => [
                    'chat_id' => $chatId,
                    'user_id' => '298410462',
                    'status' => 'restricted'
                ]
            ]);

            // Send a confirmation message to the chat
            $message = 'Automatic membership approval is now disabled.';
            $client->request('POST', 'sendMessage', [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            ]);
        }
    }
}
