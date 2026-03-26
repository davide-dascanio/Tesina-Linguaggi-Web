<?php
    session_start();
    require_once('../res/funzioni.php');

    // Verifica se il gestore è loggato
    if (isset($_SESSION['id'])) {

        $gestore = $_SESSION['gestore'];
        if ($gestore == 0) {
            // non siamo gestori
            header("Location: accesso_negato.php");
            exit();
        }
    } else {
        // Reindirizza alla pagina di accesso se non è loggato
        header("Location: login_cliente.php");
        exit();
    }

    if (!isset($_GET['id_prodotto'], $_GET['tipologia'])) {
        header("Location: ../php/index.php");
        exit();
    }

    $id_prodotto = $_GET['id_prodotto'];
    $tipologia = $_GET['tipologia'];

    $xmlFile = '../xml/catalogo_prodotti.xml';
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->load($xmlFile);

    $xpath = new DOMXPath($dom);
    $prodottoNode = $xpath->query("//prodotto[id_prodotto='$id_prodotto']")->item(0);

    if (!$prodottoNode) {
        header("Location: catalogo_$tipologia.php");
        exit();
    }

    $nome = $prodottoNode->getElementsByTagName('nome')->item(0)->nodeValue;
    $prezzo = $prodottoNode->getElementsByTagName('prezzo')->item(0)->nodeValue;

    $sb = $prodottoNode->getElementsByTagName('sconti_bonus')->item(0);

    $sg = $sb->getElementsByTagName('sconto_generico')->item(0);
    $sp = $sb->getElementsByTagName('sconto_personalizzato')->item(0);
    $sp_c = $sp->getElementsByTagName('criterio')->item(0);
    $bg = $sb->getElementsByTagName('bonus_generico')->item(0);
    $bp = $sb->getElementsByTagName('bonus_personalizzato')->item(0);
    $bp_c = $bp->getElementsByTagName('criterio')->item(0);

    $sg_attivo = $sg->getAttribute('attivo');
    $sg_valore = $sg->nodeValue;

    $sp_attivo = $sp->getAttribute('attivo');
    $sp_perc = $sp->getElementsByTagName('percentuale')->item(0)->nodeValue;
    $sp_tipo = $sp_c->getAttribute('tipo');
    $sp_soglia = $sp_c->getElementsByTagName('soglia')->item(0)->nodeValue;
    $sp_datarif = $sp_c->getElementsByTagName('data_riferimento')->item(0)->nodeValue;

    $bg_attivo = $bg->getAttribute('attivo');
    $bg_valore = $bg->nodeValue;

    $bp_attivo = $bp->getAttribute('attivo');
    $bp_crediti = $bp->getElementsByTagName('crediti')->item(0)->nodeValue;
    $bp_tipo = $bp_c->getAttribute('tipo');
    $bp_soglia = $bp_c->getElementsByTagName('soglia')->item(0)->nodeValue;
    $bp_datarif = $bp_c->getElementsByTagName('data_riferimento')->item(0)->nodeValue;
?>

<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestisci Sconti</title>
    <link rel="stylesheet" href="../css/style_standard.css">
    <link rel="stylesheet" href="../css/style_header.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script>
        function toggleData(select) {
            // Trova il campo data_riferimento dentro lo stesso form del select
            var dataInput = select.form.querySelector('input[name="data_riferimento"]');

            var dataLabel = select.form.querySelector('label[for="data_riferimento"]');

            if (select.value === 'crediti_da_data') {
                dataInput.style.display = 'inline';
                dataLabel.style.display = 'inline';
                dataInput.required = true;
            } else {
                dataInput.style.display = 'none';
                dataLabel.style.display = 'none';
                dataInput.required = false;
                dataInput.value = '';   // pulisce la data se cambia criterio
            }
        }
    </script>
