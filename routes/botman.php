<?php

use App\Http\Controllers\BotManController;
use BotMan\BotMan\BotMan;

/** @var BotMan $botman */
$botman = resolve('botman');

$answers = json_decode(file_get_contents(__DIR__ . '/botman_answers.json'));

foreach($answers as $answer)
{
    $botman->hears($answer->message_regex, function(BotMan $bot) use ($answer) {
        if(isset($answer->response_text))
        {
            $bot->typesAndWaits(2);
            $bot->reply($answer->response_text);
        }
        if(isset($answer->conversation))
        {
            $conversationClass = "\App\Conversations\\" . $answer->conversation;
            $bot->startConversation(new $conversationClass());
        }
    })->skipsConversation();
}

$botman->hears('merhaba|selam', BotManController::class.'@startConversation');

$botman->fallback(function($bot) {
    $bot->reply('Konuşma başlatmak için <b>merhaba</b> yazabilirsiniz. Çözüm merkezimizle görüşmek istiyorsanız <b><a href="tel:+904442864">444 2 864</a></b> numaralı telefondan bize ulaşabilirsiniz.');
});