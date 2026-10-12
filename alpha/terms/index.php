<!DOCTYPE html>
<html lang="it">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Termini e condizioni d'uso";
    ?>
    <title><?php echo $title; ?></title>
    <style>
        .lang-tabs { display: flex; gap: 8px; justify-content: center; margin-bottom: 24px; }
        .lang-tabs button { padding: 8px 20px; border: 2px solid var(--color-primary); border-radius: var(--border-radius-sm); background: transparent; color: var(--color-primary); cursor: pointer; font-weight: 600; font-family: var(--font-body); font-size: 0.9em; transition: var(--transition); }
        .lang-tabs button.active { background: var(--color-primary); color: #fff; }
        .lang-content { display: none; }
        .lang-content.active { display: block; }
    </style>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">Termini e condizioni d'uso</h2>
        <div class="big-space"></div>

        <div class="lang-tabs">
            <button class="active" onclick="switchLang('it', this)">Italiano</button>
            <button onclick="switchLang('en', this)">English</button>
        </div>

        <!-- ITALIAN (primary) -->
        <div id="lang-it" class="lang-content active">
            <section class="horizontal-center-p text-align-justify">
                <h4>1. Accettazione dei termini</h4>
                <p>Utilizzando il progetto <strong>savmrl.it</strong> (il "Servizio"), accetti di essere vincolato dai presenti Termini e Condizioni. Se non sei d'accordo con una qualsiasi parte di questi Termini, ti preghiamo di non utilizzare il Servizio.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>2. Servizio gratuito e anonimo</h4>
                <ul>
                    <li>L'abbreviatore di link <strong>savmrl.it</strong> &egrave; fornito <strong>gratuitamente</strong> &mdash; non &egrave; richiesto alcun pagamento o abbonamento.</li>
                    <li>Gli utenti possono utilizzare il Servizio <strong>in forma anonima</strong>, senza registrazione o creazione di un account.</li>
                </ul>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>3. Titolarit&agrave; e giurisdizione</h4>
                <p>Il Servizio &egrave; di propriet&agrave; e gestito in <strong>Italia, Unione Europea</strong>. Tutte le questioni relative al Servizio sono regolate dalla <strong>legge e giurisdizione italiana</strong>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>4. Raccolta dati e privacy</h4>
                <ul>
                    <li>Quando un utente crea o accede a un link abbreviato, il Servizio raccoglie unicamente l'<strong>indirizzo IP pubblico</strong> per finalit&agrave; di sicurezza, funzionalit&agrave; e analisi.</li>
                    <li>Gli indirizzi IP sono conservati in modo sicuro e <strong>mai condivisi con terze parti</strong>, salvo quanto previsto dalla legge.</li>
                    <li>Gli indirizzi IP vengono automaticamente anonimizzati (rimossi) dopo <strong>30 giorni</strong> dalla data di raccolta.</li>
                    <li>Per tutti i dettagli, si prega di consultare la nostra <a href="/alpha/privacy/">Informativa sulla privacy</a>.</li>
                </ul>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>5. Modifiche al Servizio</h4>
                <p>Le funzionalit&agrave;, il design e il funzionamento del Servizio possono cambiare nel tempo. Gli aggiornamenti significativi saranno annunciati sul sito. L'uso continuato dopo tali modifiche costituisce accettazione dei Termini aggiornati.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>6. Attivit&agrave; vietate e illegali</h4>
                <p>Il Servizio non deve essere utilizzato per scopi illegali, abusivi o malevoli. I link contenenti o che reindirizzano a contenuti illegali possono essere rimossi senza preavviso. Gli utenti restano gli unici responsabili del contenuto degli URL che abbreviano e condividono.</p>
                <p>Lo sviluppatore e l'operatore del Servizio <strong>non &egrave; responsabile</strong> per qualsiasi uso illegale o illegittimo del Servizio da parte dei suoi utenti. La responsabilit&agrave; per qualsiasi attivit&agrave; illecita svolta attraverso il Servizio ricade interamente sull'utente che ha compiuto tale attivit&agrave;.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>7. Segnalazione di abusi</h4>
                <p>Se incontri un link sospetto o illegale, o ritieni che il Servizio sia utilizzato in modo improprio, ti preghiamo di segnalarlo immediatamente tramite il modulo di contatto disponibile sul sito. Indagheremo e prenderemo le misure appropriate.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>8. Limitazione di responsabilit&agrave;</h4>
                <p>Il Servizio &egrave; fornito "cos&igrave; com'&egrave;" senza garanzie di alcun tipo. Gli operatori di <strong>savmrl.it</strong> non sono responsabili per:</p>
                <ul>
                    <li>Qualsiasi danno o perdita derivante dall'uso o dall'uso improprio del Servizio;</li>
                    <li>Il contenuto o la sicurezza degli URL abbreviati;</li>
                    <li>Interruzioni del servizio, errori o perdita di dati.</li>
                </ul>
                <p>L'uso del Servizio avviene a proprio rischio e pericolo.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>9. Informazioni di contatto</h4>
                <p>Per qualsiasi domanda o dubbio riguardante i presenti Termini, contattaci tramite il <strong>modulo di contatto</strong> disponibile alla seguente pagina: <a href="https://www.saveriomorelli.com/contact-me/">https://www.saveriomorelli.com/contact-me/</a></p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>10. Licenza</h4>
                <p>Il progetto &egrave; rilasciato sotto la <strong>Mozilla Public License, versione 2.0</strong>. &Egrave; possibile consultare la licenza su <a href="https://www.mozilla.org/MPL/2.0/" target="_blank" rel="noopener">https://www.mozilla.org/MPL/2.0/</a>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>11. Modifiche ai Termini</h4>
                <p>Potremmo rivedere i presenti Termini e Condizioni di tanto in tanto. La versione pi&ugrave; recente sar&agrave; sempre disponibile su <a href="/alpha/terms/">/alpha/terms/</a> con la data di "ultimo aggiornamento". Si prega di consultarli periodicamente.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <p>Utilizzando il servizio di abbreviazione savmrl.it, riconosci di aver letto, compreso e accettato i presenti termini e condizioni.</p>
                <p>Ultimo aggiornamento: 30 settembre 2026</p>
            </section>
        </div>

        <!-- ENGLISH (courtesy translation) -->
        <div id="lang-en" class="lang-content">
            <section class="horizontal-center-p text-align-justify">
                <p><em>This English version is provided for convenience only. In case of any discrepancy between the Italian and English versions, the <a href="javascript:switchLang('it', document.querySelector('.lang-tabs button'))">Italian version</a> shall prevail.</em></p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>1. Acceptance of Terms</h4>
                <p>By using the <strong>savmrl.it</strong> project (the "Service"), you agree to be bound by these Terms and Conditions. If you do not agree with any part of these Terms, please do not use the Service.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>2. Free and Anonymous Service</h4>
                <ul>
                    <li>The <strong>savmrl.it</strong> link shortener is provided <strong>free of charge</strong> &mdash; no payment or subscription is required.</li>
                    <li>Users may use the Service <strong>anonymously</strong>, without registration or account creation.</li>
                </ul>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>3. Ownership and Jurisdiction</h4>
                <p>The Service is owned and operated in <strong>Italy, European Union</strong>. All matters related to the Service are governed by <strong>Italian law and jurisdiction</strong>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>4. Data Collection and Privacy</h4>
                <ul>
                    <li>When a user creates or accesses a shortened link, the Service collects only the <strong>public IP address</strong> for security, functionality, and analytics purposes.</li>
                    <li>IP addresses are stored securely and <strong>never shared with third parties</strong>, except as required by law.</li>
                    <li>IP addresses are automatically anonymized (removed) after <strong>30 days</strong> from the date of collection.</li>
                    <li>For full details, please refer to our <a href="/alpha/privacy/">Privacy Policy</a>.</li>
                </ul>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>5. Changes to the Service</h4>
                <p>The features, design, and operation of the Service may change over time. Significant updates will be announced on the site. Continued use after such changes constitutes acceptance of the updated Terms.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>6. Prohibited and Illegal Activities</h4>
                <p>The Service must not be used for illegal, abusive, or malicious purposes. Links containing or redirecting to illegal content may be removed without notice. Users remain solely responsible for the content of the URLs they shorten and share.</p>
                <p>The developer and operator of the Service is <strong>not responsible</strong> for any illegal or illegitimate use of the Service by its users. The responsibility for any unlawful activity carried out through the Service lies entirely with the user who performed such activity.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>7. Reporting Abuse</h4>
                <p>If you encounter a suspicious or illegal link, or believe the Service is being misused, please report it immediately via the contact form available on the website. We will investigate and take appropriate measures.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>8. Limitation of Liability</h4>
                <p>The Service is provided "as is" without warranties of any kind. The operators of <strong>savmrl.it</strong> are not liable for:</p>
                <ul>
                    <li>Any damages or losses resulting from use or misuse of the Service;</li>
                    <li>The content or safety of shortened URLs;</li>
                    <li>Service interruptions, errors, or data loss.</li>
                </ul>
                <p>Use of the Service is at your own risk.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>9. Contact Information</h4>
                <p>For any questions or concerns regarding these Terms, please contact us using the <strong>contact form</strong> available on the following website page: <a href="https://www.saveriomorelli.com/contact-me/">https://www.saveriomorelli.com/contact-me/</a></p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>10. License</h4>
                <p>The project is released under the <strong>Mozilla Public License, version 2.0</strong>. You can view the license at <a href="https://www.mozilla.org/MPL/2.0/" target="_blank" rel="noopener">https://www.mozilla.org/MPL/2.0/</a>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>11. Changes to Terms</h4>
                <p>We may revise this Terms and Conditions of Service from time to time. The latest version will always be available at <a href="/alpha/terms/">/alpha/terms/</a> with the updated "last modified" date. Please review periodically.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <p>By using the savmrl.it shortener service, you acknowledge that you have read, understood, and agreed to these terms and conditions.</p>
                <p>Last updated: 30 September 2026</p>
            </section>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<script>
function switchLang(lang, btn) {
    document.querySelectorAll('.lang-content').forEach(function(el) { el.classList.remove('active'); });
    document.querySelectorAll('.lang-tabs button').forEach(function(el) { el.classList.remove('active'); });
    document.getElementById('lang-' + lang).classList.add('active');
    btn.classList.add('active');
}
</script>
</body>
</html>
