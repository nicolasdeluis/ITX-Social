<?php
session_start();

if (!isset($_SESSION["name"])) {
    header("Location: login.php");
    exit();
}

$datenbank = new SQLite3("datenbank.db");
$name = $_SESSION["name"];
$fehler = "";
$erfolg = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $altes_passwort = $_POST['altes_passwort'];
    $neues_passwort = $_POST['neues_passwort'];
    $passwort_bestaetigung = $_POST['passwort_bestaetigung'];

    // Aktuelles Passwort überprüfen
    $stmt = $datenbank->prepare("SELECT passwort FROM nutzer WHERE name = :name");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if (!$row || !password_verify($altes_passwort, $row['passwort'])) {
        $fehler = "Altes Passwort ist falsch!";
    } elseif (strlen($neues_passwort) < 6) {
        $fehler = "Neues Passwort muss mindestens 6 Zeichen lang sein!";
    } elseif ($neues_passwort !== $passwort_bestaetigung) {
        $fehler = "Die neuen Passwörter stimmen nicht überein!";
    } else {
        // Passwort aktualisieren
        $passwort_hash = password_hash($neues_passwort, PASSWORD_DEFAULT);
        $stmt = $datenbank->prepare("UPDATE nutzer SET passwort = :passwort WHERE name = :name");
        $stmt->bindValue(':passwort', $passwort_hash, SQLITE3_TEXT);
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            $erfolg = true;
        } else {
            $fehler = "Fehler beim Aktualisieren des Passworts!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ITX Messenger - Passwort ändern</title>
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
        
        input {
            height: 35px;
            width: 315px;
            font-size: 18px;
            padding-left: 5px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #6a00ff;
            background-color: #1a1a2e;
            color: #ad00ddff;
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }
        
        input::placeholder {
            color: #c100dfff;
        }
        
        .passwort-div {
            position: relative;
            width: 320px;
            margin: 10px auto;
        }
        
        .toggle-passwort {
            position: absolute;
            top: 20px;
            right: 10px;
            cursor: pointer;
            color: #6a00ff;
            transition: color 0.3s ease;
        }
        
        .toggle-passwort:hover {
            color: #ad00ddff;
        }
        
        .auge-offen {
            display: none;
        }
        
        button {
            height: 40px;
            width: 320px;
            font-size: 20px;
            margin-top: 20px;
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
        
        .fehler {
            color: #ff4444;
            margin-top: 15px;
            font-size: 18px;
        }
        
        .erfolg {
            color: #00ff00;
            margin-top: 15px;
            font-size: 18px;
        }
        
        .zurueck-link {
            display: inline-block;
            margin-top: 20px;
            color: #ad00ddff;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .zurueck-link:hover {
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

    <h1 id="title">Passwort ändern</h1>
    <p id="subtitle">Für: <?php echo htmlspecialchars($name); ?></p>
    
    <?php if ($erfolg): ?>
        <p class="erfolg" id="success-message">✓ Passwort erfolgreich geändert!</p>
    <?php endif; ?>
    
    <?php if ($fehler): ?>
        <p class="fehler" id="error-message"><?php echo htmlspecialchars($fehler); ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div class="passwort-div">
            <input id="altes-passwort" name="altes_passwort" type="password" placeholder="" required />
            <span class="toggle-passwort" onclick="togglePassword('altes-passwort', 'auge1-offen', 'auge1-zu')">
                <svg class="auge-offen" id="auge1-offen" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                </svg>
                <svg id="auge1-zu" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                </svg>
            </span>
        </div>

        <div class="passwort-div">
            <input id="neues-passwort" name="neues_passwort" type="password" placeholder="" required minlength="6" />
            <span class="toggle-passwort" onclick="togglePassword('neues-passwort', 'auge2-offen', 'auge2-zu')">
                <svg class="auge-offen" id="auge2-offen" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                </svg>
                <svg id="auge2-zu" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                </svg>
            </span>
        </div>

        <div class="passwort-div">
            <input id="passwort-bestaetigung" name="passwort_bestaetigung" type="password" placeholder="" required minlength="6" />
            <span class="toggle-passwort" onclick="togglePassword('passwort-bestaetigung', 'auge3-offen', 'auge3-zu')">
                <svg class="auge-offen" id="auge3-offen" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                </svg>
                <svg id="auge3-zu" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                </svg>
            </span>
        </div>

        <div>
            <button type="submit" id="change-button">Passwort ändern</button>
        </div>
    </form>
    
    <a href="intern.php" class="zurueck-link" id="back-link">← Zurück zum internen Bereich</a>

    <script>
        const userName = <?php echo json_encode($name); ?>;
        
        const translations = {
            en: {
                title: "Change Password",
                subtitle: "For: " + userName,
                oldPasswordPlaceholder: "Enter old password...",
                newPasswordPlaceholder: "Enter new password...",
                confirmPasswordPlaceholder: "Confirm new password...",
                changeButton: "Change Password",
                backLink: "← Back to internal area",
                successMessage: "✓ Password changed successfully!"
            },
            de: {
                title: "Passwort ändern",
                subtitle: "Für: " + userName,
                oldPasswordPlaceholder: "Altes Passwort eingeben...",
                newPasswordPlaceholder: "Neues Passwort eingeben...",
                confirmPasswordPlaceholder: "Neues Passwort bestätigen...",
                changeButton: "Passwort ändern",
                backLink: "← Zurück zum internen Bereich",
                successMessage: "✓ Passwort erfolgreich geändert!"
            },
            zh: {
                title: "更改密码",
                subtitle: "用户：" + userName,
                oldPasswordPlaceholder: "输入旧密码...",
                newPasswordPlaceholder: "输入新密码...",
                confirmPasswordPlaceholder: "确认新密码...",
                changeButton: "更改密码",
                backLink: "← 返回内部区域",
                successMessage: "✓ 密码更改成功！"
            },
            es: {
                title: "Cambiar Contraseña",
                subtitle: "Para: " + userName,
                oldPasswordPlaceholder: "Ingrese contraseña antigua...",
                newPasswordPlaceholder: "Ingrese nueva contraseña...",
                confirmPasswordPlaceholder: "Confirme nueva contraseña...",
                changeButton: "Cambiar Contraseña",
                backLink: "← Volver al área interna",
                successMessage: "✓ ¡Contraseña cambiada con éxito!"
            },
            fr: {
                title: "Changer le Mot de Passe",
                subtitle: "Pour : " + userName,
                oldPasswordPlaceholder: "Entrez l'ancien mot de passe...",
                newPasswordPlaceholder: "Entrez le nouveau mot de passe...",
                confirmPasswordPlaceholder: "Confirmez le nouveau mot de passe...",
                changeButton: "Changer le Mot de Passe",
                backLink: "← Retour à l'espace interne",
                successMessage: "✓ Mot de passe changé avec succès !"
            }
        };

        let currentLang = "de";

        const elements = {
            title: document.getElementById("title"),
            subtitle: document.getElementById("subtitle"),
            oldPassword: document.getElementById("altes-passwort"),
            newPassword: document.getElementById("neues-passwort"),
            confirmPassword: document.getElementById("passwort-bestaetigung"),
            changeButton: document.getElementById("change-button"),
            backLink: document.getElementById("back-link"),
            successMessage: document.getElementById("success-message")
        };

        function setLanguage(lang) {
            if (!translations[lang]) return;
            currentLang = lang;
            elements.title.textContent = translations[lang].title;
            elements.subtitle.textContent = translations[lang].subtitle;
            elements.oldPassword.placeholder = translations[lang].oldPasswordPlaceholder;
            elements.newPassword.placeholder = translations[lang].newPasswordPlaceholder;
            elements.confirmPassword.placeholder = translations[lang].confirmPasswordPlaceholder;
            elements.changeButton.textContent = translations[lang].changeButton;
            elements.backLink.textContent = translations[lang].backLink;
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

        function togglePassword(inputId, offenId, zuId) {
            const input = document.getElementById(inputId);
            const augeOffen = document.getElementById(offenId);
            const augeZu = document.getElementById(zuId);

            if (input.type === "password") {
                input.type = "text";
                augeOffen.style.display = "inline";
                augeZu.style.display = "none";
            } else {
                input.type = "password";
                augeOffen.style.display = "none";
                augeZu.style.display = "inline";
            }
        }

        setLanguage(currentLang);
    </script>
</body>
</html>
