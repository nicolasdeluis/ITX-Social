<?php
session_start();

if (!isset($_SESSION["name"])) {
    header("Location: login.php");
    exit();
}

$datenbank = new SQLite3("datenbank.db");
$datenbank->exec("CREATE TABLE IF NOT EXISTS nutzer (name TEXT PRIMARY KEY, passwort TEXT, notiz TEXT)");

$name = $_SESSION["name"];
$erfolg = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['notiz'])) {
    $notiz = $_POST['notiz'];
    
    $stmt = $datenbank->prepare("UPDATE nutzer SET notiz = :notiz WHERE name = :name");
    $stmt->bindValue(':notiz', $notiz, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        $erfolg = true;
    }
}

$stmt = $datenbank->prepare("SELECT notiz FROM nutzer WHERE name = :name");
$stmt->bindValue(':name', $name, SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);
$notiz = $row ? $row['notiz'] : '';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ITX Messenger - Interner Bereich</title>
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
        
        h1 {
            font-size: 50px;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        
        p {
            font-size: 24px;
            margin: 10px;
        }
        
        textarea {
            height: 200px;
            width: 600px;
            max-width: 90%;
            font-size: 18px;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            border: 1px solid #6a00ff;
            background-color: #1a1a2e;
            color: #ad00ddff;
            font-family: "Courier New", monospace;
            resize: vertical;
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }
        
        textarea::placeholder {
            color: #c100dfff;
        }
        
        button {
            height: 40px;
            width: 200px;
            font-size: 20px;
            margin-top: 15px;
            border-radius: 5px;
            color: #0e0b1b;
            background-color: #ad00ddff;
            border: none;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        button:hover {
            background-color: #ff00f7ff;
            color: #0e0b1b;
            font-weight: 600;
        }
        
        .erfolg {
            color: #00ff00;
            margin-top: 10px;
            font-size: 18px;
        }
        
        .link-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .action-link {
            color: #ad00ddff;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .action-link:hover {
            color: #ff00f7ff;
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

    <h1 id="greeting">Hallo <?php echo htmlspecialchars($name); ?>!</h1>
    <p id="subtitle">Interner Bereich</p>
    
    <?php if ($erfolg): ?>
        <p class="erfolg" id="success-message">✓ Notiz erfolgreich gespeichert!</p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <textarea name="notiz" id="notiz-textarea" placeholder=""><?php echo htmlspecialchars($notiz); ?></textarea>
        </div>
        <div>
            <button type="submit" id="save-button">Speichern</button>
        </div>
    </form>
    
    <div class="link-container">
        <a href="change_password.php" class="action-link" id="password-link">🔒 Passwort ändern</a>
        <a href="?logout=1" class="action-link" id="logout-link">🚪 Abmelden</a>
    </div>

    <script>
        const userName = <?php echo json_encode($name); ?>;
        
        const translations = {
            en: {
                greeting: "Hello " + userName + "!",
                subtitle: "Internal Area",
                placeholder: "Write your notes here...",
                saveButton: "Save",
                passwordLink: "🔒 Change Password",
                logoutLink: "🚪 Logout",
                successMessage: "✓ Note saved successfully!"
            },
            de: {
                greeting: "Hallo " + userName + "!",
                subtitle: "Interner Bereich",
                placeholder: "Schreibe hier deine Notizen...",
                saveButton: "Speichern",
                passwordLink: "🔒 Passwort ändern",
                logoutLink: "🚪 Abmelden",
                successMessage: "✓ Notiz erfolgreich gespeichert!"
            },
            zh: {
                greeting: "你好 " + userName + "!",
                subtitle: "内部区域",
                placeholder: "在此写下您的笔记...",
                saveButton: "保存",
                passwordLink: "🔒 更改密码",
                logoutLink: "🚪 登出",
                successMessage: "✓ 笔记保存成功！"
            },
            es: {
                greeting: "¡Hola " + userName + "!",
                subtitle: "Área Interna",
                placeholder: "Escribe tus notas aquí...",
                saveButton: "Guardar",
                passwordLink: "🔒 Cambiar Contraseña",
                logoutLink: "🚪 Cerrar sesión",
                successMessage: "✓ ¡Nota guardada con éxito!"
            },
            fr: {
                greeting: "Bonjour " + userName + " !",
                subtitle: "Espace Interne",
                placeholder: "Écrivez vos notes ici...",
                saveButton: "Enregistrer",
                passwordLink: "🔒 Changer le Mot de Passe",
                logoutLink: "🚪 Déconnexion",
                successMessage: "✓ Note enregistrée avec succès !"
            }
        };

        let currentLang = "de";

        const elements = {
            greeting: document.getElementById("greeting"),
            subtitle: document.getElementById("subtitle"),
            textarea: document.getElementById("notiz-textarea"),
            saveButton: document.getElementById("save-button"),
            passwordLink: document.getElementById("password-link"),
            logoutLink: document.getElementById("logout-link"),
            successMessage: document.getElementById("success-message")
        };

        function setLanguage(lang) {
            if (!translations[lang]) return;
            currentLang = lang;
            elements.greeting.textContent = translations[lang].greeting;
            elements.subtitle.textContent = translations[lang].subtitle;
            elements.textarea.placeholder = translations[lang].placeholder;
            elements.saveButton.textContent = translations[lang].saveButton;
            elements.passwordLink.textContent = translations[lang].passwordLink;
            elements.logoutLink.textContent = translations[lang].logoutLink;
            if (elements.successMessage) {
                elements.successMessage.textContent = translations[lang].successMessage;
            }
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
    </script>
</body>
</html>
