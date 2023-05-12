<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class ScholarshipFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Üniversitenizi tercih listesinde ilk üçe yazdığımız takdirde herhangi bir ek burs alabilir mi?" => "Bahçeşehir Üniversitesi’nde tercih sırasına dönük bir burs uygulaması yoktur ve kurulduğundan bu yana da hiç olmamıştır.",
            "Alacağımız maddi bursları sonradan geri ödemek durumunda kalıyor muyuz?" => "ÖSYM, ApplyBAU ve/veya başarı bursu, diploma bursu doğrultusunda verilen maddi burslar için geri ödeme durumu yoktur, bu burslar karşılıksız olarak verilir.",
            "Okulunuz öğrencilerine okurken çalışma imkanı sunuyor mu?" => "Üniversite bünyesinde bulunan farklı departmanlarda öğrenciler çalışma burslu olarak çalışabilirler. Başvurular Öğrenci Dekanlığı’na yapılmaktadır.",
            "Burslar kesilir mi?" => "ÖSYM giriş burslarının azami öğretim süresi; önlisans programlarında 4, dört yıllık lisans programlarında 7, beş yıllık programlarda 8 ve altı yıllık programlarda 9 akademik yıl olarak yurtiçinde geçerlidir.",
            "Sporcu bursu var mıdır?" => "Spor branşında milli takım seviyesine yükselmiş veya milli olmasa bile okul takımına seçildikten sonra yüksek performans göstermiş olan burssuz okuyan öğrencilere, Spor Koordinatörlüğü’nün teklifi ve Burs Komisyonu’nun tespit edeceği oranlarda karşılıksız ‘sporcu bursu’ verilebilir.\nBursun verilmesindeki esas, söz konusu öğrencinin kendi branşındaki okul takımında ve üniversite adına yarıştığı bireysel branşlarda Bahçeşehir Üniversitesi’ni temsil etmesidir. Burs verilecek branşlar, Spor Birimi’nin görüşü alınarak, Burs Komitesi tarafından belirlenir."
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

        $question = Question::create("Burslar ile ilgili sıkça sorulan soruları listeliyorum:")
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