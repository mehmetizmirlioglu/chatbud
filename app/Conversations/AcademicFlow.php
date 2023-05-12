<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class AcademicFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Derslerin ne kadarı İngilizce işleniyor?" => "Bahçeşehir Üniversitesi’nin lisans bölümlerinde Sağlık Bilimleri Fakültesi’ne bağlı Türkçe eğitim veren bölümler dışındaki bölümlerde eğitim dilinin tamamı İngilizcedir ve öğrencilerin hazırlık sınıfını okumaları veya atlamaları gerekmektedir. [Sağlık Bilimleri Fakültesi öğrencileri isteğe bağlı olarak hazırlık sınıfı okuyabilirler] Hukuk Fakültesi’nde de hazırlık sınıfı zorunlu olup lisans derslerinin belirli bir kısmını İngilizce belirli bir kısmını ise Türkçe olarak işlenmektedir. Önlisans bölümlerinde ise eğitim dili Türkçe olup öğrenciler isteğe bağlı olarak hazırlık sınıfı okuyabilirler.",
            "Özel yetenek sınavıyla öğrenci alan bölümleriniz nelerdir?" => "Bahçeşehir Üniversitesi Çizgi Film ve Animasyon bölümüne özel yetenek sınavı ile öğrenci almaktadır.",
            "Derslerde devam zorunluluğu var mı?" => "Üniversitenin uygulamış olduğu genel bir devam zorunluluğu kuralı yoktur. Her dersin hocasının devam zorunluluğuna verdiği önem ve notlandırmaya etkisi değişkenlik gösterir.",
            "Dersler nasıl işleniyor? Ne kadar profesör var? Derslere asistanlar mı giriyor?" => "Derslerin tamamına o ders için görevlendirilmiş olan akademisyenler (profesör, doçent, yardımcı doçent veya doktor) girmektedir. Derslerin asistanları soru çözümlerinin yapıldığı ek derslere (P.S) ve dersin akademisyeninin derse gelemediği zorunlu durumlarda derslere girmektedir.",
            "BAU diploması hangi ülkelerde geçerli?" => "Bahçeşehir Üniversitesi YÖK’e bağlı bir üniversite olduğu için Türkiye’yi ve YÖK’ü tanıyan tüm ülkelerde diploması geçerlidir. Ayrıca üniversitemiz AKTS (Avrupa Kredi Transfer Sistemi) akreditasyonu almış bir üniversitedir. Üniversitemizden bu zaman kadar 121 farklı ülke vatandaşı öğrenci mezun olmuştur.",
            "Bahçeşehir Üniversitesinden mezun öğrencilerin yüksek lisans için bursları nelerdir?" => "Üniversitemizin lisans programlarından mezun olan öğrencilerimiz için yüksek lisans programlarında %50 burs uygulanmaktadır.",
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

        $question = Question::create("Akademik konular ile ilgili sıkça sorulan soruları listeliyorum:")
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