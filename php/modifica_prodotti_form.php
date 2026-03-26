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

    // Verifica se sono stati passati i parametri GET
    if (!isset($_GET['id_prodotto']) || !isset($_GET['tipologia'])) {
        header("Location: ../php/index.php");
        exit();
    }else{
        // Possiamo ora utilizzare $id_prodotto per recuperare le informazioni del prodotto dal file XML
        $id_prodotto = $_GET['id_prodotto'];
        $tipologia = $_GET['tipologia'];

        // Leggiamo il file XML
        $xmlFile = '../xml/catalogo_prodotti.xml';
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->load($xmlFile);

        // Trova il nodo del prodotto con l'id corrispondente
        $xpath = new DOMXPath($dom);
        $query = "//prodotto[id_prodotto='$id_prodotto']";
        $prodottoNodeList = $xpath->query($query);

        // Se esiste un prodotto con l'id corrispondente
        if ($prodottoNodeList->length > 0) {
            $prodottoNode = $prodottoNodeList->item(0);

            // Recupera le informazioni del prodotto
            $nome = $prodottoNode->getElementsByTagName('nome')->item(0)->nodeValue;
            $descrizione = $prodottoNode->getElementsByTagName('descrizione')->item(0)->nodeValue;
            $prezzo = $prodottoNode->getElementsByTagName('prezzo')->item(0)->nodeValue;
            $immagine = $prodottoNode->getElementsByTagName('immagine')->item(0)->nodeValue;

            // Ora possiamo utilizzare queste informazioni per le modifiche
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
        <title>Modifica Prodotto</title>
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
                if(isset($_SESSION['errore_nome_esistente']) && $_SESSION['errore_nome_esistente'] == 'true'){
                    echo '<h2>Nome prodotto già esistente...</h2>';
                    unset($_SESSION['errore_nome_esistente']);
                }
                if(isset($_SESSION['errore_immagine']) && $_SESSION['errore_immagine'] == 'true'){
                    echo '<h2>Errore nel caricamneto dell\' immagine!!!</h2>';
                    unset($_SESSION['errore_immagine']);
                }

                $_SESSION['nome_prodotto_attuale'] = $nome;
            ?>

            <?php echo '<a class="go-back" href="../php/catalogo_' . $tipologia . '.php">
                    <span class="material-symbols-outlined" style="vertical-align:middle;">arrow_back</span>
                    Torna al catalogo ' . $tipologia . '</a>'; ?>
            <h1 class="titolo">Modifica Prodotto &mdash; <?php echo $nome; ?></h1>
            <form class="form" action="../res/modifica_prodotti.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_prodotto" value="<?php echo $id_prodotto; ?>">
                <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>" >
                <table class="table">
                    <tr>
                        <td><label>Nome:</label></td>
                        <td>
                            <input class="input" style="width:400px;" type="text" name="nome" value="<?php echo $nome; ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Descrizione:</label></td>
                        <td><textarea style="width:500px; height:100px; resize:none;" class="input" name="descrizione" required><?php echo $descrizione; ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label>Modifica Il Prezzo Base:</label></td>
                        <td><input class="input" type="number" name="prezzo" min="0.01" step="0.01" value="<?php echo $prezzo; ?>" required></td>
                    </tr>
                    <tr>
                        <td><label>Immagine:</label></td>
                        <td><input type="file" class="input" name="immagine" accept="image/*"></td>
                    </tr>
                </table>
                <button class="btn" style="margin-top:1vw;" type="submit">Salva Modifiche</button>
            </form>
        </div>
    </body>
</html>