</head>
<body>
    <?php 
        require_once('../res/header.php');
    ?>

    <div class="cont">

        <?php
            if (isset($_SESSION['successo_sconto'])) {
                echo '<h2 id="successo">' . $_SESSION['successo_sconto'] . '</h2>';
                unset($_SESSION['successo_sconto']);
            }
            if (isset($_SESSION['errore_sconto'])) {
                echo '<h2>' . $_SESSION['errore_sconto'] . '</h2>';
                unset($_SESSION['errore_sconto']);
            }
        ?>

        <a class="go-back" href="catalogo_<?php echo $tipologia; ?>.php">
            <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
            Torna al catalogo <?php echo $tipologia; ?>
        </a>
        <h1 class="titolo">Gestisci Sconti &mdash; <?php echo $nome; ?></h1>
        <p class="titolo">Prezzo base: <?php echo $prezzo; ?> &euro;</p>

        <table>

            <!-- ══════════════════════════════ -->
            <!-- SCONTO GENERICO                -->
            <!-- ══════════════════════════════ -->
            <tr>
                <td><strong>Sconto Generico</strong></td>
                <td>
                    <?php if ($sg_attivo == 1){ ?>
                        <p id="successo">Attivo &mdash; <?php echo $sg_valore; ?>%</p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="sconto_generico">
                            <input type="hidden" name="azione" value="disattiva">
                            <button class="btn" type="submit">Disattiva</button>
                        </form>
                    <?php }else{ ?>
                        <p>Non attivo</p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="sconto_generico">
                            <input type="hidden" name="azione" value="attiva">
                            <label>Percentuale (%):</label>
                            <input class="input" type="number" name="percentuale" min="1" max="100" step="1" required>
                            <button class="btn" type="submit">Attiva</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>

            <!-- ══════════════════════════════ -->
            <!-- SCONTO PERSONALIZZATO          -->
            <!-- ══════════════════════════════ -->
            <tr>
                <td><strong>Sconto Personalizzato</strong></td>
                <td>
                    <?php if ($sp_attivo == 1){ ?>
                        <p id="successo">
                            Attivo &mdash; <?php echo $sp_perc; ?>%
                            &mdash; <?php echo etichettaCriterio($sp_tipo, $sp_soglia, $sp_datarif); ?>
                        </p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="sconto_personalizzato">
                            <input type="hidden" name="azione" value="disattiva">
                            <button class="btn" type="submit">Disattiva</button>
                        </form>
                    <?php }else{ ?>
                        <p>Non attivo</p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="sconto_personalizzato">
                            <input type="hidden" name="azione" value="attiva">
                            <label>Percentuale (%):</label>
                            <input class="input" type="number" name="percentuale" min="1" max="100" step="1" required><br>
                            <label>Criterio:</label>
                            <select class="input" name="tipo_criterio" required onchange="toggleData(this)">
                                <option value="" selected disabled>Scegli criterio</option>
                                <option value="reputazione">Reputazione &ge; soglia</option>
                                <option value="mesi_registrato">Registrato da &ge; N mesi</option>
                                <option value="anni_registrato">Registrato da &ge; N anni</option>
                                <option value="crediti_totali">Spesa totale &ge; soglia &euro;</option>
                                <option value="crediti_da_data">Spesa da data &ge; soglia &euro;</option>
                            </select><br>
                            <label>Soglia:</label>
                            <input class="input" type="number" name="soglia" min="0" step="1" required><br>
                            <label for="data_riferimento">Data di riferimento:</label>
                            <input class="input" type="date" id="data_riferimento" name="data_riferimento"><br>
                            <button class="btn" type="submit">Attiva</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>

            <!-- ══════════════════════════════ -->
            <!-- BONUS GENERICO                 -->
            <!-- ══════════════════════════════ -->
            <tr>
                <td><strong>Bonus Generico</strong></td>
                <td>
                    <?php if ($bg_attivo == 1){ ?>
                        <p id="successo">Attivo &mdash; +<?php echo $bg_valore; ?> crediti</p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="bonus_generico">
                            <input type="hidden" name="azione" value="disattiva">
                            <button class="btn" type="submit">Disattiva</button>
                        </form>
                    <?php }else{ ?>
                        <p>Non attivo</p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="bonus_generico">
                            <input type="hidden" name="azione" value="attiva">
                            <label>Crediti da assegnare:</label>
                            <input class="input" type="number" name="crediti" min="1" step="1" required>
                            <button class="btn" type="submit">Attiva</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>

            <!-- ══════════════════════════════ -->  
            <!-- BONUS PERSONALIZZATO           -->
            <!-- ══════════════════════════════ -->
            <tr>
                <td><strong>Bonus Personalizzato</strong></td>
                <td>
                    <?php if ($bp_attivo == 1){ ?>
                        <p id="successo">
                            Attivo &mdash; +<?php echo $bp_crediti; ?> crediti
                            &mdash; <?php echo etichettaCriterio($bp_tipo, $bp_soglia, $bp_datarif); ?>
                        </p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="bonus_personalizzato">
                            <input type="hidden" name="azione" value="disattiva">
                            <button class="btn" type="submit">Disattiva</button>
                        </form>
                    <?php }else{ ?>
                        <p>Non attivo</p>
                        <form action="../res/gestisci_sconti.php" method="post">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                            <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
                            <input type="hidden" name="tipo_sconto" value="bonus_personalizzato">
                            <input type="hidden" name="azione" value="attiva">
                            <label>Crediti da assegnare:</label>
                            <input class="input" type="number" name="crediti" min="1" step="1" required><br>
                            <label>Criterio:</label>
                            <select class="input" name="tipo_criterio" required onchange="toggleData(this)">
                                <option value="" selected disabled>Scegli criterio</option>
                                <option value="reputazione">Reputazione &ge; soglia</option>
                                <option value="mesi_registrato">Registrato da &ge; N mesi</option>
                                <option value="anni_registrato">Registrato da &ge; N anni</option>
                                <option value="crediti_totali">Spesa totale &ge; soglia &euro;</option>
                                <option value="crediti_da_data">Spesa da data &ge; soglia &euro;</option>
                            </select><br>
                            <label>Soglia:</label>
                            <input class="input" type="number" name="soglia" min="0" step="1" required><br>
                            <label for="data_riferimento">Data di riferimento:</label>
                            <input class="input" type="date" id="data_riferimento" name="data_riferimento"><br>
                            <button class="btn" type="submit">Attiva</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
