<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ApplyBAU;
use App\Conversations\StartConversation;
use App\Conversations\HelloConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new HelloConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function getUserInformation(BotMan $bot)
    {
        $bot->startConversation(new ApplyBAU());
    }
}
