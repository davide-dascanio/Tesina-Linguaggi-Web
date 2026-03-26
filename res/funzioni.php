<?php

// ============================================================
//  FUNZIONI AUSILIARIE PER IL CATALOGO
//  Questo file contiene tutte le funzioni di supporto usate
//  dai cataloghi (tv, soundbar, supporti) per:
//    - leggere dati dall'XML
//    - verificare i criteri degli sconti/bonus
//    - calcolare prezzi finali e bonus per il cliente loggato
// ============================================================


// ------------------------------------------------------------
//  getProdotti($xmlFile)
//
//  Legge il file XML del catalogo e restituisce un array PHP
//  con tutti i prodotti. Per ogni prodotto vengono estratti:
//    - dati base (id, nome, descrizione, prezzo, tipologia, immagine)
//    - struttura completa sconti_bonus con i 4 elementi:
//        * sconto_generico      (attivo 0/1, valore %)
//        * sconto_personalizzato(attivo 0/1, percentuale %, criterio)
//        * bonus_generico       (attivo 0/1, valore crediti)
//        * bonus_personalizzato (attivo 0/1, crediti, criterio)
// ------------------------------------------------------------


// Legge tutti i prodotti dall'XML (dal catalogo) 
function getProdotti($xmlFile) {

    // Array dove poi metterò i prodotti presi dal file catalogo.xml
    $prodotti = [];

    // Crea un nuovo oggetto DOMDocument e carica il file XML
    $dom = new DOMDocument();
    $dom->load($xmlFile);

    $lista_prodotti = $dom->getElementsByTagName('prodotto');

    foreach ($lista_prodotti as $prodotto) {

        // Nodo radice sconti_bonus del prodotto corrente
        $sb = $prodotto->getElementsByTagName('sconti_bonus')->item(0);

        // Estraggo i 4 sotto-nodi sconti/bonus
        $sg = $sb->getElementsByTagName('sconto_generico')->item(0);
        $sp = $sb->getElementsByTagName('sconto_personalizzato')->item(0);
        $sp_c = $sp->getElementsByTagName('criterio')->item(0);     // criterio dello sconto personalizzato
        $bg = $sb->getElementsByTagName('bonus_generico')->item(0);
        $bp = $sb->getElementsByTagName('bonus_personalizzato')->item(0);
        $bp_c = $bp->getElementsByTagName('criterio')->item(0);     // criterio del bonus personalizzato

        // Aggiungo il prodotto all'array come array associativo
        $prodotti[] = [
            'id_prodotto' => $prodotto->getElementsByTagName('id_prodotto')->item(0)->nodeValue,
            'nome'        => $prodotto->getElementsByTagName('nome')->item(0)->nodeValue,
            'descrizione' => $prodotto->getElementsByTagName('descrizione')->item(0)->nodeValue,
            'prezzo'      => $prodotto->getElementsByTagName('prezzo')->item(0)->nodeValue,
            'tipologia'   => $prodotto->getElementsByTagName('tipologia')->item(0)->nodeValue,
            'immagine'    => $prodotto->getElementsByTagName('immagine')->item(0)->nodeValue,
            'sconti_bonus' => [
                'sconto_generico' => [
                    'attivo' => $sg->getAttribute('attivo'),
                    'valore' => $sg->nodeValue,
                ],
                'sconto_personalizzato' => [
                    'attivo'          => $sp->getAttribute('attivo'),
                    'percentuale'     => $sp->getElementsByTagName('percentuale')->item(0)->nodeValue,
                    'tipo'            => $sp_c->getAttribute('tipo'),
                    'soglia'          => $sp_c->getElementsByTagName('soglia')->item(0)->nodeValue,
                    'data_riferimento'=> $sp_c->getElementsByTagName('data_riferimento')->item(0)->nodeValue,
                ],
                'bonus_generico' => [
                    'attivo' => $bg->getAttribute('attivo'),
                    'valore' => $bg->nodeValue,
                ],
                'bonus_personalizzato' => [
                    'attivo'          => $bp->getAttribute('attivo'),
                    'crediti'         => $bp->getElementsByTagName('crediti')->item(0)->nodeValue,
                    'tipo'            => $bp_c->getAttribute('tipo'),
                    'soglia'          => $bp_c->getElementsByTagName('soglia')->item(0)->nodeValue,
                    'data_riferimento'=> $bp_c->getElementsByTagName('data_riferimento')->item(0)->nodeValue,
                ],
            ],
        ];
    }
    return $prodotti;
}





