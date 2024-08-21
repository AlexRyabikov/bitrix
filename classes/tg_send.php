<?
//Простой класс для отправки отбоек к TG

namespace Telegram;

if (!class_exists('Telegram\Telegram')) {
    class Telegram
    {
        private $token = "your_tg_token";
        private $chat_id = TG_DEFAULT_CHAT;
        private $url = "https://api.telegram.org/bot{token}/sendMessage?chat_id={chat_id}&parse_mode=html&text={txt}";

        public function send ($text, $chat=''){
            $chatId = $this->chat_id;
            if(strlen($chat)>0){
                $chatId = $chat;
            }
            
            $encode_text = urlencode($text);
            $target = str_replace(['{token}', '{chat_id}', '{txt}'], [$this->token, $chatId, $encode_text], $this->url);
            return fopen($target,"r");
        }
    }
}

//Пример вызова:
//$telegram = new \Telegram\Telegram();
//$telegram->send($message, "CHAT_ID");