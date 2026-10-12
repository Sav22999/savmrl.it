<!DOCTYPE html>
<html lang="it">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Informativa sulla privacy";
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
        <h2 class="title-section">Informativa sulla privacy</h2>
        <div class="big-space"></div>

        <div class="lang-tabs">
            <button class="active" onclick="switchLang('it', this)">Italiano</button>
            <button onclick="switchLang('en', this)">English</button>
        </div>

        <!-- ITALIAN (primary) -->
        <div id="lang-it" class="lang-content active">
            <section class="horizontal-center-p text-align-justify">
                <h4>1. Introduzione e ambito di applicazione</h4>
                <p>Benvenuto su <strong>savmrl.it</strong> (il "Servizio", "noi"). Il Servizio offre una funzionalit&agrave; di abbreviazione e reindirizzamento di URL.</p>
                <p>La presente Informativa sulla privacy spiega come raccogliamo, utilizziamo, conserviamo e proteggiamo i dati quando utilizzi il nostro Servizio. Ci impegniamo a rispettare il <strong>Regolamento Generale sulla Protezione dei Dati (GDPR)</strong> (Regolamento UE 2016/679) e le leggi italiane in materia di privacy applicabili.</p>
                <p>I nostri server sono ospitati in <strong>Italia</strong> (tramite <strong>Aruba.it</strong>) e il Servizio opera sotto la <strong>giurisdizione italiana</strong>.</p>
                <p>Utilizzando il Servizio, accetti la presente Informativa sulla privacy. Se non sei d'accordo, ti preghiamo di non utilizzare <code>savmrl.it</code>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>2. Dati che raccogliamo</h4>
                <p>Raccogliamo esclusivamente i dati necessari per fornire il Servizio e garantirne il corretto funzionamento e la sicurezza.</p>

                <h4>2.1 Dati forniti dall'utente</h4>
                <ul>
                    <li>L'<strong>URL originale</strong> che si desidera abbreviare.</li>
                    <li>Impostazioni opzionali: <em>numero massimo di clic</em>, <em>data di scadenza</em> e un <em>codice di accesso / password</em> per limitare l'accesso.</li>
                </ul>
                <p>Se imposti un codice di accesso, questo viene memorizzato <strong>cifrato (hash)</strong> nel nostro database, e il link viene memorizzato <strong>cifrato (AES-256)</strong>, in modo che <strong>nemmeno l'operatore del servizio possa visualizzare il testo in chiaro</strong>. Altri dati come timestamp e indirizzo IP dell'utente sono memorizzati in chiaro per garantire il corretto funzionamento.</p>

                <h4>2.2 Dati raccolti automaticamente</h4>
                <ul>
                    <li>L'<strong>indirizzo IP</strong> pubblico del client che crea o accede al link.</li>
                    <li>Il <strong>timestamp</strong> di creazione o accesso.</li>
                    <li>Il <strong>conteggio dei clic</strong> per il link abbreviato.</li>
                    <li>Le <strong>impostazioni di scadenza</strong> del link e l'eventuale <strong>codice di accesso cifrato</strong>.</li>
                </ul>
                <p>Non raccogliamo ulteriori dati personali come nomi, email o geolocalizzazione precisa.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>3. Finalit&agrave; e base giuridica del trattamento</h4>
                <table>
                    <thead>
                    <tr>
                        <th>Finalit&agrave;</th>
                        <th>Base giuridica (GDPR)</th>
                        <th>Dettagli</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Fornire abbreviazione e reindirizzamento dei link</td>
                        <td>Esecuzione di un contratto / richiesta dell'utente</td>
                        <td>Il trattamento &egrave; necessario per creare e gestire i link abbreviati.</td>
                    </tr>
                    <tr>
                        <td>Applicare scadenze, codici di accesso, limiti di clic</td>
                        <td>Interesse legittimo</td>
                        <td>Necessario per fornire le funzionalit&agrave; di protezione e validit&agrave; dei link richieste.</td>
                    </tr>
                    <tr>
                        <td>Prevenire abusi e mantenere la sicurezza</td>
                        <td>Interesse legittimo</td>
                        <td>Monitoraggio per proteggere il servizio da spam, abusi o attacchi.</td>
                    </tr>
                    <tr>
                        <td>Log tecnici e analisi</td>
                        <td>Interesse legittimo</td>
                        <td>Utilizzati per affidabilit&agrave;, debug e miglioramento delle prestazioni.</td>
                    </tr>
                    <tr>
                        <td>Conformit&agrave; legale</td>
                        <td>Obbligo di legge</td>
                        <td>Per rispondere a richieste legittime o far valere i diritti.</td>
                    </tr>
                    </tbody>
                </table>
                <p>Non trattiamo dati per profilazione, marketing o pubblicit&agrave;.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>4. Condivisione e divulgazione dei dati</h4>
                <p><strong>Non condividiamo, vendiamo, affittiamo o divulghiamo alcun dato dell'utente a terzi in nessuna circostanza.</strong></p>
                <p>I dati non vengono mai trasferiti a societ&agrave; esterne, fornitori di analisi o inserzionisti. L'accesso ai dati &egrave; strettamente limitato all'amministratore di sistema del servizio per soli scopi operativi e di sicurezza.</p>
                <p><strong>Eccezioni:</strong> I dati possono essere divulgati solo quando richiesto dalla legge o da un'ordinanza del tribunale (ad es. richiesta giudiziaria o di polizia) o per far valere i nostri Termini di servizio o difendere rivendicazioni legali, se necessario.</p>
                <p>Nessun dato viene trasferito al di fuori dello Spazio Economico Europeo (SEE).</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>5. Conservazione dei dati</h4>
                <ul>
                    <li>I dati relativi a un link (URL originale, data di creazione, scadenza, conteggio dei clic) vengono conservati fino a quando il link non &egrave; <strong>scaduto</strong> o <strong>eliminato</strong>.</li>
                    <li>Gli <strong>indirizzi IP</strong> vengono automaticamente anonimizzati (impostati a null) dopo <strong>30 giorni</strong> dalla data di raccolta. Questo si applica agli indirizzi IP memorizzati nei record di creazione dei link, nei record di clic/visite e nei record delle sessioni.</li>
                    <li>I link scaduti o eliminati e i relativi dati possono essere rimossi dopo un breve periodo di conservazione.</li>
                    <li>Quando tecnicamente possibile, gli utenti possono richiedere la rimozione di uno specifico link e dei dati associati.</li>
                </ul>
                <p>Dopo l'eliminazione, i dati memorizzati vengono cancellati in modo permanente o anonimizzati.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>6. Sicurezza dei dati</h4>
                <p>Applichiamo misure tecniche e organizzative ragionevoli per proteggere i dati, tra cui:</p>
                <ul>
                    <li>Memorizzazione cifrata/hash per i codici di accesso;</li>
                    <li>Accesso controllato e registrato al database;</li>
                    <li>Protezioni firewall e server;</li>
                    <li>Aggiornamenti di sicurezza e audit regolari.</li>
                </ul>
                <p>Nonostante le precauzioni, nessun sistema &egrave; completamente immune da accessi non autorizzati o attacchi informatici. Il fornitore del servizio non &egrave; responsabile per violazioni o incidenti al di l&agrave; del ragionevole controllo.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>7. Diritti dell'utente (GDPR)</h4>
                <p>Se ti trovi nell'UE/SEE, hai i seguenti diritti:</p>
                <ul>
                    <li>Accedere ai tuoi dati personali (Art. 15).</li>
                    <li>Rettificare dati inesatti o incompleti (Art. 16).</li>
                    <li>Richiedere la cancellazione ("diritto all'oblio", Art. 17).</li>
                    <li>Limitare il trattamento (Art. 18).</li>
                    <li>Portabilit&agrave; dei dati, ove applicabile (Art. 20).</li>
                    <li>Opporsi al trattamento basato su interessi legittimi (Art. 21).</li>
                    <li>Revocare il consenso ove applicabile.</li>
                    <li>Presentare un reclamo all'autorit&agrave; di controllo (Garante per la Protezione dei Dati Personali).</li>
                </ul>
                <p>Le richieste devono essere inviate al contatto indicato di seguito. Potrebbe essere necessario verificare l'identit&agrave; prima di dare seguito alle richieste.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>8. Esclusione e limitazione di responsabilit&agrave;</h4>
                <p>Il Servizio &egrave; fornito "cos&igrave; com'&egrave;" senza garanzie. L'operatore di <strong>savmrl.it</strong> non &egrave; responsabile per:</p>
                <ul>
                    <li>Contenuto o sicurezza dei link abbreviati;</li>
                    <li>Disponibilit&agrave; continua o correttezza del Servizio;</li>
                    <li>Danni derivanti da uso doloso, fraudolento o inappropriato del Servizio;</li>
                    <li>Perdita di dati, problemi tecnici o indisponibilit&agrave; temporanea.</li>
                </ul>
                <p>Gli utenti sono responsabili degli URL che inviano e condividono.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>9. Titolare del trattamento e contatti</h4>
                <p>
                    <strong>Titolare del trattamento:</strong> Saverio Morelli
                    <br>
                    Contatto: <a href="https://www.saveriomorelli.com/contact-me/">https://www.saveriomorelli.com/contact-me/</a>
                </p>
                <p>
                    Per richieste relative alla privacy o ai propri dati personali, contattare l'indirizzo sopra indicato. In caso di mancata risoluzione, &egrave; possibile presentare un reclamo al Garante per la Protezione dei Dati Personali: <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener">https://www.garanteprivacy.it</a>.
                </p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>10. Modifiche alla presente Informativa</h4>
                <p>Potremmo rivedere la presente Informativa sulla privacy di tanto in tanto. La versione pi&ugrave; recente sar&agrave; sempre disponibile su <a href="/alpha/privacy/">/alpha/privacy/</a> con la data di "ultimo aggiornamento". Si prega di consultarla periodicamente.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <p>Utilizzando il servizio di abbreviazione savmrl.it, riconosci di aver letto, compreso e accettato la presente Informativa sulla privacy.</p>
                <p>Ultimo aggiornamento: 30 settembre 2026</p>
            </section>
        </div>

        <!-- ENGLISH (courtesy translation) -->
        <div id="lang-en" class="lang-content">
            <section class="horizontal-center-p text-align-justify">
                <p><em>This English version is provided for convenience only. In case of any discrepancy between the Italian and English versions, the <a href="javascript:switchLang('it', document.querySelector('.lang-tabs button'))">Italian version</a> shall prevail.</em></p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>1. Introduction &amp; Scope</h4>
                <p>Welcome to <strong>savmrl.it</strong> (the "Service", "we", "us", or "our"). This Service provides a URL shortening and redirection feature.</p>
                <p>This Privacy Policy explains how we collect, use, store, and protect data when you use our Service. We are committed to complying with the <strong>General Data Protection Regulation (GDPR)</strong> (EU Regulation 2016/679) and applicable Italian privacy laws.</p>
                <p>Our servers are hosted in <strong>Italy</strong> (via <strong>Aruba.it</strong>), and the Service operates under <strong>Italian jurisdiction</strong>.</p>
                <p>By using the Service, you agree to this Privacy Policy. If you do not agree, please do not use <code>savmrl.it</code>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>2. Data We Collect</h4>
                <p>We only collect the data necessary to provide the Service and ensure its proper functioning and security.</p>

                <h4>2.1 Data Provided by the User</h4>
                <ul>
                    <li>The <strong>original URL</strong> you want to shorten.</li>
                    <li>Optional settings: <em>maximum number of clicks</em>, <em>expiry date</em>, and an <em>access code / password</em> to restrict access.</li>
                </ul>
                <p>If you set an access code, it is stored <strong>encrypted (hashed)</strong> in our database, and the link is stored <strong>encrypted (AES-256) as well</strong>, so that <strong>even the service operator cannot view the plain text</strong>. Other data such as timestamps and the user's IP address are stored in clear text to ensure correct functionality.</p>

                <h4>2.2 Data Automatically Collected</h4>
                <ul>
                    <li>The public <strong>IP address</strong> of the client creating or accessing the link.</li>
                    <li>The <strong>timestamp</strong> of creation or access.</li>
                    <li>The <strong>click count</strong> for the shortened link.</li>
                    <li>The link <strong>expiry settings</strong> and any <strong>hashed access code</strong>.</li>
                </ul>
                <p>We do not collect additional personal data such as names, emails, or precise geolocation.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>3. Purpose and Legal Basis for Processing</h4>
                <table>
                    <thead>
                    <tr><th>Purpose</th><th>Legal Basis (GDPR)</th><th>Details</th></tr>
                    </thead>
                    <tbody>
                    <tr><td>Provide link shortening and redirection</td><td>Performance of a contract / user request</td><td>Processing is necessary to create and manage shortened links.</td></tr>
                    <tr><td>Enforce expiry, access codes, click limits</td><td>Legitimate interest</td><td>Necessary to provide requested link protection and validity features.</td></tr>
                    <tr><td>Prevent abuse and maintain security</td><td>Legitimate interest</td><td>Monitoring to protect service from spam, abuse, or attacks.</td></tr>
                    <tr><td>Technical logs and analytics</td><td>Legitimate interest</td><td>Used for reliability, debugging, and improving performance.</td></tr>
                    <tr><td>Legal compliance</td><td>Legal obligation</td><td>To respond to lawful requests or enforce rights.</td></tr>
                    </tbody>
                </table>
                <p>We do not process data for profiling, marketing, or advertising.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>4. Data Sharing and Disclosure</h4>
                <p><strong>We do not share, sell, rent, or disclose any user data to third parties under any circumstance.</strong></p>
                <p>Data are never transferred to external companies, analytics providers, or advertisers. Access to data is strictly limited to the service's system administrator for operational and security purposes only.</p>
                <p><strong>Exceptions:</strong> Data may be disclosed only when required by law or court order (e.g., judicial or police request) or to enforce our Terms of Service or defend legal claims, if necessary.</p>
                <p>No data are transferred outside the European Economic Area (EEA).</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>5. Data Retention</h4>
                <ul>
                    <li>Data related to a link (original URL, creation date, expiry, click count) are kept until the link is <strong>expired</strong> or <strong>deleted</strong>.</li>
                    <li><strong>IP addresses</strong> are automatically anonymized (set to null) after <strong>30 days</strong> from the date of collection. This applies to IP addresses stored in link creation records, click/visit records, and session records.</li>
                    <li>Expired or deleted links and their associated data may be purged after a short retention period.</li>
                    <li>When technically possible, users may request removal of a specific link and associated data.</li>
                </ul>
                <p>After deletion, stored data are permanently erased or anonymized.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>6. Data Security</h4>
                <p>We apply reasonable technical and organizational measures to protect data, including:</p>
                <ul>
                    <li>Hashed/encrypted storage for access codes;</li>
                    <li>Controlled and logged database access;</li>
                    <li>Firewall and server protections;</li>
                    <li>Regular security updates and audits.</li>
                </ul>
                <p>Despite precautions, no system is completely immune to unauthorized access or cyberattacks. The service provider is not liable for breaches or incidents beyond reasonable control.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>7. User Rights (GDPR)</h4>
                <p>If you are located in the EU/EEA, you have the following rights:</p>
                <ul>
                    <li>Access your personal data (Art. 15).</li>
                    <li>Rectify inaccurate or incomplete data (Art. 16).</li>
                    <li>Request erasure ("right to be forgotten", Art. 17).</li>
                    <li>Restrict processing (Art. 18).</li>
                    <li>Data portability, when applicable (Art. 20).</li>
                    <li>Object to processing based on legitimate interests (Art. 21).</li>
                    <li>Withdraw consent where applicable.</li>
                    <li>Lodge a complaint with the supervisory authority (Garante per la Protezione dei Dati Personali).</li>
                </ul>
                <p>Requests should be sent to the contact below. We may need to verify identity before acting on requests.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>8. Disclaimer and Limitation of Liability</h4>
                <p>The Service is provided "as is" without warranties. The operator of <strong>savmrl.it</strong> is not responsible for:</p>
                <ul>
                    <li>Content or safety of shortened links;</li>
                    <li>Continuous availability or correctness of the Service;</li>
                    <li>Damages resulting from malicious, fraudulent, or inappropriate use of the Service;</li>
                    <li>Data loss, technical issues, or temporary unavailability.</li>
                </ul>
                <p>Users are responsible for the URLs they submit and share.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>9. Data Controller &amp; Contact</h4>
                <p>
                    <strong>Data Controller:</strong> Saverio Morelli
                    <br>
                    Contact: <a href="https://www.saveriomorelli.com/contact-me/">https://www.saveriomorelli.com/contact-me/</a>
                </p>
                <p>For privacy inquiries or requests regarding your personal data, contact the email above. If unresolved, you may file a complaint with the Italian Data Protection Authority (Garante per la Protezione dei Dati Personali): <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener">https://www.garanteprivacy.it</a>.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <h4>10. Changes to This Privacy Policy</h4>
                <p>We may revise this Privacy Policy from time to time. The latest version will always be available at <a href="/alpha/privacy/">/alpha/privacy/</a> with the updated "last modified" date. Please review periodically.</p>
            </section>

            <section class="horizontal-center-p text-align-justify">
                <p>By using the savmrl.it shortener service, you acknowledge that you have read, understood, and agreed to this Privacy Policy.</p>
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
