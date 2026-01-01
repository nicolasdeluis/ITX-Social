<?php
session_start();

if (isset($_SESSION["name"])) {
    header("Location: intern.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITX-Messenger</title>

    <style>
        body {
            text-align: center;
            font-family: "Courier New", monospace;
            color: #ad00ddff;
            background-color: #0e0b1b;
            margin: 0;
            padding: 0;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        h1 {
            font-size: 80px;
            margin-top: 50px;
        }
        
        p {
            font-size: 30px;
        }

        a {
            display: block;
            width: 320px;
            margin: 15px auto;
            padding: 10px;
            font-size: 22px;
            color: #0e0b1b;
            font-weight: bold;
            background-color: #ad00ddff;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            background-color: #ff00f7ff;
            cursor: pointer;
        }

        #language-switcher {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0, 166, 8, 0.1);
            border-radius: 6px;
            padding: 5px 8px;
            box-shadow: 0 0 5px rgba(61, 0, 117, 0.3);
            display: flex;
            gap: 8px;
            z-index: 1000;
        }
        
        #language-switcher img {
            width: 24px;
            height: 16px;
            border-radius: 3px;
            border: 1px solid #6a00ff;
            transition: transform 0.2s ease;
            cursor: pointer;
        }
        
        #language-switcher img:hover {
            transform: scale(1.2);
            border-color: #ad00ddff;
        }
    </style>
</head>
<body>

    <div id="language-switcher">
        <img src="https://flagcdn.com/w20/cn.png" data-lang="zh" alt="中文">
        <img src="https://flagcdn.com/w20/gb.png" data-lang="en" alt="English">
        <img src="https://flagcdn.com/w20/es.png" data-lang="es" alt="Español">
        <img src="https://flagcdn.com/w20/fr.png" data-lang="fr" alt="Français">
        <img src="https://flagcdn.com/w20/de.png" data-lang="de" alt="Deutsch">
    </div>

    <h1 id="title">ITX-Messenger</h1>
    <p id="subtitle">Anmelden oder registrieren</p>

    <a id="login" href="login.php">Anmelden</a>
    <a id="register" href="registrieren.php">Registrieren</a>

    <script>
        const translations = {
            en: {
                title: "ITX-Messenger",
                subtitle: "Login or register:",
                login: "Login",
                register: "Register"
            },
            de: {
                title: "ITX-Messenger",
                subtitle: "Anmelden oder registrieren:",
                login: "Anmelden",
                register: "Registrieren"
            },
            es: {
                title: "ITX-Messenger",
                subtitle: "Iniciar sesión o registrarse:",
                login: "Iniciar sesión",
                register: "Registrarse"
            },
            zh: {
                title: "ITX-信使",
                subtitle: "登录 或 注册:",
                login: "登录",
                register: "注册"
            },
            fr: {
                title: "ITX-Messenger",
                subtitle: "Connexion ou inscription:",
                login: "Connexion",
                register: "Inscription"
            }
        };

        let currentLang = "de";

        function setLanguage(lang) {
            const t = translations[lang];
            if (!t) return;

            currentLang = lang;

            document.getElementById("title").textContent = t.title;
            document.getElementById("subtitle").textContent = t.subtitle;
            document.getElementById("login").textContent = t.login;
            document.getElementById("register").textContent = t.register;
        }

        document.querySelectorAll("#language-switcher img").forEach(flag => {
            flag.addEventListener("click", () => setLanguage(flag.dataset.lang));
        });

        setLanguage(currentLang);
    </script>

</body>
</html>
