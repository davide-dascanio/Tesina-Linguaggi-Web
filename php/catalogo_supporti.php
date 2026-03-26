<?php
    session_start();
    require_once('../res/funzioni.php');
?>


<?xml version = "1.0"?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Catalogo Prodotti</title>
        <link rel="stylesheet" href="../css/style_catalogo.css" />
        <link rel="stylesheet" href="../css/style_search.css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="../css/style_header.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
        <?php
            require_once('../res/header.php');
        ?>
        <div class="cont">
            <div class="container">
                <a class="btn" href="catalogo_tv.php">TV</a>
                <a class="btn" href="catalogo_soundbar.php">Soundbar</a>
                <a class="btn" href="catalogo_supporti.php">Supporti TV</a>
            </div>
            <table>
                <tr>
                    <td>
                        <input type="text" class="search-input" placeholder="Cerca...">
                        <button class="btn_stilizzato"><span class="material-symbols-outlined">search</span></button>
                    </td>
                    <td>
                        <label class="scritta">Ordina per:</label>
                        <select id="ordina">
                            <option value="" selected disabled>Scegli ordinamento</option>
                            <option value="nomeCrescente">(A-Z)</option>
                            <option value="nomeDecrescente">(Z-A)</option>
                            <option value="prezzoCrescente">Prezzo crescente</option>
                            <option value="prezzoDecrescente">Prezzo decrescente</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php
            if(isset($_SESSION['id'])){
                $id_utente = $_SESSION['id'];
            }


            // Inizializza la variabile booleana per verificare se ci sono prodotti di tipo 'supporti'
            $supportiPresenti = false;


            // Leggi il file XML del catalogo
            $xmlFile = '../xml/catalogo_prodotti.xml';
            $dom = new DOMDocument();
            $dom->load($xmlFile);


            // Ottieni la lista di prodotti
            $prodotti = $dom->getElementsByTagName('prodotto');


            // Itera attraverso i prodotti e stampali
            foreach ($prodotti as $prodotto) {
                $id_prodotto = $prodotto->getElementsByTagName('id_prodotto')->item(0)->nodeValue;
                $nome = $prodotto->getElementsByTagName('nome')->item(0)->nodeValue;
                $descrizione = $prodotto->getElementsByTagName('descrizione')->item(0)->nodeValue;
                $prezzo = $prodotto->getElementsByTagName('prezzo')->item(0)->nodeValue;
                $tipologia = $prodotto->getElementsByTagName('tipologia')->item(0)->nodeValue;
                $immagine = $prodotto->getElementsByTagName('immagine')->item(0)->nodeValue;

                $sconti_bonus = $prodotto->getElementsByTagName('sconti_bonus')->item(0);

                $sconto_generico = $sconti_bonus->getElementsByTagName('sconto_generico')->item(0);
                $sconto_generico_percentuale = $sconto_generico->nodeValue;        // valore percentuale
                $sconto_generico_attivo = $sconto_generico->getAttribute('attivo');  // 0 o 1

                
                $bonus_generico = $sconti_bonus->getElementsByTagName('bonus_generico')->item(0);
                $bonus_generico_crediti = $bonus_generico->nodeValue;           // valore crediti
                $bonus_generico_attivo = $bonus_generico->getAttribute('attivo');         // 0 o 1


                $bonus_personal = $sconti_bonus->getElementsByTagName('bonus_personalizzato')->item(0);
                $bonus_personal_attivo = $bonus_personal->getAttribute('attivo');
                $bonus_personal_crediti = $bonus_personal->getElementsByTagName('crediti')->item(0)->nodeValue;
                $bonus_personal_criterio = $bonus_personal->getElementsByTagName('criterio')->item(0);
                $bonus_personal_tipo = $bonus_personal_criterio->getAttribute('tipo');
                $bonus_personal_soglia = $bonus_personal_criterio->getElementsByTagName('soglia')->item(0)->nodeValue;
                $bonus_personal_datrif = $bonus_personal_criterio->getElementsByTagName('data_riferimento')->item(0)->nodeValue;


                $sconto_personal = $sconti_bonus->getElementsByTagName('sconto_personalizzato')->item(0);
                $sconto_personal_attivo = $sconto_personal->getAttribute('attivo');
                $sconto_personal_perc = $sconto_personal->getElementsByTagName('percentuale')->item(0)->nodeValue;
                $sconto_personal_criterio = $sconto_personal->getElementsByTagName('criterio')->item(0);
                $sconto_personal_tipo = $sconto_personal_criterio->getAttribute('tipo');
                $sconto_personal_soglia = $sconto_personal_criterio->getElementsByTagName('soglia')->item(0)->nodeValue;
                $sconto_personal_datrif = $sconto_personal_criterio->getElementsByTagName('data_riferimento')->item(0)->nodeValue;



                if ($tipologia !== 'supporti') {
                    continue; // Salta il prodotto se la tipologia non è 'supporti'
                }
                

                // Imposta la variabile booleana a true se almeno un prodotto è di tipo 'supporti'
                $supportiPresenti = true;


                // Calcola il prezzo finale per l'ordinamento PRIMA di aprire il div
                // Per i loggati chiama getDettaglioScontiBonus 
                // che restituisce anche tutto il necessario per il tooltip
                if (isset($_SESSION['loggato']) && $_SESSION['loggato'] == true) {
                    $xmlPath = "../xml/catalogo_prodotti.xml";
                    $dettaglio = getDettaglioScontiBonus($xmlPath, $id_prodotto, $prezzo);
                    $prezzoPerOrdinamento = $dettaglio['prezzo_finale'];
                    $bonusTotale = $dettaglio['bonus_totale'];
                } else {
                    // default visitatore
                    $dettaglio = null;
                    $prezzoPerOrdinamento = $prezzo;
                    $bonusTotale = 0;
                }


                // Stampa le informazioni del prodotto
                // data-prezzo è un attributo HTML personalizzato serve a portare dati dal server al JavaScript senza mostrarli all'utente
                echo '<div class="prodotto" data-prezzo="' . $prezzoPerOrdinamento . '">';  
                echo '<h1 class="nome">';
                if(isset($_SESSION['loggato'])){
                    $gestore = $_SESSION['gestore'];
                    $admin = $_SESSION['ammin'];
                    $cliente = $_SESSION['cliente'];
                    $prezzoFinale = $prezzoPerOrdinamento;
                    echo $nome;

                    if($gestore == 1){

                        // ════════════════════════════════════════════════════════
                        //  RAMO GESTORE
                        // ════════════════════════════════════════════════════════

                        echo '</h1>';
                        echo '<table class="table">';
                        echo '<tr>';
                        
                        // Colonna pulsanti
                        echo '<td>';
                        echo '<a class="btn1"style="margin-left:10vw;" title="Lista delle domande" href="lista_domande.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome .'&tipologia='. $tipologia .'">Lista delle domande</a>';
                        echo '<a class="btn1" style="margin-left:10vw;"title="Lista delle recensioni" href="lista_recensioni.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome .'&tipologia='. $tipologia .'">Liste delle recensioni</a>';
                        echo '<a class="btn1" style="margin-left:10vw;" href="modifica_prodotti_form.php?id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">Modifica prodotto</a>';
                        echo '<a class="btn1" style="margin-left:10vw;" href="gestisci_sconti_form.php?id_prodotto=' . $id_prodotto . '&tipologia=' . $tipologia . '">Gestisci Sconti</a>';
                        echo '</td>';

                        // Colonna immagine
                        echo '<td class="td">';
                        echo '<div class="box">';
                        echo '<img class="img" src="' . $immagine . '" alt="' . $nome . '">';
                        echo '</div>';
                        echo '</td>';

                        // Colonna info
                        echo '<td class="td">';
                        echo '<p class="des">' . $descrizione . '</p>';

                        if ($sconto_generico_attivo == 1 || $bonus_generico_attivo == 1 || $bonus_personal_attivo == 1 || $sconto_personal_attivo == 1) {
                            echo "<p id='successo'>Sconti/Bonus presenti su questo prodotto</p>";
                        }

                        // TOOLTIP GESTORE: mostra TUTTI i 4 sconti/bonus con criteri (perchè li deve gestire)
                        echo '<table>';
                            echo '<tr>';
                                echo '<td>';
                                    echo "<div class='tooltip'>";
                                        echo "<span class='tooltiptext'>";
                                            echo "<ul>";
                                                // Sconto generico
                                                if ($sconto_generico_attivo == 1)
                                                    echo "<li><strong>Sconto generico:</strong> $sconto_generico_percentuale% (attivo)</li>";
                                                else
                                                    echo "<li><strong>Sconto generico:</strong> non attivo</li>";

                                                // Sconto personalizzato
                                                if ($sconto_personal_attivo == 1)
                                                    echo "<li><strong>Sconto personalizzato:</strong> $sconto_personal_perc% &mdash; " . etichettaCriterio($sconto_personal_tipo, $sconto_personal_soglia, $sconto_personal_datrif) . " (attivo)</li>";
                                                else
                                                    echo "<li><strong>Sconto personalizzato:</strong> non attivo</li>";

                                                // Bonus generico
                                                if ($bonus_generico_attivo == 1)
                                                    echo "<li><strong>Bonus generico:</strong> $bonus_generico_crediti crediti (attivo)</li>";
                                                else
                                                    echo "<li><strong>Bonus generico:</strong> non attivo</li>";
                                                
                                                // Bonus personalizzato
                                                if ($bonus_personal_attivo == 1)
                                                    echo "<li><strong>Bonus personalizzato:</strong> $bonus_personal_crediti crediti &mdash; " . etichettaCriterio($bonus_personal_tipo, $bonus_personal_soglia, $bonus_personal_datrif) . " (attivo)</li>";
                                                else
                                                    echo "<li><strong>Bonus personalizzato:</strong> non attivo</li>";
                                            echo "</ul>";
                                        echo "</span>";
                                        echo "<i id='simbolo' class='material-symbols-outlined'>info</i>";
                                    echo "</div>";
                                echo '</td>';
                                echo '<td>';
                                    echo "<p class = 'prezzo'>Prezzo base: " . $prezzo . " €</p>";
                                echo '</td>';
                            echo '</tr>';
                        echo '</table>';
                        echo '</td>';

                        echo '</tr>';
                        echo '</table>';
                        echo '</div>';
 
                    }elseif ($cliente == 1) {

                        // ════════════════════════════
                        //  RAMO CLIENTE
                        // ════════════════════════════  

                        echo '</h1>';
                        echo '<table class="table">';
                        echo '<tr>';

                        // Colonna pulsanti
                        echo '<td>';
                        echo '<a class="btn1" style="margin-left:10vw;" title="Lista delle domande" href="lista_domande.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&tipologia=' . $tipologia . '">Lista delle domande</a>';
                        echo '<a class="btn1" style="margin-left:10vw;" title="Lascia una domanda" href="domande_prodotti.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&tipologia=' . $tipologia . '">Scrivi una domanda</a>';
                        echo '<a class="btn1" style="margin-left:10vw;" title="Lascia una recensione" href="recensione_cliente.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&tipologia=' . $tipologia . '">Scrivi una recensione</a>';
                        echo '<a class="btn1" style="margin-left:10vw;" title="Lista delle recensioni" href="lista_recensioni.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&tipologia=' . $tipologia . '">Lista delle recensioni</a>';
                        echo '</td>';

                        // Colonna immagine
                        echo '<td class="td">';
                        echo '<div class="box">';
                        echo '<img class="img" src="' . $immagine . '" alt="' . $nome . '">';
                        echo '</div>';
                        echo '</td>';

                        // Colonna info
                        echo '<td class="td">';
                        echo '<p class="des">' . $descrizione . '</p>';

                        if ($dettaglio['sconto_generico']['applicato']       ||
                            $dettaglio['sconto_personalizzato']['applicato'] ||
                            $dettaglio['bonus_generico']['applicato']         ||
                            $dettaglio['bonus_personalizzato']['applicato']) {
                            echo "<p id='successo'>Sconti/Bonus attivi per te su questo prodotto</p>";
                        }

                        // TOOLTIP CLIENTE: mostra solo ciò che si applica a lui
                        echo '<table>';
                            echo '<tr>';
                                echo '<td>';
                                    echo "<div class='tooltip'>";
                                        echo "<span class='tooltiptext'>";
                                            echo "<ul>";
                                                echo "<li><strong>Prezzo base:</strong> $prezzo &euro;</li>";

                                                // Sconto generico (se attivo si applica sempre al cliente)
                                                if ($dettaglio['sconto_generico']['applicato']) {
                                                    $v = $dettaglio['sconto_generico']['valore'];
                                                    echo "<li><strong>Sconto generico:</strong> -$v%</li>";
                                                }

                                                // Sconto personalizzato (solo se il cliente soddisfa il criterio)
                                                if ($dettaglio['sconto_personalizzato']['applicato']) {
                                                    $p = $dettaglio['sconto_personalizzato']['percentuale'];
                                                    $et  = etichettaCriterio(
                                                        $dettaglio['sconto_personalizzato']['tipo'],
                                                        $dettaglio['sconto_personalizzato']['soglia'],
                                                        $dettaglio['sconto_personalizzato']['data_rif']
                                                    );
                                                    echo "<li><strong>Sconto personalizzato:</strong> -$p% ($et)</li>";
                                                }

                                                // Bonus generico (se attivo si applica sempre al cliente)
                                                if ($dettaglio['bonus_generico']['applicato']) {
                                                    $v = $dettaglio['bonus_generico']['valore'];
                                                    echo "<li><strong>Bonus dopo acquisto:</strong> +$v crediti</li>";
                                                }

                                                // Bonus personalizzato (solo se il cliente soddisfa il criterio)
                                                if ($dettaglio['bonus_personalizzato']['applicato']) {
                                                    $cr = $dettaglio['bonus_personalizzato']['crediti'];
                                                    $et = etichettaCriterio(
                                                        $dettaglio['bonus_personalizzato']['tipo'],
                                                        $dettaglio['bonus_personalizzato']['soglia'],
                                                        $dettaglio['bonus_personalizzato']['data_rif']
                                                    );
                                                    echo "<li><strong>Bonus personalizzato dopo acquisto:</strong> +$cr crediti ($et)</li>";
                                                }
                                            echo "</ul>";
                                        echo "</span>";
                                        echo "<i id='simbolo' class='material-symbols-outlined'>info</i>";
                                    echo "</div>";
                                echo '</td>';
                                echo '<td>';
                                    echo "<p class = 'prezzo'>Prezzo Finale: " . $prezzoFinale . " €</p>";
                                echo '</td>';
                            echo '</tr>';
                        echo '</table>';
                        echo '<div class="linea">';
                        echo '<form action="catalogo_' . $tipologia . '.php" method="post">';
                        echo '<input type="hidden" name="id_prodotto" value="' . $id_prodotto . '">';
                        echo '<input type="hidden" name="nome" value="' . $nome . '">';
                        echo '<input type="hidden" name="bonus" value="' . $bonusTotale . '">';
                        echo '<input type="hidden" name="tipologia" value="' . $tipologia . '">';
                        echo '<input type="hidden" name="prezzo" value="' . $prezzo . '">';
                        echo '<input type="hidden" name="prezzoFinale" value="' . $prezzoFinale . '">';
                        echo '<input class="input" type="number" name="quantita" value="0" min="1" step="1" size="3" max="99" />';
                        echo '<button style="border:none; background:none; cursor:pointer;" type="submit" name="azione" value="aggiungi_al_carrello"><span id="cart" class="material-symbols-outlined">add_shopping_cart</span></button>';
                        echo '</form>';
                        echo '</div>';

                        echo '</td>';
                        echo '</tr>';
                        echo '</table>';
                        echo '</div>';

                    }elseif ($admin == 1) {

                        // ════════════════════════════════════════════════════════
                        //  RAMO AMMINISTRATORE
                        // ════════════════════════════════════════════════════════

                        echo '</h1>';
                        echo '<table class="table">';
                        echo '<tr>';

                        // Colonna pulsanti
                        echo '<td>';
                        echo '<a class="btn1" style="margin-left:10vw;" title="Lista delle domande" href="lista_domande.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&tipologia=' . $tipologia . '">Lista delle domande</a>';
                        echo '<a class="btn1" style="margin-left:10vw;" title="Lista delle recensioni" href="lista_recensioni.php?id_prodotto=' . $id_prodotto . '&nome=' . $nome . '&tipologia=' . $tipologia . '">Lista delle recensioni</a>';
                        echo '</td>';

                        // Colonna immagine
                        echo '<td class="td">';
                        echo '<div class="box">';
                        echo '<img class="img" src="' . $immagine . '" alt="' . $nome . '">';
                        echo '</div>';
                        echo '</td>';

                        // Colonna info
                        echo '<td class="td">';
                        echo '<p class="des">' . $descrizione . '</p>';


                        if ($sconto_generico_attivo == 1 || $bonus_generico_attivo == 1 || $bonus_personal_attivo == 1 || $sconto_personal_attivo == 1) {
                            echo "<p id='successo'>Sconti/Bonus presenti su questo prodotto</p>";
                        }

                        // TOOLTIP AMMINISTRATORE: mostra TUTTI i 4 sconti/bonus_generico_percentuale con criteri (perchè li deve supervisionare)
                        echo '<table>';
                            echo '<tr>';
                                echo '<td>';
                                    echo "<div class='tooltip'>";
                                        echo "<span class='tooltiptext'>";
                                            echo "<ul>";
                                                echo "<li><strong>Prezzo base:</strong> $prezzo &euro;</li>";
                                                // Sconto generico
                                                if ($sconto_generico_attivo == 1)
                                                    echo "<li><strong>Sconto generico:</strong> $sconto_generico_percentuale% (attivo)</li>";
                                                else
                                                    echo "<li><strong>Sconto generico:</strong> non attivo</li>";

                                                // Sconto personalizzato
                                                if ($sconto_personal_attivo == 1)
                                                    echo "<li><strong>Sconto personalizzato:</strong> $sconto_personal_perc% &mdash; " . etichettaCriterio($sconto_personal_tipo, $sconto_personal_soglia, $sconto_personal_datrif) . " (attivo)</li>";
                                                else
                                                    echo "<li><strong>Sconto personalizzato:</strong> non attivo</li>";
                                                
                                                // Bonus generico
                                                if ($bonus_generico_attivo == 1)
                                                    echo "<li><strong>Bonus generico:</strong> $bonus_generico_crediti crediti (attivo)</li>";
                                                else
                                                    echo "<li><strong>Bonus generico:</strong> non attivo</li>";

                                                // Bonus personalizzato
                                                if ($bonus_personal_attivo == 1)
                                                    echo "<li><strong>Bonus personalizzato:</strong> $bonus_personal_crediti crediti &mdash; " . etichettaCriterio($bonus_personal_tipo, $bonus_personal_soglia, $bonus_personal_datrif) . " (attivo)</li>";
                                                else
                                                    echo "<li><strong>Bonus personalizzato:</strong> non attivo</li>";
                                            echo "</ul>";
                                        echo "</span>";
                                        echo "<i id='simbolo' class='material-symbols-outlined'>info</i>";
                                    echo "</div>";
                                echo '</td>';
                                echo '<td>';
                                    echo "<p class = 'prezzo'>Prezzo di vendita : " . $prezzoFinale . " €</p>";
                                echo '</td>';
                            echo '</tr>';
                        echo '</table>';
                        echo '</td>';

                        echo '</tr>';
                        echo '</table>';
                        echo '</div>';
                    }

                }else{

                    // ════════════════════════════════════════════════════════
                    //  RAMO VISITATORE
                    // ════════════════════════════════════════════════════════
                    
                    echo $nome;
                    echo '</h1>';
                    echo '<table class="table">';
                    echo '<tr>';

                    // Colonna immagine
                    echo '<td class="td">';
                    echo '<div class="box">';
                    echo '<img class="img" src="' . $immagine . '" alt="' . $nome . '">';
                    echo '</div>';
                    echo '</td>';

                    // Colonna info
                    echo '<td class="td">';
                    echo '<p class="des">' . $descrizione . '</p>';
                    echo '<p class="prezzo">Prezzo: ' . $prezzo . '€</p>';
                    echo '<a href="login_cliente.php"><span id="cart" class="material-symbols-outlined">add_shopping_cart</span></a>';
                    echo '</td>';
                    
                    echo '</tr>';
                    echo '</table>';
                    echo '</div>';
                }
            }


            if (!$supportiPresenti) {
                echo '<h3 class="titolo">Il catalogo delle supporti è vuoto :(</h3>';
            }
            ?>


            <?php
            // Inizializza o ottieni il carrello dalla sessione
            // Verifica se l'azione è "aggiungi_al_carrello"
            if (isset($_POST['azione']) && $_POST['azione'] === 'aggiungi_al_carrello') {
                $id_prodotto = $_POST['id_prodotto'];
                $nome = $_POST['nome'];
                $prezzo = $_POST['prezzo'];
                $prezzoFinale = $_POST['prezzoFinale'];
                $quantita = $_POST['quantita'];
                $bonus = $_POST['bonus'];
                
                
                if(!isset($_SESSION['carrello'])){
                    // Aggiungi il prodotto al carrello subito
                    $_SESSION['carrello'][] = array(
                        'id_prodotto'  => $id_prodotto,
                        'nome'         => $nome,
                        'prezzo'       => $prezzo,
                        'bonus'        => $bonus,
                        'quantita'     => $quantita,
                        'prezzoFinale' => $prezzoFinale,
                    );
                }else{
                    // Se il prodotto è già nel carrello, incrementa la quantità invece di duplicarlo
                    $trovato = false;
                    foreach ($_SESSION['carrello'] as $i => $item) {
                        if ($item['id_prodotto'] == $id_prodotto) {
                            $_SESSION['carrello'][$i]['quantita'] += $quantita;
                            $trovato = true;
                            break;
                        }
                    }

                    if (!$trovato) {
                        // Aggiungi il prodotto al carrello
                        $_SESSION['carrello'][] = array(
                            'id_prodotto'  => $id_prodotto,
                            'nome'         => $nome,
                            'prezzo'       => $prezzo,
                            'bonus'        => $bonus,
                            'quantita'     => $quantita,
                            'prezzoFinale' => $prezzoFinale,
                        );
                    }
                }
                
            }
            ?>



            <script>
                // Quando il documento è caricato
                $(document).ready(function() {


                    // Ricerca per nome
                    // Associo un'azione al bottone di ricerca "btn_stilizzato"
                    $('.btn_stilizzato').on('click', function() {
                        var searchText = $('.search-input').val().toLowerCase();
                        $('.prodotto').each(function() {
                            var titolo = $(this).find('.nome').text().toLowerCase();
                            $(this).toggle(titolo.indexOf(searchText) !== -1);
                        });
                    });

                    // Ricerca per nome
                    // Associo un'azione alla barra di ricerca quando scrivo qualcosa sulla tastiera
                    $('.search-input').on('keyup', function() {
                        var searchText = $(this).val().toLowerCase();
                        $('.prodotto').each(function() {
                            var titolo = $(this).find('.nome').text().toLowerCase();
                            $(this).toggle(titolo.indexOf(searchText) !== -1);
                        });
                    });



                    // Ordinamento
                    $('#ordina').on('change', function() {
                        var selectedOption = $(this).val();

                        var prodottiArray = $('.prodotto').toArray();

                        prodottiArray.sort(function(a, b) {
                            if (selectedOption === 'prezzoCrescente' || selectedOption === 'prezzoDecrescente') {
                                var prezzoA = parseFloat($(a).attr('data-prezzo'));
                                var prezzoB = parseFloat($(b).attr('data-prezzo'));
                                return selectedOption === 'prezzoCrescente' ? prezzoA - prezzoB : prezzoB - prezzoA;
                            } else {
                                var nomeA = $(a).find('.nome').text().toLowerCase().trim();
                                var nomeB = $(b).find('.nome').text().toLowerCase().trim();
                                return selectedOption === 'nomeCrescente' ? nomeA.localeCompare(nomeB) : nomeB.localeCompare(nomeA);
                            }
                        });


                        var cont = $('.cont');
                        $('.prodotto').detach();
                        $.each(prodottiArray, function(i, prodotto) {
                            cont.append(prodotto);
                        });
                    });
                });
            </script>
        </div>
    </body>
</html>