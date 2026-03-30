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
        <title>Gestione Segnalazioni</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="../css/style_header.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="cont">
            <?php
                if(isset($_SESSION['contributo_gia_rimosso']) && $_SESSION['contributo_gia_rimosso'] == 'true'){
                    echo '<h2>Post già rimosso!!!</h2>';
                    unset($_SESSION['contributo_gia_rimosso']);
                }
                elseif(isset($_SESSION['segnalazione_approvata']) && $_SESSION['segnalazione_approvata'] == 'true'){
                    echo '<h2 id="successo">Segnalazione accettata, post rimosso con successo!!!</h2>';
                    unset($_SESSION['segnalazione_approvata']);   
                }
                elseif(isset($_SESSION['segnalazione_rifiutata']) && $_SESSION['segnalazione_rifiutata'] == 'true'){
                    echo '<h2>Segnalazione rifiutata, il post non è stato rimosso!!!</h2>';
                    unset($_SESSION['segnalazione_rifiutata']);
                }

                // Carica il file XML
                $xmlFile = '../xml/segnalazioni.xml';
                $dom = new DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->load($xmlFile);
                
                $segnalazioni = $dom->getElementsByTagName('segnalazione');

                // Controlla se esistono segnalazioni in attesa
                $hasPendingRequests = false;

                foreach ($segnalazioni as $segnalazione) {
                    if ($segnalazione->getAttribute('status') == 'In Attesa') {
                        $hasPendingRequests = true;
                        break; // inutile continuare, basta trovarne una
                    }
                }


                if ($hasPendingRequests) {

                    //C'è almeno una segnalazione da mostrare che è "In Attesa"

                    echo '<h1 class="titolo">Gestione Segnalazioni</h1>';

                    echo '<table>';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Autore Segnalazione</th>';  //autore_segnalazione
                    echo '<th>Post Segnalato</th>';   //testo_contributo
                    echo '<th>Autore Del Post Segnalato</th>';  //autore_contributo
                    echo '<th>Segnalazione</th>';  //testo_segnalazione
                    echo '<th>Azione</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($segnalazioni as $segnalazione) {
                        $status = $segnalazione->getAttribute('status');
                        
                        if ($status == 'In Attesa') {

                            $autoreSegnalazione = $segnalazione->getAttribute('autore_segnalazione');
                            $idContributo = $segnalazione->getAttribute('id_contributo');
                            $idProdotto = $segnalazione->getAttribute('id_prodotto');
                            $testoContributo = $segnalazione->getElementsByTagName('testo_contributo')->item(0)->nodeValue;
                            $autoreContributo = $segnalazione->getElementsByTagName('autore_contributo')->item(0)->nodeValue;
                            $testoSegnalazione = $segnalazione->getElementsByTagName('testo_segnalazione')->item(0)->nodeValue;


                            echo '<tr>';
                            echo "<td>$autoreSegnalazione</td>";
                            echo nl2br("<td>$testoContributo</td>");
                            echo "<td>$autoreContributo</td>";
                            echo nl2br("<td>$testoSegnalazione</td>");
                            echo '<td>';
                            echo '<form action="../res/approva_segnalazione.php" method="post">';
                            echo "<input type='hidden' name='id_contributo' value='$idContributo'>";
                            echo "<input type='hidden' name='id_prodotto' value='$idProdotto'>";
                            echo '<button class="done" type="submit" name="action" value="Approva">';
                            echo '<span class="done material-symbols-outlined">done</span>';
                            echo '</button>';
                            echo '<button class="done" type="submit" name="action" value="Rifiuta">';
                            echo '<span class="done material-symbols-outlined">close</span>';
                            echo '</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }

                    echo '</tbody>';
                    echo '</table>';
                }else{
                    //Nessuna segnalazione "In Attesa"
                    echo '<p class="titolo">Nessuna segnalazione attualmente in sospeso.</p>';
                }   
            ?>
        </div>
    </body>
</html>