<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class InternationalConnectionsFlow extends Conversation
{
    private function scholarship()
    {
        $this->getBot()->typesAndWaits(2);

        $questionsRaw = [
            "Kaçıncı sınıftan itibaren BAU Global bünyesinde açılan yurt dışı programlarından ders alabilirim? Sağlamam gereken şartlar neler?" => "BAU Global bünyesinde açılan yurt dışı programlarında eğitim dönemleri (sömestre) ve/veya yaz – kış okulları fakülteler tarafından belirlenmektedir. Dolayısıyla fakülteye bağlı olarak öğrenci 1. sınıftan itibaren yurt dışı akademik merkezlerinde eğitim alabilir. Aynı zamanda üniversitede uygulanan non-departmental / GE (General Education) dersler sayesinde öğrenciler kendi fakültelerinin dersleri dışında açılan dersleri almak üzere (özellikle yaz okullarında) yurt dışı akademik merkezlerine gidebilirler.",
            "BAU Global bünyesinde açılan yurt dışı programları için not ortalamasına gerek var mı? Var ise kaç olması gerekiyor?" => "BAU Global bünyesinde açılan yurt dışı programlarında eğitim alınması adına belirlenen bir not ortalaması kriteri yoktur. Öğrencinin İstanbul kampüslerinde mevcut olan akademik profili gereği alabileceği derslerin tamamını eğer bu dersler BAU Global bünyesinde açılan yurt dışı programları açılmış ise alabilir. Öğrencinin suspension olması durumunda (not ortalamasının 1.80’nin altında olması) uluslararası akademik danışmanın yönlendirmesi doğrultusunda BAU Global yurt dışı programlarına katılması mümkündür."
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

        $question = Question::create("Yurt dışı ile ilgili sıkça sorulan soruları listeliyorum.")
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
                $this->getBot()->typesAndWaits(3);
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