<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram extends CI_Controller {

    private $botToken = '7009331882:AAHwB0cLcmH-RSoRabyAi_f6ZUP99WCCtow';

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // $this->load->model('Load_model', 'loader');
		// $this->loader->loadModels();
    }

	public function index() {
        $input = json_decode(file_get_contents('php://input'), true);
		file_put_contents(APPPATH . 'logs/telegram_debug.json', json_encode($input, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

        if (isset($input['message'])) {
            $chatId = $input['message']['chat']['id'];
            $messageText = $input['message']['text'];

            $reply = "You said: " . $messageText;
            $this->sendMessage($chatId, $reply);
        }
    }

    private function sendMessage($chatId, $message) {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        file_get_contents($url, false, $context);
    }


}
