# OnlineShop

## Configurare trimitere email (MAMP)
Formularul `contact.php` folosește funcția `mail()` din PHP, care în MAMP depinde de configurarea corectă a `sendmail`. Pași rezumați:

1. Deschide `php.ini` din `MAMP/conf/php/php<versiune>/php.ini` și setează:
   ```
   [mail function]
   sendmail_path = /Applications/MAMP/bin/sendmail/sendmail -t -i
   ```
   (potrivește calea exactă din instalarea ta).
2. Editează `sendmail.ini` (de obicei în `/Applications/MAMP/bin/sendmail/`) și completează datele SMTP:
   ```
   smtp_server=smtp.gmail.com
   smtp_port=587
   smtp_ssl=tls
   auth_username=contultau@gmail.com
   auth_password=parola_sau_app_password
   ```
3. Restartează serverele MAMP după salvarea fișierelor.

După configurare, accesează `/contact.php`, completează formularul și verifică logurile `sendmail` dacă emailul nu se trimite.

## Stripe checkout & `tbl_payment`
1. Setează cheile de test Stripe în `stripe_config.php`:
   ```php
   define('STRIPE_SECRET_KEY', 'sk_test_REPLACEME');
   define('STRIPE_PUBLISHABLE_KEY', 'pk_test_REPLACEME');
   define('STRIPE_CURRENCY', 'eur'); // Stripe nu suportă RON
   ```
2. Creează tabela care stochează tranzacțiile reușite:
   ```sql
   CREATE TABLE IF NOT EXISTS tbl_payment (
       id INT AUTO_INCREMENT PRIMARY KEY,
       member_id INT NOT NULL,
       payment_intent_id VARCHAR(64) NOT NULL,
       amount DECIMAL(10,2) NOT NULL,
       currency VARCHAR(10) NOT NULL,
       status VARCHAR(30) NOT NULL,
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_member (member_id),
       INDEX idx_payment_intent (payment_intent_id)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```
3. Fluxul: utilizatorul completează coșul → `cart.php` → `checkout.php` (Stripe) → `payment_success.php` inserează în `tbl_payment` și golește coșul.