// ------------------------------------------------------------
//  getAcquisti($xmlFile)
//
//  Legge il file XML dello storico acquisti e restituisce un
//  array con tutti gli acquisti effettuati. Ogni elemento ha:
//    - IDutente      : id dell'utente che ha acquistato
//    - prezzo_totale : importo speso in quell'acquisto
//    - data          : data dell'acquisto (stringa "YYYY-MM-DD")
//
//  Viene usata da verificaCriterio() per i criteri basati
//  sulla spesa totale o sulla spesa da una certa data.
// ------------------------------------------------------------


//  Legge gli acquisti dallo storico acquisti XML 
function getAcquisti($xmlFile) {

    // Array dove poi metterò i prodotti acquistati presi dal file storico_acquisti.xml
    $acquisti = [];

    $dom = new DOMDocument();
    $dom->load($xmlFile);

    $lista_acquisti = $dom->getElementsByTagName('acquisto');

    foreach ($lista_acquisti as $acquisto) {

        // Aggiungo il prodotto all'array come array associativo
        $acquisti[] = [
            'IDutente'     => $acquisto->getAttribute('id_utente'),
            'prezzo_totale'=> $acquisto->getElementsByTagName('prezzo_totale')->item(0)->nodeValue,
            'data'         => $acquisto->getElementsByTagName('data')->item(0)->nodeValue,
        ];
    }
    return $acquisti;

    /*
    $acquisti sarà un array di array associativi, cioè un array dove ogni elemento è a sua volta un array con chiavi nominate
    Ogni sotto-array ha le chiavi che ho inserito ('IDutente', 'prezzo_totale', 'data'), da cui il nome "associativo" — 
    ogni valore è associato a un nome, non a un numero.

    $acquisti = [
        0 => [
            'IDutente'      => '3',
            'prezzo_totale' => 500.00,
            'data'          => '2025-11-10',
        ],
        1 => [
            'IDutente'      => '7',
            'prezzo_totale' => 200.00,
            'data'          => '2026-01-05',
        ],
    ];
    */
    
}



// ------------------------------------------------------------
//  verificaCriterio($tipo, $soglia, $data_rif, $id_utente, $data_reg, $reputazione)
//
//  Restituisce TRUE se il cliente soddisfa il criterio dato,
//  FALSE altrimenti. Viene chiamata sia per lo sconto
//  personalizzato che per il bonus personalizzato.
//
//  I criteri possibili sono:
//    - reputazione     : la reputazione del cliente >= soglia
//    - mesi_registrato : registrato da almeno X mesi
//    - anni_registrato : registrato da almeno Y anni
//    - crediti_totali  : ha speso almeno N crediti in totale
//    - crediti_da_data : ha speso almeno M crediti dalla data_rif
//    - nessuno         : criterio non definito → false
// ------------------------------------------------------------


