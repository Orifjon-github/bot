<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        $endpoint = 'https://api.telegram.org/bot';
        $token = '<YOUR_BOT_TOKEN>';

        // Get the message text and chat ID from the request
        $text = $request->input('message.text');
        $chatId = $request->input('message.chat.id');

        // Check if the message is the /auto command
        if ($text == '/auto') {
            // Call the Telegram API to get the list of chat administrators
            $client = new Client(['base_uri' => $endpoint . $token . '/']);
            $response = $client->request('POST', 'getChatAdministrators', [
                'form_params' => [
                    'chat_id' => $chatId
                ]
            ]);

            // Loop through the list of chat administrators and get their user IDs
            $admins = json_decode($response->getBody(), true)['result'];
            $adminIds = array_map(function($admin) {
                return $admin['user']['id'];
            }, $admins);

            // Call the Telegram API to set the chat member status to "approved" for each administrator
            foreach ($adminIds as $adminId) {
                $client->request('POST', 'setChatMemberStatus', [
                    'form_params' => [
                        'chat_id' => $chatId,
                        'user_id' => $adminId,
                        'status' => 'member'
                    ]
                ]);
            }

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
            // Call the Telegram API to set the chat member status to "restricted" for each administrator
            foreach ($adminIds as $adminId) {
                $client->request('POST', 'setChatMemberStatus', [
                    'form_params' => [
                        'chat_id' => $chatId,
                        'user_id' => $adminId,
                        'status' => 'restricted'
                    ]
                ]);
            }

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
