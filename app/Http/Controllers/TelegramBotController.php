<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        $endpoint = 'https://api.telegram.org/bot';
        $token = '6137134311:AAESLLeHd4z3lhIXZcQrIQ51dqG3GaX4Clg';

        // Get the message text and chat ID from the request
        $text = $request->input('message.text');
        $chatId = $request->input('message.chat.id');

        if ($text == '/start') {
            // Call the Telegram API to get the list of chat administrators
            $client = new Client(['base_uri' => $endpoint . $token . '/']);
            $message = 'Test Working';
            $client->request('POST', 'sendMessage', [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            ]);
        }

        // Check if the message is the /auto command
        if ($text == '/auto') {
            // Call the Telegram API to get the list of chat members
            $client = new Client(['base_uri' => $endpoint . $token . '/']);
            $response = $client->request('POST', 'getChatAdministrators', [
                'form_params' => [
                    'chat_id' => $chatId,
                ]
            ]);

            // Get the list of chat admins and their user IDs
            $admins = json_decode($response->getBody()->getContents(), true)['result'];
            $adminIds = array_column($admins, 'user', 'status');

            // Check if the bot is an admin of the channel
            if (array_key_exists($chatId, $adminIds) && in_array($token, $adminIds)) {
                // Call the Telegram API to set the chat member status to "approved"
                $response = $client->request('POST', 'setChatMemberStatus', [
                    'form_params' => [
                        'chat_id' => $chatId,
                        'user_id' => $request->input('message.from.id'),
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
            } else {
                // Send an error message if the bot is not an admin of the channel
                $message = 'Error: bot is not an admin of the channel.';
                $client->request('POST', 'sendMessage', [
                    'form_params' => [
                        'chat_id' => $chatId,
                        'text' => $message
                    ]
                ]);
            }
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
