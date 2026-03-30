<?php
    session_start();

    // Verifica se l'amministratore è loggato
    if (isset($_SESSION['id'])) {

        $admin = $_SESSION['ammin'];
        if ($admin == 0){
            // non siamo amministratori
            header("Location: accesso_negato.php");
            exit();
        }
    }else{
        // Reindirizza alla pagina di accesso se non è loggato
        header("Location: login_cliente.php");
        exit();
    }
?>


<?xml version = "1.0"?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Dati Utente</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        
        <div class="contenitore">
            <?php
                // Verifica se è stato fornito un ID utente valido
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $id_utente = $_GET['id'];

                    // Esegui una query per ottenere i dati dell'utente
                    $query = "SELECT * FROM utenti WHERE id = $id_utente";
                    $result = $connessione->query($query);

                    if ($result->num_rows == 1) {
                        $utente = $result->fetch_assoc();

                        if(isset($_SESSION['errore_query']) && $_SESSION['errore_query'] == 'true'){
                            echo "<h2>Errore durante la richiesta!</h2>";
                            unset($_SESSION['errore_query']);
                        }

                        if(isset($_SESSION['errore_cellulare_ex']) && $_SESSION['errore_cellulare_ex'] == 'true'){
                            echo "<h2>Il numero di cellulare '" . $_SESSION['cellulare_errato'] . "' è già in uso!</h2>";
                            unset($_SESSION['errore_cellulare_ex']);
                        }
                        
                        if(isset($_SESSION['errore_codfiscale_ex']) && $_SESSION['errore_codfiscale_ex'] == 'true'){
                            echo "<h2>Il codice fiscale '" . $_SESSION['codfiscale_errato'] . "' è già in uso!</h2>";
                            unset($_SESSION['errore_codfiscale_ex']);
                        }
                        
                        ?>
                        <h1 class="titolo">Modifica Dati Utente</h1>
                        <table>
                            <tr>
                                <td class="col-mod-profilo">
                                    <form class="form" action="../res/processa_modifica_utente.php" method="post">
                                        <input class="input" type="hidden" name="id" value="<?php echo $utente['id']; ?>">

                                        <label>Nome:</label>
                                        <input style="width: 200px;" class="input" type="text" name="nome" value="<?php echo $utente['nome']; ?>" required><br>

                                        <label>Cognome:</label>
                                        <input style="width: 200px;" class="input" type="text" name="cognome" value="<?php echo $utente['cognome']; ?>" required><br>

                                        <label>Indirizzo di residenza:</label>
                                        <input style="width: 200px;" class="input" type="text" name="indirizzo" value="<?php echo $utente['indirizzo_di_residenza']; ?>" required><br>

                                        <label>Codice fiscale:</label>
                                        <input style="width: 200px;" class="input" type="text" name="fiscale" 
                                               pattern="[A-Za-z]{6}\d{2}[A-Za-z]\d{2}[A-Za-z]\d{3}[A-Za-z]" maxlength="16"
                                               title="Inserisci un codice fiscale italiano valido. (es.XXXXXX00X00X000X)"
                                               value="<?php echo $utente['codice_fiscale']; ?>" required><br>

                                        <label>Data di nascita:</label>
                                        <input style="width: 200px;" class="input" type="date" name="nascita" value="<?php echo $utente['data_di_nascita']; ?>" required><br>

                                        <label>Cellulare:</label>
                                        <input style="width: 200px;" class="input" type="text" name="cellulare" pattern="\d{10}" maxlength="10" title="Inserisci un numero di telefono valido (formato da 10 numeri)" value="<?php echo $utente['cellulare']; ?>" required>
                                        
                                        <br><br><br>
                                        <input class="btn" type="submit" value="Salva Modifiche">
                                    </form>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } 
                    else {
                        echo '<h2>Utente non trovato</h2>';
                    }
                }
                else {
                    echo '<h2>ID utente non valido</h2>';
                }
            ?>
        </div>
    </body>
</html>