<?php
    session_start();

    // Verifica se il gestore è loggato
    if (isset($_SESSION['id'])) {

        $gestore = $_SESSION['gestore'];
        if ($gestore == 0){
            // non siamo gestori
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
        <title>Aggiungi Prodotto</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <?php
            if(isset($_SESSION['successo_aggiunta_prodotto']) && $_SESSION['successo_aggiunta_prodotto'] == 'true'){
                echo '<h2 id="successo">Prodotto aggiunto con successo!!!</h2>';
                unset($_SESSION['successo_aggiunta_prodotto']);
            }
            if(isset($_SESSION['errore_immagine']) && $_SESSION['errore_immagine'] == 'true'){
                echo '<h2>Errore durante il caricamento dell\'immagine...</h2>';
                unset($_SESSION['errore_immagine']);
            }
            if(isset($_SESSION['errore_nome_esistente']) && $_SESSION['errore_nome_esistente'] == 'true'){
                echo '<h2>Nome prodotto già esistente...</h2>';
                unset($_SESSION['errore_nome_esistente']);
            }
        ?>
        <div class="cont">
            <h1 class="titolo">Aggiungi Prodotto</h1>
            <table>
                <tr>
                    <td>
                        <form class="form" action="../res/aggiungi_prodotto.php" method="post" enctype="multipart/form-data">
                            <label>Nome:</label>
                            <input class="input" type="text" name="nome" required><br>

                            <textarea style="width:500px; height:100px; resize:none; vertical-align:top;" class="input" name="descrizione" placeholder="Inserisci la DESCRIZIONE..."required></textarea><br>

                            <label>Prezzo:</label>
                            <input class="input" type="number" name="prezzo" min="0.01" step="0.01" required><br>

                            <label>Immagine:</label>
                            <input class="input" type="file" name="immagine" accept="image/*" required><br>

                            <label>Tipologia:</label>
                            <select name="tipologia" required>
                                <option value="" selected disabled>Scegli</option>
                                <option value="tv">TV</option>
                                <option value="soundbar">Soundbar</option>
                                <option value="supporti">Supporto TV</option>
                            </select>
                            <br><br><br>
                            <input class="btn" type="submit" value="Aggiungi Prodotto">
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
