<?php
    session_start();
?>

<?xml version = "1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrazione</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_register.css">
        <link rel="stylesheet" href="../css/style_header.css">
    </head>

    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="cont">
            <?php
            $data_registrazione = date("Y-m-d H:i:s");

            if (isset($_SESSION['errore_email']) && $_SESSION['errore_email'] == 'true') { 
                echo "<h2>L'email '" . $_SESSION['email_errata'] . "' è già in uso!</h2>";
                unset($_SESSION['errore_email']);
                unset($_SESSION['form_email']);
            }

            if (isset($_SESSION['errore_preg']) && $_SESSION['errore_preg'] == 'true') {
                echo "<h2>La password non rispetta i criteri di sicurezza!</h2>";
                unset($_SESSION['errore_preg']);
            }

            if (isset($_SESSION['errore_cellulare']) && $_SESSION['errore_cellulare'] == 'true') {
                echo "<h2>Il numero di cellulare '" . $_SESSION['cellulare_errato'] . "' è già in uso!</h2>";
                unset($_SESSION['errore_cellulare']);
                unset($_SESSION['form_cellulare']);
            }

            if (isset($_SESSION['errore_codice_fiscale']) && $_SESSION['errore_codice_fiscale'] == 'true') {
                echo "<h2>Il codice fiscale '" . $_SESSION['codice_fiscale_errato'] . "' è già in uso!</h2>";
                unset($_SESSION['errore_codice_fiscale']);
                unset($_SESSION['form_codice_fiscale']);
            }

            if (isset($_SESSION['errore_registrazione']) && $_SESSION['errore_registrazione'] == 'true') {
                echo "<h2>Errore in fase di registrazione</h2>";
                unset($_SESSION['errore_registrazione']);
            }
            ?>

            <div class="wrapper">

                <form action="../res/cliente_register.php" method="post" class="form">
                    <table>
                        <tr>
                            <td colspan="2">
                                <h1 class="titolo">
                                    <div class="tooltip">
                                        <span class="tooltiptext">LA PASSWORD DEVE SODDISFARE I SEGUENTI REQUISITI:
                                            <ol>
                                                <li>Deve essere lunga almeno 8 caratteri;</li>
                                                <li>Deve contenere almeno una lettera maiuscola e una minuscola;</li>
                                                <li>Deve contenere almeno un numero;</li>
                                                <li>Deve contenere almeno un carattere speciale (!,@,#,$,%,^,&,*).</li>
                                            </ol>
                                        </span>
                                        <i id="simbolo" class="material-symbols-outlined">info</i>
                                    </div>
                                    REGISTRAZIONE
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td class="inp">
                                <input type="text" name="nome" class="input" placeholder="Nome"
                                    value="<?php if (isset($_SESSION['form_nome'])) echo $_SESSION['form_nome']; ?>" required>
                            </td>
                            <td class="inp">
                                <input type="date" name="data_di_nascita" class="input"
                                    value="<?php if (isset($_SESSION['form_data_di_nascita'])) echo $_SESSION['form_data_di_nascita']; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="inp">
                                <input type="text" name="cognome" class="input" placeholder="Cognome"
                                    value="<?php if (isset($_SESSION['form_cognome'])) echo $_SESSION['form_cognome']; ?>" required>
                            </td>
                            <td class="inp">
                                <input type="email" name="email" class="input" placeholder="Email"
                                    value="<?php if (isset($_SESSION['form_email'])) echo $_SESSION['form_email']; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="inp">
                                <input type="text" name="indirizzo_di_residenza" class="input" placeholder="Indirizzo di residenza"
                                    value="<?php if (isset($_SESSION['form_indirizzo_di_residenza'])) echo $_SESSION['form_indirizzo_di_residenza']; ?>" required>
                            </td>
                            <td class="inp">
                                <input type="text" name="codice_fiscale" class="input" placeholder="Codice fiscale"
                                    pattern="[A-Za-z]{6}\d{2}[A-Za-z]\d{2}[A-Za-z]\d{3}[A-Za-z]" maxlength="16"
                                    title="Inserisci un codice fiscale italiano valido. (es.XXXXXX00X00X000X)"
                                    value="<?php if (isset($_SESSION['form_codice_fiscale'])) echo $_SESSION['form_codice_fiscale']; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="inp">
                                <input type="text" name="cellulare" class="input" placeholder="Numero di Cellulare"
                                    pattern="\d{10}" maxlength="10" 
                                    title="Inserisci un numero di telefono valido. (formato da 10 numeri)"
                                    value="<?php if (isset($_SESSION['form_cellulare'])) echo $_SESSION['form_cellulare']; ?>" required>
                            </td>
                            <td class="inp">
                                <input type="password" name="password" class="input" placeholder="Password" required>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="data_registrazione" value="<?php echo $data_registrazione; ?>">
                                <button class="submit" type="submit">Crea Account</button>
                                <p class="footer">Hai già un account? <a href="login_cliente.php" class="link">Accedi!</a></p>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>