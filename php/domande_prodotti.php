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

    // Verifica se sono stati passati i parametri GET
    if (!isset($_GET['id_prodotto']) || !isset($_GET['tipologia']) || !isset($_GET['nome'])) {
        header("Location: ../php/index.php");
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
        <title>Fai una Domanda</title>   
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="cont">
            <?php
                $id_prodotto = $_GET['id_prodotto'];
                $nome = $_GET['nome'];
                $tipologia = $_GET['tipologia'];
                $email_utente = $_SESSION['email'];


                echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                    <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                    Torna al catalogo ' . $tipologia . '</a>';
            ?>
            
            <h1 class="titolo">Fai una domanda per <?php echo $nome; ?>:</h1>

            <table>
                <tr>
                    <td>
                        <form class="form" method="post" action="../res/processa_prodotti.php">    
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="autore" value="<?php echo $email_utente; ?>">
                            <input type="hidden" name="nome" value="<?php echo $nome; ?>">

                            <textarea style="width:700px; height:100px; resize:none; vertical-align:top;" class="input" name="domanda" placeholder="Scrivi una domanda..." required></textarea>
                            <input class="btn" type="submit" value="Invia Domanda">
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
