<?php
    session_start();
    require_once('../res/connessione1.php');


    // Verifica se il cliente è loggato
    if (!isset($_SESSION['loggato'])) {
        header("Location: login_cliente.php");
        exit();
    }
    
    $cliente = $_SESSION['cliente'];
    if ($cliente == 0){
        // non siamo clienti
        header("Location: accesso_negato.php");
        exit();
    }

    $id_utente = $_SESSION['id'];
    
    $sql_select = "SELECT * FROM utenti WHERE id = '$id_utente'";
    if($result = $connessione->query($sql_select)){
        if($result->num_rows === 1){
            $row = $result->fetch_array(MYSQLI_ASSOC);
        }
        $_SESSION['crediti'] = $row['crediti'];
    }
    


    //print_r($_POST);
    //print_r($_SESSION);

    // Gestisci le azioni di rimuovere il prodotto o modificare la quantità
    if (isset($_POST['azione'])) {
        
        if ($_POST['azione'] === 'svuota_carrello') {
            // Azione per svuotare il carrello
            unset($_SESSION['carrello']);
            header("Location: cart.php");
            exit();

        } elseif ($_POST['azione'] === 'rimuovi_prodotto') {
            // Azione per rimuovere singolarmente un prodotto
            $index = $_POST['index'];
            if (isset($_SESSION['carrello'][$index])) {
                unset($_SESSION['carrello'][$index]);
                $_SESSION['carrello'] = array_values($_SESSION['carrello']); // Resetta gli indici dell'array
            }
            header("Location: cart.php");
            exit();

        } elseif ($_POST['azione'] === 'modifica_quantita') {
            // Azione per modificare la quantità di un prodotto
            $index = $_POST['index'];
            $nuova_quantita = $_POST['nuova_quantita'];

            if (isset($_SESSION['carrello'][$index]) && $nuova_quantita >= 1) {
                $_SESSION['carrello'][$index]['quantita'] = $nuova_quantita;
            }
            header("Location: cart.php");
            exit();

        } elseif ($_POST['azione'] === 'conferma_acquisto') {

            // Calcola il totale speso e il totale bonus da accreditare
            $totale_acquisto = 0;
            $bonusDaAggiungere = 0;

            foreach ($_SESSION['carrello'] as $prodotto_carrello) {
                $prezzoFinale = $prodotto_carrello['prezzoFinale'];
                
                $totale_acquisto += $prezzoFinale * $prodotto_carrello['quantita'];
                $bonusDaAggiungere += $prodotto_carrello['bonus'] * $prodotto_carrello['quantita'];
            }



            if ($_SESSION['crediti'] >= $totale_acquisto) {
                // Aggiorna i crediti: sottrai spesa e aggiungi bonus
                $_SESSION['crediti'] = $_SESSION['crediti'] - $totale_acquisto + $bonusDaAggiungere;
            
                // Aggiorna i crediti nella tabella degli utenti
                $connessione->query("UPDATE utenti SET crediti = {$_SESSION['crediti']} WHERE id = {$id_utente}");

                if (!empty($_SESSION['carrello'])) {
                    $xmlPath = '../xml/storico_acquisti.xml';
                
                    // Carica il documento XML 
                    $dom = new DomDocument('1.0', 'UTF-8');
                    $dom->preserveWhiteSpace = false;
                    $dom->formatOutput = true;

                    $dom->load($xmlPath);

                    foreach ($_SESSION['carrello'] as $prodotto_carrello) {
                        // Crea l'elemento "acquisto" per ogni prodotto nel carrello
                        $acquisto = $dom->createElement('acquisto');

                        // Aggiungi l'id utente come attributo all'elemento "acquisto"
                        $acquisto->setAttribute('id_utente', $id_utente);
                        
                        // Aggiungi data e ora come elementi figli
                        $acquisto->appendChild($dom->createElement('data', date('Y-m-d')));
                        $acquisto->appendChild($dom->createElement('ora', date('H:i:s')));
                        
                        // Aggiungi gli altri dettagli del prodotto
                        $acquisto->appendChild($dom->createElement('id_prodotto', $prodotto_carrello['id_prodotto']));
                        $acquisto->appendChild($dom->createElement('nome_prodotto', $prodotto_carrello['nome']));
                        $acquisto->appendChild($dom->createElement('prezzo_unitario', $prodotto_carrello['prezzo']));
                        $acquisto->appendChild($dom->createElement('quantita', $prodotto_carrello['quantita']));
                        $acquisto->appendChild($dom->createElement('prezzo_scontato', $prodotto_carrello['prezzoFinale']));
                        $acquisto->appendChild($dom->createElement('bonus', $prodotto_carrello['bonus']));

                        // Calcola e aggiungi il prezzo totale come elemento separato
                        $prezzo_totale_riga = number_format($prodotto_carrello['prezzoFinale'] * $prodotto_carrello['quantita'], 2, '.', '');
                        $acquisto->appendChild($dom->createElement('prezzo_totale', $prezzo_totale_riga));
                        
                        // Aggiungi l'elemento "acquisto" all'elemento radice "storico_acquisti"
                        $dom->documentElement->appendChild($acquisto);
                    }
                    

                    // Salva il DOM nel file storico_acquisti.xml
                    $dom->save($xmlPath);
                
                    // Svuota il carrello dopo l'acquisto
                    unset($_SESSION['carrello']);


                    $messaggio = "Acquisto confermato!";
                }
            }else {
                $messaggio = "Non hai abbastanza crediti per effettuare l'acquisto.";
            }
        }
    }
?>

