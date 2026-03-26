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

    if(!isset($_GET['nome'], $_GET['tipologia'], $_GET['id_prodotto'])){
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
    <title>Segnala un contributo</title>
    <link rel="stylesheet" href="../css/style_standard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>
    <?php
        require_once('../res/header.php');
    ?>
    <?php
        $tipologia = $_GET['tipologia'];
        $id_prodotto = $_GET['id_prodotto'];
        $nome = $_GET['nome'];
    ?>

    <div class="cont">
        <?php echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                    <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                    Torna al catalogo ' . $tipologia . '</a>'; ?>
        <h1 class="titolo">Scrivi la tua segnalazione</h1>
        <table>
            <?php
            
            // Il contributo segnalato è una domanda
            if(isset($_GET['id_domanda']) && !isset($_GET['id_risposta']) && isset($_GET['autore_domanda'], $_GET['testo_domanda'])){

                $id_contributo = $_GET['id_domanda'];
                $testo_contributo = $_GET['testo_domanda'];
                $autore_contributo = $_GET['autore_domanda'];
                $autore_segnalazione = $_SESSION['email'];
            ?>
                <tr>
                    <td>
                        <form class="form" action="../res/processa_segnalazione.php" method="post">
                            <input type="hidden" name="id_contributo" value="<?php echo $id_contributo; ?>">     
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="testo_contributo" value="<?php echo $testo_contributo; ?>">
                            <input type="hidden" name="autore_contributo" value="<?php echo $autore_contributo; ?>">
                            <input type="hidden" name="nome" value="<?php echo $nome; ?>">
                            <input type="hidden" name="autore_segnalazione" value="<?php echo $autore_segnalazione; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <textarea style="width:700px; height:100px; resize:none; vertical-align:top;" class="input" name="testo_segnalazione" placeholder="Testo Segnalazione Domanda..." required></textarea>
                            <br><br><br>
                            <button type="submit" name="report" class="btn">Invia Segnalazione</span></button>
                        </form>
                    </td>
                </tr>
            <?php

            // Il contributo segnalato è una risposta
            }elseif(isset($_GET['id_risposta']) && isset($_GET['autore_risposta'], $_GET['testo_risposta'])){

                $id_contributo = $_GET['id_risposta'];
                $testo_contributo = $_GET['testo_risposta'];
                $autore_contributo = $_GET['autore_risposta'];
                $autore_segnalazione = $_SESSION['email'];
            ?>
                <tr>
                    <td>
                        <form class="form" action="../res/processa_segnalazione.php" method="post">  
                            <input type="hidden" name="id_contributo" value="<?php echo $id_contributo; ?>">      
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="testo_contributo" value="<?php echo $testo_contributo; ?>">
                            <input type="hidden" name="autore_segnalazione" value="<?php echo $autore_segnalazione; ?>">
                            <input type="hidden" name="autore_contributo" value="<?php echo $autore_contributo; ?>">
                            <input type="hidden" name="nome" value="<?php echo $nome; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <textarea style="width:700px; height:100px; resize:none; vertical-align:top;" class="input" name="testo_segnalazione" placeholder="Testo Segnalazione Risposta..." required></textarea>
                            <br><br><br>
                            <button type="submit" name="report" class="btn">Invia Segnalazione</span></button>
                        </form>
                    </td>
                </tr>
            <?php

            // Il contributo segnalato è una recensione
            }elseif(isset($_GET['id_recensione']) && isset($_GET['autore_recensione'], $_GET['testo_recensione'])){

                $id_contributo = $_GET['id_recensione'];
                $testo_contributo = $_GET['testo_recensione'];
                $autore_contributo = $_GET['autore_recensione'];
                $autore_segnalazione = $_SESSION['email'];
            ?>
                <tr>
                    <td>
                        <form class="form" action="../res/processa_segnalazione.php" method="post">
                            <input type="hidden" name="id_contributo" value="<?php echo $id_contributo; ?>">      
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="testo_contributo" value="<?php echo $testo_contributo; ?>">
                            <input type="hidden" name="autore_segnalazione" value="<?php echo $autore_segnalazione; ?>">
                            <input type="hidden" name="autore_contributo" value="<?php echo $autore_contributo; ?>">
                            <input type="hidden" name="nome" value="<?php echo $nome; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="rec" value="rec">
                            <textarea style="width:700px; height:100px; resize:none; vertical-align:top;" class="input" name="testo_segnalazione" placeholder="Testo Segnalazione Recensione..." required></textarea>
                            <br><br><br>
                            <button type="submit" name="report" class="btn">Invia Segnalazione</span></button>
                        </form>
                    </td>
                </tr>
            <?php
            }else{
                echo '<tr><td>ID del contributo mancante</td></tr>';
            }
            ?>
        </table>
    </div>
</body>
</html>