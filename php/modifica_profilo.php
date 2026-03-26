<?php
    session_start();

    // Verifica se il cliente è loggato
    if (isset($_SESSION['id'])) {

        $cliente = $_SESSION['cliente'];
        if ($cliente == 0){
            // non siamo clienti
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
        <title>Modifica Dati Profilo</title>
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

                $id_utente = $_SESSION['id'];

                // Esegui una query per ottenere i dati del cliente
                $result = $connessione->query("SELECT * FROM utenti WHERE id = $id_utente");

                if ($result->num_rows == 1) {
                    $cliente = $result->fetch_assoc();

                    if(isset($_SESSION['errore_query']) && $_SESSION['errore_query'] == 'true'){
                        echo "<h2>Errore durante la richiesta!</h2>";
                        unset($_SESSION['errore_query']);
                    }

                    if(isset($_SESSION['errore_cellulare_ex']) && $_SESSION['errore_cellulare_ex'] == 'true'){
                        echo "<h2>Il numero di cellulare '" . $_SESSION['cellulare_errato'] . "' è già in uso!</h2>";
                        unset($_SESSION['errore_cellulare_ex']);
                        unset($_SESSION['mod_cellulare']);
                    }

                    ?>
                    <h1 class="titolo">Modifica Dati Profilo</h1>
                    <table>
                        <tr>
                            <td class="col-mod-profilo">
                                <form class="form" action="../res/processa_modifica_profilo.php" method="post">   
                                    <label>Nome:</label>
                                    <input class="input" type="text" name="nome" value="<?php echo $cliente['nome']; ?>" required><br>

                                    <label>Cognome:</label>
                                    <input class="input" type="text" name="cognome" value="<?php echo $cliente['cognome']; ?>" required><br>

                                    <label>Indirizzo di residenza:</label>
                                    <input class="input" type="text" name="indirizzo" value="<?php echo $cliente['indirizzo_di_residenza']; ?>" required><br>

                                    <label>Cellulare:</label>              
                                    <input class="input" type="text" name="cellulare" pattern="\d{10}" maxlength="10" title="Inserisci un numero di telefono valido (formato da 10 numeri)" value="<?php echo $cliente['cellulare']; ?>" required>

                                    <br><br><br>
                                    <input class="btn" type="submit" value="Salva Modifiche">
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
                }else{
                    echo '<h2>Utente non trovato</h2>';
                }
            ?>
        </div>
    </body>
</html>