<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class FeeAndPaymentFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Üniversitedeki eğitim hayatım boyunda üniversiteye girdiğim yıl ödediğim ücret üzerinden mi ödeme yaparım?" => "Üniversitemizde okuyan bir öğrenci yıllık ödemelerini her yıl ilan edilen ücrete göre öder ve ücretler her yıl Mütevelli Heyeti tarafından belirlenen oranda değişiklik göstermektedir. Bu oran genellikle yıllık enflasyon oranına eşittir.",
            "Ödeme şekilleriniz nelerdir?" => "Üniversitemizde eğitim ücretleri nakit veya anlaşmalı kredi kartlarına 9 taksit şeklinde ödenebilir. Ayrıca Denizbank’tan Eğitim Kredisi veya Kredili Mevduat Hesabı kullanılabilir. Anlaşmalı Kredi Kartları; Akbank, Denizbank, Halkbank, İş Bankası, Yapı Kredi Bankası, Vakıfbank, Tüm Axess Kartları, Tüm Bonus Kartları, Tüm Maximum Kartları, Tüm World Kartları, Finansbank, HSBC (Advantage)",
            "Çek veya senet ile ödeme yapılabiliyor mu?" => "Çek veya senet ile ödeme yapılmamaktadır."
        ];
        $questions = [];

        foreach($questionsRaw as $question => $answer)
        {
            $questions[] = (object)[
                "question" => $question,
                "answer" => $answer,
            ];
        }

        $buttons = [];
        foreach($questions as $qId => $question)
            $buttons[] = Button::create($question->question)->value($qId);

        $question = Question::create("Ücret ve Ödeme ile ilgili sıkça sorulan soruları listeliyorum.")
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->addButtons($buttons);
        $this->ask($question, function (Answer $answer) use ($questions) {
            if($answer->isInteractiveMessageReply())
            {
                $this->getBot()->typesAndWaits(2);
                if($questions[$answer->getValue()] != null)
                {
                    $question = $questions[$answer->getValue()];
                    $this->say("<b>{$question->question}</b><br>{$question->answer}");
                }
                $this->getBot()->typesAndWaits(5);
                $this->backOrAsk();
            }
            else
            {
                $this->getBot()->startConversation(new HelloConversation());
            }
        });
    }

    public function backOrAsk()
    {
        $question = Question::create("Bu konuda sormak istediğiniz başka bir soru var mı?")
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->addButtons([
                Button::create("Evet")->value(1),
                Button::create("Hayır")->value(0),
            ]);
        $this->ask($question, function (Answer $answer) {
            if($answer->isInteractiveMessageReply())
            {
                switch($answer->getValue())
                {
                    case 1:
                        $this->scholarship();
                        break;
                    case 0:
                        $this->getBot()->startConversation(new HelloConversation());
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
        $this->scholarship();
    }
}