//  Verifica se UN criterio è soddisfatto dal cliente 
function verificaCriterio($tipo, $soglia, $data_rif, $id_utente, $data_reg, $reputazione) {
    switch ($tipo) {

        case 'reputazione':
            // Controlla se la reputazione del cliente è >= alla soglia richiesta
            return ($reputazione >= $soglia);
            
        case 'mesi_registrato':
            // Calcola la data minima di registrazione sottraendo X mesi ad oggi
            // (es. soglia=6 → il cliente deve essersi registrato prima di 6 mesi fa)
            $data_minima = new DateTime();
            $data_minima->sub(new DateInterval("P{$soglia}M"));

            // Converto la data in formato DateTime per poter fare la differenza
            $data_r = DateTime::createFromFormat('Y-m-d', $data_reg);
            return ($data_r <= $data_minima);

        case 'anni_registrato':
            // Come sopra ma in anni
            $data_minima = new DateTime();
            $data_minima->sub(new DateInterval("P{$soglia}Y"));
            $data_r = DateTime::createFromFormat('Y-m-d', $data_reg);
            return ($data_r <= $data_minima);

        case 'crediti_totali':
            // Somma tutto ciò che il cliente ha speso in totale (tutti gli acquisti)
            // e controlla se supera la soglia richiesta
            $acquisti = getAcquisti("../xml/storico_acquisti.xml");
            $spesa = 0;
            foreach ($acquisti as $a) {
                // $a è uno dei sotto-array
                if ($a['IDutente'] == $id_utente) 
                    $spesa += $a['prezzo_totale'];
            }
            return ($spesa >= $soglia);

        case 'crediti_da_data':
            // Come sopra ma considera solo gli acquisti fatti dalla data_rif in poi
            $acquisti = getAcquisti("../xml/storico_acquisti.xml");
            $spesa = 0;
            foreach ($acquisti as $a) {
                if ($a['IDutente'] == $id_utente && $a['data'] >= $data_rif)
                    $spesa += $a['prezzo_totale'];
            }
            return ($spesa >= $soglia);
            
        default: // 'nessuno' o tipo non riconosciuto
            return false;
    }
}




// ------------------------------------------------------------
//  etichettaCriterio($tipo, $soglia, $data_rif)
//
//  Converte un criterio in una stringa leggibile dall'utente,
//  usata nel tooltip del catalogo per descrivere lo sconto/bonus personalizzato.
//  Es. tipo="reputazione", soglia="6" → "reputazione ≥ 6"
// ------------------------------------------------------------

function etichettaCriterio($tipo, $soglia, $data_rif) {
    switch ($tipo) {
        case 'reputazione':     return "per chi ha reputazione &ge; $soglia";
        case 'mesi_registrato': return "per chi si è registrato da &ge; $soglia mesi";
        case 'anni_registrato': return "per chi si è registrato da &ge; $soglia anni";
        case 'crediti_totali':  return "per chi ha speso &ge; $soglia crediti in totale";
        case 'crediti_da_data': return "per chi ha speso &ge; $soglia crediti dal $data_rif";
        default:                return "nessun criterio";
    }
}



// ------------------------------------------------------------
//  getDettaglioScontiBonus($xmlpath, $id_prodotto, $prezzo)
//
//  Funzione principale per il cliente loggato.
//  Fa UNA SOLA query al DB per recuperare i dati del cliente,
//  poi determina quali dei 4 sconti/bonus si applicano a lui
//  e calcola il prezzo finale.
//
//  Restituisce un array con:
//    - prezzo_base            : prezzo originale del prodotto
//    - prezzo_finale          : prezzo dopo gli sconti applicati
//    - bonus_totale           : crediti totali guadagnati dopo l'acquisto
//    - sconto_generico        : ['applicato' => bool, 'valore' => %]
//    - sconto_personalizzato  : ['applicato' => bool, 'percentuale' => %, 'tipo', 'soglia', 'data_rif']
//    - bonus_generico         : ['applicato' => bool, 'valore' => crediti]
//    - bonus_personalizzato   : ['applicato' => bool, 'crediti' => n, 'tipo', 'soglia', 'data_rif']
//
//  Se il cliente non è loggato, restituisce il risultato
//  "neutro" (prezzo invariato, nessun bonus, nulla applicato).
// ------------------------------------------------------------


