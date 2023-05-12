<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class HelloConversation extends Conversation
{
    private function hello()
    {
        $this->getBot()->typesAndWaits(2);
        $this->say("Merhaba 👋, BAU Chatbot'a hoş geldin!");
        $this->getBot()->typesAndWaits(2);
        $this->ask('Adını ve soyadını öğrenebilir miyim?', function(Answer $answer) {
            $answerText = trim($answer->getText());
            if($answerText == "" || !preg_match("/^([a-zA-ZÖÇŞİĞÜöçşğüı\s]+)$/",$answerText))
            {
                $this->getBot()->typesAndWaits(2);
                $this->say("Adın ve soyadın doğru formatta değil.");
                $this->getBot()->typesAndWaits(2);
                return $this->repeat();
            }

            $this->getBot()->userStorage()->save([
                'fullname' => $answerText
            ]);

            $this->second();
        });
    }

    private function second()
    {
        $this->getBot()->typesAndWaits(2);

        $question = Question::create("Merhaba {$this->getBot()->userStorage()->get("fullname")}, bilgi almak istediğin konu seçebilirsin:")
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->callbackId('hello')
            ->addButtons([
                Button::create('ApplyBAU')->value('applybau'),
                Button::create('Burs')->value('burs'),
                Button::create('Ücret ve Ödeme')->value('ucret-ve-odeme'),
                Button::create('Yurt Dışı')->value('yurt-disi'),
                Button::create('Akademik')->value('akademik'),
                Button::create('İmkanlar')->value('imkanlar'),
                Button::create('Fiziki Koşullar')->value('fiziki-kosullar'),
                Button::create('Hazırlık')->value('hazirlik'),
            ]);
        $this->ask($question, function (Answer $answer) {
            if($answer->isInteractiveMessageReply())
            {
                switch($answer->getValue())
                {
                    case "burs":
                        $this->getBot()->startConversation(new ScholarshipFlow());
                        break;
                    case "ucret-ve-odeme":
                        $this->getBot()->startConversation(new FeeAndPaymentFlow());
                        break;
                    case "yurt-disi":
                        $this->getBot()->startConversation(new InternationalConnectionsFlow());
                        break;
                    case "akademik":
                        $this->getBot()->startConversation(new AcademicFlow());
                        break;
                    case "imkanlar":
                        $this->getBot()->startConversation(new FacilityFlow());
                        break;
                    case "fiziki-kosullar":
                        $this->getBot()->startConversation(new PhysicalEnvironmentFlow());
                        break;
                    case "hazirlik":
                        $this->getBot()->startConversation(new PreparatorySchoolFlow());
                        break;
                }
            }
            else
            {
                if(preg_match("/burs/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new ScholarshipFlow());
                }
                elseif(preg_match("/ücret|ödeme/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new FeeAndPaymentFlow());
                }
                elseif(preg_match("/yurt dışı/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new InternationalConnectionsFlow());
                }
                elseif(preg_match("/akademik/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new AcademicFlow());
                }
                elseif(preg_match("/imkanlar/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new FacilityFlow());
                }
                elseif(preg_match("/fiziki|fiziksel/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new PhysicalEnvironmentFlow());
                }
                elseif(preg_match("/hazırlık|prep/i",$answer->getValue()))
                {
                    $this->getBot()->startConversation(new PreparatorySchoolFlow());
                }
                else
                    $this->quitOrAsk();
            }
        });
    }

    public function quitOrAsk()
    {
        $this->getBot()->typesAndWaits(2);
        $this->getBot()->reply("Çözüm Merkezi için aramanız gereken telefon numarası: <b><a href=\"tel:+904442864\">444 2 864</a></b>");
        $this->getBot()->typesAndWaits(2);
        $question = Question::create("Yardımcı olabileceğim konuları tekrardan listelememi ister misiniz?")
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->callbackId('hello2')
            ->addButtons([
                Button::create("Evet")->value(1),
                Button::create("Hayır")->value(0),
            ]);
        $this->ask($question, function (Answer $answer) {
            if($answer->isInteractiveMessageReply())
            {
                $this->getBot()->typesAndWaits(2);
                switch($answer->getValue())
                {
                    case 1:
                        $this->hello();
                        break;
                    case 0:
                        $this->getBot()->typesAndWaits(2);
                        $this->getBot()->reply('Konuşma başlatmak için <b>merhaba</b> yazabilirsiniz. Çözüm merkezimizle görüşmek istiyorsanız <b><a href="tel:+904442864">444 2 864</a></b> numaralı telefondan bize ulaşabilirsiniz.');
                        break;
                }
            }
            else
            {
                $this->getBot()->startConversation(new HelloConversation());
            }
        });
    }

    public function run()
    {
        if($this->getBot()->userStorage()->get("fullname") == null)
            $this->hello();
        else
            $this->second();
    }
}