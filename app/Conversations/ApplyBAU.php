<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ApplyBAU extends Conversation
{
    protected $fullname;
    protected $email;
    protected $emailVerificationCode;

    /**
     * FullName
     */
    public function askFullName()
    {
        if($this->getBot()->userStorage()->get("fullname") != null)
            return $this->askEmail();
        $this->getBot()->typesAndWaits(2);
        $this->ask('Öncelikle isminizi soyisminizi öğrenebilir miyim?', function(Answer $answer) {
            // Save result
            $this->fullname = $answer->getText();

            $this->getBot()->userStorage()->save([
                'fullname' => $this->fullname
            ]);

            $this->getBot()->typesAndWaits(2);
            $this->say("Merhaba <b>{$this->fullname}</b>");
            $this->askEmail();
        });
    }

    /**
     * Email
     */
    public function askEmail()
    {
        $this->getBot()->typesAndWaits(2);
        $this->ask('Başvuru yaptığınız veya kullandığınız e-posta adresini alabilir miyim?', function(Answer $answer) {
            // Save result
            $this->email = $answer->getText();
            $this->getBot()->typesAndWaits(2);
            $this->say("Vermiş olduğunuz bilgiler için teşekkür ederim.");
            $this->askEmailVerificationCode();
        });
    }

    /**
     * askEmailVerificationCode
     */
    public function askEmailVerificationCode($repeat = false)
    {
        $this->emailVerificationCode = 181818;
        $this->getBot()->typesAndWaits(2);

        $question = Question::create(($repeat ? "Yazdığınız kod yanlış. {$this->email} e-posta adresinize gönderdiğim kodu kontrol edip bana yazabilir misiniz?" : "{$this->email} e-posta adresinize bir kod gönderdim. Bu kodu bana yazabilir misiniz? (Test Verification Code: 181818)"))
            ->fallback('Geçersiz bir istekte bulundunuz.')
            ->callbackId('email-verification')
            ->addButtons([
                Button::create('E-Posta adresimi yanlış girdim')->value('wrong-email'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                if($answer->getValue() == "wrong-email")
                {
                    $this->askEmail();
                }
            }
            else
            {
                if($answer->getText() != $this->emailVerificationCode)
                {
                    $this->askEmailVerificationCode(true);
                }
                else
                {
                    $this->getBot()->userStorage()->save([
                        'email' => trim($this->email)
                    ]);

                    $this->getBot()->typesAndWaits(2);
                    $this->say("Vermiş olduğunuz bilgiler için teşekkür ederim.");
                    $this->applyBAUStatus();
                }
            }
        });
    }

    public function applyBAUStatus()
    {
        $this->getBot()->typesAndWaits(2);
        if($this->getBot()->userStorage()->get("email") == "mehmet@izmirlioglu.com")
        {
            $this->say("Başvurunuz <b>Değerlendirme</b> aşamasındadır.");
            $this->getBot()->typesAndWaits(2);
            $this->say("Başvurunuzu bu aşamada <u>düzenleyemezsiniz</u>.");
            $this->getBot()->typesAndWaits(2);
            $this->say("<a href=\"https://applybau.com/\" target='_parent'>Başvuruzu görüntülemek için tıklayınız.</a>");
        }
        else
        {
            $this->say("Yapmış olduğunuz bir başvuru <b>bulunmamaktadır</b>.");
            $this->getBot()->typesAndWaits(2);
            $this->say("<a href=\"https://applybau.com/basvuru-yap\" target='_parent'>Başvuru yapmak için tıklayınız.</a>");
        }
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askFullName();
    }
}