function getDettaglioScontiBonus($xmlpath, $id_prodotto, $prezzo) {

    // Valore di ritorno di default (nessuno sconto/bonus applicato)
    // $risultato rappresenta un solo set di dati per un prodotto specifico 
    $risultato = [
        'prezzo_base'            => $prezzo,
        'prezzo_finale'          => $prezzo,
        'bonus_totale'           => 0,
        'sconto_generico'        => ['applicato' => false, 'valore' => 0],
        'sconto_personalizzato'  => ['applicato' => false, 'percentuale' => 0, 'tipo' => '', 'soglia' => 0, 'data_rif' => ''],
        'bonus_generico'         => ['applicato' => false, 'valore' => 0],
        'bonus_personalizzato'   => ['applicato' => false, 'crediti' => 0, 'tipo' => '', 'soglia' => 0, 'data_rif' => ''],
    ];

    // Se non è loggato non ha diritto a nessuno sconto → restituisco il default  
    if (!isset($_SESSION['loggato']) || !$_SESSION['loggato']) 
        return $risultato;

    // $connessione è una variabile globale creata da connessione1.php
    global $connessione;
    $row = $connessione->query("SELECT id, data_registrazione, reputazione FROM utenti WHERE email='{$_SESSION['email']}'")->fetch_assoc();

    $id_utente = $row['id'];
    $data_reg = $row['data_registrazione'];
    $reputazione = $row['reputazione'];

    // Percentuale di sconto totale cumulata (sconto generico + personalizzato se entrambi attivi)
    $sconto_totale = 0;

    $prodotti_documento = getProdotti($xmlpath);

    // Scorro i prodotti finché non trovo quello con l'id cercato
    foreach ($prodotti_documento as $prodotto_documento) {
        if ($prodotto_documento['id_prodotto'] != $id_prodotto) 
            continue;
        
        $sb = $prodotto_documento['sconti_bonus'];

        // Sconto generico — per tutti i clienti loggati
        // Si applica a TUTTI i clienti loggati, basta che sia attivo
        if ($sb['sconto_generico']['attivo'] == 1) {
            $val = $sb['sconto_generico']['valore'];
            $sconto_totale += $val;
            $risultato['sconto_generico'] = ['applicato' => true, 'valore' => $val];
        }

        // Sconto personalizzato — solo se soddisfa il criterio
        // Si applica solo se è attivo E il cliente soddisfa il criterio
        if ($sb['sconto_personalizzato']['attivo'] == 1) {
            $sp = $sb['sconto_personalizzato'];
            $risultato['sconto_personalizzato']['tipo'] = $sp['tipo'];
            $risultato['sconto_personalizzato']['soglia'] = $sp['soglia'];
            $risultato['sconto_personalizzato']['data_rif'] = $sp['data_riferimento'];
            if (verificaCriterio($sp['tipo'], $sp['soglia'], $sp['data_riferimento'], $id_utente, $data_reg, $reputazione)) {
                $perc = $sp['percentuale'];
                $sconto_totale += $perc;
                $risultato['sconto_personalizzato']['applicato'] = true;
                $risultato['sconto_personalizzato']['percentuale'] = $perc;
            }
        }

        // Bonus generico — per tutti i clienti loggati
        // Accreditato a TUTTI i clienti loggati dopo l'acquisto, basta che sia attivo
        if ($sb['bonus_generico']['attivo'] == 1) {
            $val = $sb['bonus_generico']['valore'];
            $risultato['bonus_totale'] += $val;
            $risultato['bonus_generico'] = ['applicato' => true, 'valore' => $val];
        }

        // Bonus personalizzato — solo se soddisfa il criterio
        // Accreditato solo se è attivo E il cliente soddisfa il criterio
        if ($sb['bonus_personalizzato']['attivo'] == 1) {
            $bp = $sb['bonus_personalizzato'];
            $risultato['bonus_personalizzato']['tipo'] = $bp['tipo'];
            $risultato['bonus_personalizzato']['soglia'] = $bp['soglia'];
            $risultato['bonus_personalizzato']['data_rif'] = $bp['data_riferimento'];
            if (verificaCriterio($bp['tipo'], $bp['soglia'], $bp['data_riferimento'], $id_utente, $data_reg, $reputazione)) {
                $cr = $bp['crediti'];
                $risultato['bonus_totale'] += $cr;
                $risultato['bonus_personalizzato']['applicato'] = true;
                $risultato['bonus_personalizzato']['crediti'] = $cr;
            }
        }
        break;  // prodotto trovato, esco dal foreach
    }

    // Applico lo sconto totale al prezzo base solo se c'è almeno uno sconto
    if ($sconto_totale > 0) {
        $risultato['prezzo_finale'] = number_format($prezzo - ($prezzo * $sconto_totale / 100), 2, '.', '');
    }
    return $risultato;
}


?>