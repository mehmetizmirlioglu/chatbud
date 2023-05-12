<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Attachments\Image;

class StartConversation extends Conversation
{
    private $first;

    public function startConversation()
    {
        $this->getBot()->typesAndWaits(2);

        $message = "Sana yardımcı olabilmem için butonları kullanabilir veya bana soru sorabilirsin.";
        if($this->first)
            $message = "Hey BAU adayı hoş geldin! Sana nasıl yardımcı olabilirim? Sana yardımcı olabilmem için butonları kullanabilir veya bana soru sorabilirsin.";

        $question = Question::create($message)
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->callbackId('start')
            ->addButtons([
                Button::create('ApplyBAU başvuru durumu sorgulama')->value('applybau-application-query'),
                Button::create('ApplyBAU başvurusu nasıl yapabilirim?')->value('applybau-application-info'),
                Button::create('ApplyBAU başvuru - sonuç takvimi')->value('applybau-calendar'),
            ]);

        $this->first = false;

        $this->ask($question, function (Answer $answer) {
            if($answer->isInteractiveMessageReply())
            {
                switch($answer->getValue())
                {
                    case "applybau-application-query":
                        $this->getBot()->typesAndWaits(2);
                        $this->getBot()->reply("Sana yardımcı olabilmek için bilgilerini almam gerekiyor.");
                        $this->getBot()->startConversation(new ApplyBAU);
                        break;
                    case "applybau-application-info":
                        $this->startConversation();
                        break;
                    case "applybau-calendar":
                        $this->applyBAUCalendar();
                        break;
                }
            }
            else
            {
            }
        });
    }

    private function applyBAUCalendar()
    {
        $this->getBot()->typesAndWaits(2);
        // Create attachment
        $attachment = new Image('https://applybau.com/img/ApplyBAU_Takvim_2023.jpg');

        // Build message object
        $message = OutgoingMessage::create('ApplyBAU Başvuru - Sonuç İlan Takvimi için <a href="https://applybau.com/basvuru-sonuc-ilan-takvimi" target="_blank">https://applybau.com/basvuru-sonuc-ilan-takvimi</a> adresini ziyaret edebilirsiniz.')
            ->withAttachment($attachment);

        // Reply message object
        $this->say($message);
        $this->startConversation();
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->first = true;
        $this->startConversation();
    }
}
