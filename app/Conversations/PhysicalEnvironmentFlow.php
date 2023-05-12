<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class PhysicalEnvironmentFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Spor salonunuz var mı?" => "Üniversitemizin ana yerleşkesinin Beşiktaş gibi merkezi bir yerde bulunmasından dolayı maalesef uygun alan bulunamadığından spor salonumuz yoktur. Dileyen öğrenciler Öğrencinin Konseyi’nin anlaşmalı olduğu Beşiktaş’ta bulunan spor salonlarından faydalanabilirler.",
            "Dersler kaç kişilik sınıflarda işleniyor?" => "Üniversitemizde sınıfların kapasitesi farklılık göstermektedir. Derslerin mevcutu da dersi seçen öğrencilere göre değişmektedir. Ortalama 60 kişilik sınıflarda eğitim görülüyor diyebiliriz. 1. sınıfta görülen mevcudun yüksek olduğu ortak dersler de sectionlara (bölümlere) ayrılarak daha küçük gruplarla işlenir.",
            "Bütün bölümleriniz Beşiktaş kampüsünde midir?" => "Üniversitemizin ana kampüsü ve bölümlerin çoğunluğu Beşiktaş’ta bulunmaktadır. İletişim Fakültesi Galata’da, Tıp Fakültesi Göztepe’de, Mimarlık Fakültesi, Sağlık Bilimleri Fakültesi ve Sağlık Hizmetleri Meslek Yüksekokulu Kuzey kampüs de bulunmaktadır.",
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

        $question = Question::create("Fiziksel ortam ile ilgili sıkça sorulan soruları listeliyorum:")
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->callbackId('scholarship1')
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
                $this->getBot()->typesAndWaits(3);
                $this->backOrAsk();
            }
            else
            {
                $this->backOrAsk();
            }
        });
    }

    public function backOrAsk()
    {
        $question = Question::create("Bu konuda sormak istediğiniz başka bir soru var mı?")
            ->fallback('Söylediğini anlayamadım. Başka bir şekilde sorabilir misin?')
            ->callbackId('scholarship2')
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