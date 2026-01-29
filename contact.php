<?php
session_start();
// Formular simplu prin care utilizatorii trimit un mesaj către magazin.

$errors = [];
$successMessage = '';

$name = '';
$email = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // La trimitere validăm câmpurile și încercăm să expediem emailul
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['msg'] ?? '');

    if ($name === '') {
        $errors[] = 'Introdu un nume.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Introdu o adresă de email validă.';
    }

    if ($subject === '') {
        $errors[] = 'Introduce un subiect.';
    }

    if ($message === '') {
        $errors[] = 'Completează mesajul.';
    }

    if (empty($errors)) {
        $to = 'liana_stanca@yahoo.com'; 
        $subjectLine = 'Contact magazin: ' . $subject;

        $htmlBody = "<html><head><title>Mesaj nou</title></head><body>";
        $htmlBody .= "<p>Ai primit un mesaj nou din formularul magazinului.</p>";
        $htmlBody .= "<table cellpadding='6' cellspacing='0' border='1' style='border-collapse:collapse;'>";
        $htmlBody .= "<tr><th align='left'>Nume</th><th align='left'>Email</th><th align='left'>Subiect</th></tr>";
        $htmlBody .= "<tr><td>" . htmlspecialchars($name) . "</td><td>" . htmlspecialchars($email) . "</td><td>" . htmlspecialchars($subject) . "</td></tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<p style='margin-top:12px;'>" . nl2br(htmlspecialchars($message)) . "</p>";
        $htmlBody .= "</body></html>";

        $headers = [];
        $fromHeaderName = preg_replace("/[\\r\\n]+/", '', $name);
        $headers[] = "From: {$fromHeaderName} <{$email}>";
        $headers[] = "Reply-To: {$email}";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";

        $wrappedBody = wordwrap($htmlBody, 70, "\r\n");

        $sent = @mail($to, $subjectLine, $wrappedBody, implode("\r\n", $headers));

        if ($sent) {
            $successMessage = 'Mesajul a fost trimis. Mulțumim!';
            $name = $email = $subject = $message = '';
        } else {
            $errors[] = 'Nu am putut trimite emailul. Verifică setările serverului de mail din MAMP.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Contact magazin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        form {
            max-width: 520px;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            min-height: 140px;
            resize: vertical;
        }
        button {
            padding: 0.6rem 1.2rem;
            background-color: #1a4a9f;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #842029;
        }
        .alert.success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
    </style>
</head>
<body>
    <h1>Contactează-ne</h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <?php if ($successMessage): ?>
        <div class="alert success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="name">Nume</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="subject">Subiect</label>
        <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($subject); ?>" required>

        <label for="msg">Mesaj</label>
        <textarea name="msg" id="msg" required><?php echo htmlspecialchars($message); ?></textarea>

        <button type="submit" name="send_message_btn">Trimite</button>
    </form>

    <p style="margin-top:1.5rem;"><a href="categoryIndex.php">Înapoi la magazin</a></p>
</body>
</html>