<?xml version = "1.0"?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Il tuo carrello</title>
        <link rel="stylesheet" href="../css/style_standard.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css">
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="contenitore">
            <h1 class="titolo">Il Tuo Carrello</h1>
            <?php
                if(isset($messaggio)){
                    if($messaggio == "Acquisto confermato!")
                        echo '<h2 id="successo">' . $messaggio . '</h2>';
                    else
                        echo '<h2>' . $messaggio . '</h2>';
                }
            ?>
            <?php
                // Leggi il file XML del catalogo
                $xmlFile = '../xml/catalogo_prodotti.xml'; 
                $dom = new DOMDocument();
                $dom->load($xmlFile);

                // Ottieni la lista di prodotti
                $prodottiCatalogo = $dom->getElementsByTagName('prodotto');


                // Verifica se il carrello contiene prodotti
                if (!empty($_SESSION['carrello'])) {
                    echo '<table>';
                    echo '<tr>';
                    echo '<th>Prodotto</th>';
                    echo '<th>Quantità</th>';
                    echo '<th>Prezzo Base</th>';
                    echo '<th>Prezzo Finale</th>';
                    echo '<th>Prezzo Totale</th>';
                    echo '<th>Bonus Totale</th>';
                    echo '<th>Modifica Quantità</th>';
                    echo '<th>Rimuovi Prodotto</th>';
                    echo '</tr>';
                    
                    // Inizializziamo variabili 
                    $totale_ordine = 0;
                    $bonus_ordine = 0;

                    foreach ($_SESSION['carrello'] as $index => $prodotto_carrello) {

                        // Verifica che il prodotto esista ancora nel catalogo (il gestore potrebbe eliminare un prodotto quando noi lo abbiamo già messo nel carrello)
                        $trovato = false;
                        foreach ($prodottiCatalogo as $prodottoCatalogo) {
                            $id_prodotto = $prodottoCatalogo->getElementsByTagName('id_prodotto')->item(0)->nodeValue;
                            if ($id_prodotto == $prodotto_carrello['id_prodotto']) {
                                $trovato = true;
                                break;
                            }
                        }

                        // Output del risultato
                        if (!$trovato) {
                            echo 'Il valore '. $prodotto_carrello['id_prodotto'] . 'non esiste nella lista XML di catalogo.';
                            unset($_SESSION['carrello'][$index]);
                            $_SESSION['carrello'] = array_values($_SESSION['carrello']); // Resetta gli indici dell'array
                            continue;
                        }

                        

                        // Calcola il totale dell'ordine e il totale bonus da accreditare
                        $prezzoFinale = $prodotto_carrello['prezzoFinale'];
                        
                        $totale_ordine += $prezzoFinale * $prodotto_carrello['quantita'];
                        $bonus_ordine += $prodotto_carrello['bonus'] * $prodotto_carrello['quantita'];


                        echo '<tr>';
                        echo '<td>' . $prodotto_carrello['nome'] . '</td>';
                        echo '<td>' . $prodotto_carrello['quantita'] . '</td>';
                        echo '<td>' . $prodotto_carrello['prezzo'] . '€</td>';  // Prezzo unitario
                    
                        if ($prodotto_carrello['prezzoFinale'] != $prodotto_carrello['prezzo']) {
                            echo '<td>' . $prodotto_carrello['prezzoFinale'] . '€</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                    
                        $prezzoTotale = number_format($prodotto_carrello['prezzoFinale'] * $prodotto_carrello['quantita'], 2, '.', '');
                        echo '<td>' . $prezzoTotale . '€</td>';
                        

                        // Aggiunta della cella per il Bonus Totale
                        $bonusTotale = $prodotto_carrello['bonus'] * $prodotto_carrello['quantita'];
                        echo '<td>' . $bonusTotale . ' crediti </td>';

                        echo '<td>';
                        echo '<form action="cart.php" method="post">';
                        echo '<input type="hidden" name="index" value="' . $index . '">';
                        echo '<input class="input" style="width:50px;margin-bottom:0px;" type="number" name="nuova_quantita" value="' . $prodotto_carrello['quantita'] . '" min="1" max="99">';
                        echo '<button class="done" type="submit" name="azione" value="modifica_quantita">CONFERMA<span class="material-symbols-outlined" id="done">done_outline</span></button>';
                        echo '</form>';
                        echo '</td>';
                        echo '<td>';
                        echo '<form action="cart.php" method="post">';
                        echo '<input type="hidden" name="index" value="' . $index . '">';
                        echo '<button class="done" type="submit" name="azione" value="rimuovi_prodotto"><span class="material-symbols-outlined" id="done">delete</span></button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    
                    echo '<tr>';
                    echo '<td colspan="3">Totale ordine: <strong>' . number_format($totale_ordine, 2, '.', '') . '€</strong></td>';
                    echo '<td colspan="2">Bonus totale: <strong>' . $bonus_ordine . ' crediti</strong></td>';
                    echo '<td colspan="3">Crediti disponibili: ' . $_SESSION['crediti'] . '€</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td colspan="8">Indirizzo di Consegna: <input style="margin-bottom:0px;" class="input" type="text" name="indirizzo_consegna" value="' . $_SESSION['indirizzo'] . '"></td>';
                    echo '</tr>';
                    
                    echo '</table>';
                    
                    echo '<form action="cart.php" method="post" style="display: flex; justify-content: space-between; margin-top: 5vh;">';
                    echo '<button style="margin-bottom:10px;" class="btn" type="submit" name="azione" value="svuota_carrello">Svuota Carrello</button>';
                    echo '<button style="margin-bottom:10px;" class="btn" type="submit" name="azione" value="conferma_acquisto">Acquista</button>';
                    echo '</form>';
                
                } else {
                    echo '<p style="margin-top: 50px;" class="titolo">Il carrello è vuoto</p>';
                }
            ?>
        </div>
    </body>
</html>