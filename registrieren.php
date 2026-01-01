<?php
session_start();

$datenbank = new SQLite3("datenbank.db");
$datenbank->exec("CREATE TABLE IF NOT EXISTS nutzer (name TEXT PRIMARY KEY, passwort TEXT, notiz TEXT)");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $passwort = $_POST['passwort'];
    
    $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

    $stmt = $datenbank->prepare("INSERT INTO nutzer (name, passwort, notiz) VALUES (:name, :passwort, '')");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':passwort', $passwort_hash, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $_SESSION["name"] = $name;
        header("Location: intern.php");
        exit();
    } else {
        $fehler = "Registrierung fehlgeschlagen. Name bereits vergeben?";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ITX Messenger - Registrieren</title>
    <style>
        body {
            text-align: center;
            font-family: "Courier New", monospace;
            color: #ad00ddff;
            background-color: #0e0b1b;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            margin: 0;
            padding: 0;
        }
        input {
            height: 35px;
            width: 315px;
            font-size: 20px;
            padding-left: 5px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #6a00ff;
            background-color: #1a1a2e;
            color: #ad00ddff;
            user-select: text;
        }
        input::placeholder {
            color: #c100dfff;
        }
        #passwort-div {
            position: relative;
            width: 320px;
            margin: auto;
        }
        #toggle-passwort-sichtbarkeit {
            position: absolute;
            top: 15px;
            right: 10px;
            cursor: pointer;
            color: #6a00ff;
            transition: color 0.3s ease;
        }
        #toggle-passwort-sichtbarkeit:hover {
            color: #ad00ddff;
        }
        #auge-offen {
            display: none;
        }
        button {
            height: 35px;
            width: 320px;
            font-size: 20px;
            padding-left: 5px;
            margin-top: 5px;
            border-radius: 5px;
            color: #0e0b1b;
            background-color: #ad00ddff;
            border: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #ff00f7ff;
            color: #0e0b1b;
            cursor: pointer;
            font-weight: 600;
        }
        .fehler {
            color: #ff4444;
            margin-top: 10px;
        }
        #language-switcher {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0, 166, 8, 0.1);
            border-radius: 6px;
            padding: 5px 8px;
            box-shadow: 0 0 5px rgba(61, 0, 117, 0.3);
            user-select: none;
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
    <div id="language-switcher" aria-label="Sprache wechseln" role="list">
        <img src="https://flagcdn.com/w20/cn.png" alt="中文" data-lang="zh" role="listitem" tabindex="0" />
        <img src="https://flagcdn.com/w20/gb.png" alt="English" data-lang="en" role="listitem" tabindex="0" />
        <img src="https://flagcdn.com/w20/es.png" alt="Español" data-lang="es" role="listitem" tabindex="0" />
        <img src="https://flagcdn.com/w20/fr.png" alt="Français" data-lang="fr" role="listitem" tabindex="0" />
        <img src="https://flagcdn.com/w20/de.png" alt="Deutsch" data-lang="de" role="listitem" tabindex="0" />
    </div>

    <h1 id="title" style="font-size: 80px;">ITX</h1>
    <p id="subtitle" style="font-size: 30px;">Registrieren:</p>
    
    <?php if (isset($fehler)): ?>
        <p class="fehler"><?php echo htmlspecialchars($fehler); ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <input type="text" id="name" name="name" placeholder="" required />
        </div>

        <div id="passwort-div">
            <input id="passwort-input" name="passwort" type="password" placeholder="" required minlength="6" />
            <span id="toggle-passwort-sichtbarkeit" onclick="PasswortSichtbarkeit()" aria-label="Passwort anzeigen/verbergen" role="button" tabindex="0">
                <svg id="auge-offen" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                </svg>
                <svg id="auge-zu" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                </svg>
            </span>
        </div>

        <div>
            <button type="submit" id="register-button"></button>
        </div>
    </form>

    <script>
        const translations = {
            en: {
                title: "ITX",
                subtitle: "Register:",
                namePlaceholder: "Enter your name here...",
                passwordPlaceholder: "Enter your password here...",
                registerButton: "Register"
            },
            de: {
                title: "ITX",
                subtitle: "Registrieren:",
                namePlaceholder: "Hier Namen eingeben...",
                passwordPlaceholder: "Hier Passwort eingeben...",
                registerButton: "Registrieren"
            },
            es: {
                title: "ITX",
                subtitle: "Registrarse:",
                namePlaceholder: "Escribe tu nombre aquí...",
                passwordPlaceholder: "Escribe tu contraseña aquí...",
                registerButton: "Registrarse"
            },
            zh: {
                title: "ITX",
                subtitle: "注册：",
                namePlaceholder: "请输入您的姓名...",
                passwordPlaceholder: "请输入您的密码...",
                registerButton: "注册"
            },
            fr: {
                title: "ITX",
                subtitle: "S'inscrire :",
                namePlaceholder: "Entrez votre nom ici...",
                passwordPlaceholder: "Entrez votre mot de passe ici...",
                registerButton: "S'inscrire"
            }
        };

        let currentLang = "de";

        const elements = {
            title: document.getElementById("title"),
            subtitle: document.getElementById("subtitle"),
            nameInput: document.getElementById("name"),
            passwordInput: document.getElementById("passwort-input"),
            registerButton: document.getElementById("register-button")
        };

        function setLanguage(lang) {
            if (!translations[lang]) return;
            currentLang = lang;
            elements.title.textContent = translations[lang].title;
            elements.subtitle.textContent = translations[lang].subtitle;
            elements.nameInput.placeholder = translations[lang].namePlaceholder;
            elements.passwordInput.placeholder = translations[lang].passwordPlaceholder;
            elements.registerButton.textContent = translations[lang].registerButton;
        }

        document.querySelectorAll("#language-switcher img").forEach(flag => {
            flag.addEventListener("click", () => setLanguage(flag.dataset.lang));
            flag.addEventListener("keydown", (e) => {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    setLanguage(flag.dataset.lang);
                }
            });
        });

        setLanguage(currentLang);

        function PasswortSichtbarkeit() {
            const passwortInput = document.getElementById("passwort-input");
            const augeOffen = document.getElementById("auge-offen");
            const augeZu = document.getElementById("auge-zu");

            if (passwortInput.type === "password") {
                passwortInput.type = "text";
                augeOffen.style.display = "inline";
                augeZu.style.display = "none";
            } else {
                passwortInput.type = "password";
                augeOffen.style.display = "none";
                augeZu.style.display = "inline";
            }
        }
    </script>
</body>
</html>
