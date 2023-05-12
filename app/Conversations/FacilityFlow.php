<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class FacilityFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Mezuniyet sonrası öğrencilerinize iş bulma anlamında yardımınız bulunuyor mu?" => "Öğrencilerimizi uyguladığımız CO-OP programı sayesinde daha öğrenci iken iş dünyası ile buluşturuyoruz. Ayrıca dünyada bir ilk olan “Markalı Dersler” sayesinde iş dünyasının profesyonellerinden dersler alarak ilişki ağlarını geliştirmektedirler. CO-OP kapsamında yapılan stajlar sayesinde başarılı öğrenciler bu firmalarda işe devam etme olanağı bulabilmektedirler.",
            "Okulunuzun sosyal imkanları nelerdir?" => "Üniversitemizde 56 adet öğrenci kulubü bulunmaktadır. Bu kulüpler aracılığıyla öğrencilerimiz bir çok etkinliğe katılma imkanı yakalarlar. Eğer öğrencinin istediği kulüp üniversite bünyesinde bulunmuyorsa toplamda 25 kişi ile çok kolay bir şekilde kendi kulübünü kurabilmektedir.",
            "Öğrencilerinize staj konusunda yardımcı oluyor musunuz?" => "Üniversitemizde 6 yıldır uygulanan ve 150si yurt dışında olmak üzere toplamda 2700 şirket ile anlaşmalı CO-OP programımız doğrultusunda öğrencilerimiz üniversite tarafından sigortalı bir şekilde 3-6-9 aylık periyodlar şeklinde staj (CO-OP) yapabilmektedirler.",
            "Kaçıncı sınıftan itibaren co-op yapabilirim?" => "CO-OP bünyesinde anlaşmalı olunan 2700 firmanın sunduğu imkanlar doğrultusunda öğrenciler 1. sınıftan itibaren CO-OP yapabilmektedirler.",
            "Öğrencilerinize servis sağlıyor musunuz?" => "Üniversitemizin Beşiktaş gibi merkezi bir yerde bulunması sebebiyle İstanbul geneline dağılmış bir servis hizmeti yoktur. Kuzey Kampüsü’ne ulaşım için Beşiktaş Kampüsü’nden ücretsiz ring seferler, Galata Kampüsü’ne ulaşım için BAUPort’tan ücretsiz tekne seferleri, Göztepe Kampüsü’ne ulaşım için Üsküdar’dan ücretsiz seferler düzenlenmektedir.",
            "Okulunuzda sertifika programları düzenleniyor mudur?" => "Üniversitemiz bünyesinde yer alan Merkezler (HLO,Amers vb.); BAUSEM (Bahçeşehir Üniversitesi Sürekli Eğitim Merkezi) ve öğrenci kulüpleri aracılığıyla çok sayıda sertifika programları düzenlenmektedir.",
            "Üniversitenizde ne sıklıkta etkinlik-organizasyon gerçekleştiriliyor?" => "Gerek üniversitemizin kendisinin gerçekleştirdiği gerekse kampüsümüzün merkezi yerde olmasından dolayı dış kaynaklı yapılan öğrenciye açık çok sayıda organizasyon gerçekleşmektedir. 2017 yılı içerisinde üniversitemizde 1000’e yakın etkinlik gerçekleşmiştir. ",
            "Üniversite hangi saatler arasında açık?" => "Üniversitemiz 08:00 – 24:00 arası öğrencilere açık olup 24:00’dan sonra üniversitede kalmak isteyen öğrenciler Fakültelerinden aldıkları izin doğrultusunda kampüsü kullanabilmektedirler.",
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

        $question = Question::create("İmkanlar ile ilgili sıkça sorulan soruları listeliyorum:")
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