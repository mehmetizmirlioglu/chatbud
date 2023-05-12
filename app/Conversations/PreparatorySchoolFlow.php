<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class PreparatorySchoolFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Hazırlık okulunun eğitimi hakkında bilgi verebilir misiniz?" => "Hazırlık öğrencileri için modüler sistem (kur sistemi) uygulanmaktadır. Bir akademik yıl, her biri sekiz haftadan oluşan toplam 5 modül ve 5 seviyeden oluşur (A1, A2, B1, B2 & Prep C). Hazırlık Okulu tarafından yapılan Seviye Belirleme Sınavı’ndan alınan puana göre öğrencinin İngilizce seviyesine göre eğitim göreceği kur belirlenir. Öğrencilerin bir sonraki modüle geçebilmeleri için her bir modülü başarıyla tamamlamaları zorunludur. Öğrenci, bir sonraki seviyeye geçebilmek için bulunduğu kurdan % 65 başarı notunu almak zorundadır. Öğrenci bulunduğu modül sonu yapılacak olan Modül Sonu Sınavına girmek için o modülün devam zorunluluğunu yerine getirmiş ve sınıf içi çalışmalarını da eksiksiz tamamlamış (“complete” almış) olmalıdır.",
            "Hazırlık sınıfı zor mu?" => "Hazırlık sınıfında yoğun bir İngilizce eğitimi verilmektedir ve devam zorunluluğu bulunmaktadır. İngilizce seviyenize göre çalışma temponuzu arttırmanız gerekebilir. Bireysel Öğrenim Merkezi tarafından alınacak takviyeler ve teknolojik destekler ile hazırlık sınıfını geçmeniz kolaylaşacaktır.",
            "Hazırlık sınıfını okumadan geçebilme imkanımız var mıdır?" => "Hazırlık Okulu tarafından yapılan Seviye Belirleme Sınavı’ndan 60 soruda en az 30 soru net ve yazma bölümünden B1 (intermediate) oranında başarılı olan öğrencilerimiz Yeterlik Sınavı’na girmeye hak kazanırken, bu oranın altında kalarak başarısız olanlar sınav sonucuna göre Hazırlık Programı’nda kendilerine uygun olan seviyeye yerleştirilir. İngilizce Yeterlik Sınavı’ndan, 100 (yüz) üzerinden 60 (altmış) puan; İngilizce Öğretmenliği öğrencileri için 100 (yüz) üzerinden 80 (seksen) puan almaları halinde hazırlık sınıfından muaf sayılırlar. Ayrıca; aşağıda belirtilen ve üniversitemiz tarafından kabul edilen uluslararası geçerliliği olan sınavlardan yeterli puanı alan öğrenciler de İngilizce Hazırlık Programı’ndan muaf sayılırlar.\r\n– TOEFL (IBT 72) İngilizce Öğretmenliği için (IBT 79)\r\n– YDS 60 İngilizce Öğretmenliği için 80\r\n– Pearson PTE Akademik 55 İngilizce Öğretmenliği için 78\r\n– CAE C İngilizce Öğretmenliği için A",
            "Hazırlığı yarım dönemde bitirebilir miyim? Bitirince direkt bölüm derslerine başlayabilir miyim?" => "Hazırlık Programı’nı 1.Yarıyıl başarıyla bitiren (İngilizce Yeterlik Sınavı’nda başarılı olan) lisans öğrencileri, 2. Yarıyılda fakültelerinde ilgili bölümlerinden ders almaya başlarlar. 1. Yarıyılın sonunda başarılı olan tüm öğrenciler fakültelerinden ders almak zorundadırlar.",
            "Yurt dışında hazırlık okuma imkanımız var mıdır? Yurt imkanı sağlanıyor mu?" => "Öğrenciler Washington D.C’de ve Berlin’de bulunan Mentora College’da 1 akademik yıl, veya Toronto’da bulunan CES’de (Capital English Solutions) 5 ay süreyle Hazırlık eğitimini alabilirler. Yurt dışı Hazırlık Programının adı BESL olup bu programa katılacak öğrencilerin İngilizce Belirleme Sınavı’na katılımları zorunludur. BESL programlarında da derse devam zorunluluğu vardır. Öğrenciler hem Washington D.C, Berlin hem de Toronto için üniversitenin yurtlarından ücret karşılığında kalabilirler.",
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

        $question = Question::create("Hazırlık ile ilgili sıkça sorulan soruları listeliyorum:")
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