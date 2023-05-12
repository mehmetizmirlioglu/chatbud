<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Capstone Project</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        body {
            font-family: "Varela Round", sans-serif;
            margin: 0;
            padding: 0;
            background: radial-gradient(#ff6666, #ffa500);
        }

        .container {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .content {
            text-align: center;
        }

        .logo {
            margin-right: 40px;
            margin-bottom: 20px;
        }

        .links a {
            font-size: 1.25rem;
            text-decoration: none;
            color: white;
            margin: 10px;
        }

        @media all and (max-width: 500px) {

            .links {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="links">
                <a href="javascript:botmanChatWidget.open();">Start Conversation</a>
            </div>
        </div>
    </div>

    <script>
        var botmanWidget = {
            chatServer: '/botman',
            frameEndpoint: '/botman/chat',
            timeFormat: "HH:MM",
            dateTimeFormat: "d.m.yy HH:MM",
            introMessage: "Konuşmayı başlatmak için <b>merhaba</b> yazabilirsiniz.",
            mainColor: "#bf1f2f",
            bubbleBackground: "#bf1f2f",
            title: "BAU Capstone Project - Chatbot",
            titleColor: "#fff",
            aboutLink: "https://bau.edu.tr",
            aboutText: "BAU Capstone Project Develop with Botman"
        };
    </script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
</body>
</html>