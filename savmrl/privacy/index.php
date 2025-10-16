<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Privacy policy";
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">Privacy policy</h2>
        <div class="big-space"></div>
        <br>
        <section class="horizontal-center-p text-align-justify">
            <h4>1. Introduction &amp; Scope</h4>
            <p>Welcome to <strong>savmrl.it</strong> (the “Service”, “we”, “us”, or “our”). This Service provides a URL
                shortening and redirection feature.</p>
            <p>This Privacy Policy explains how we collect, use, store, and protect data when you use our Service. We
                are committed to complying with the <strong>General Data Protection Regulation (GDPR)</strong> (EU
                Regulation 2016/679) and applicable Italian privacy laws.</p>
            <p>Our servers are hosted in <strong>Italy</strong> (via <strong>Aruba.it</strong>), and the Service
                operates under <strong>Italian jurisdiction</strong>.</p>
            <p>By using the Service, you agree to this Privacy Policy. If you do not agree, please do not use <code>savmrl.it</code>.
            </p>
        </section>

        <section class="horizontal-center-p text-align-justify">
            <h4>2. Data We Collect</h4>
            <p>We only collect the data necessary to provide the Service and ensure its proper functioning and
                security.</p>

            <h4>2.1 Data Provided by the User</h4>
            <ul>
                <li>The <strong>original URL</strong> you want to shorten.</li>
                <li>Optional settings: <em>maximum number of clicks</em>, <em>expiry date</em>, and an <em>access code /
                        password</em> to restrict access.
                </li>
            </ul>
            <p>If you set an access code, it is stored <strong>encrypted (hashed)</strong> in our database, and the link
                is stored <strong>encrypted (AES-256) as well, </strong> so that <strong>even the service operator
                    cannot view the plain text</strong>. Other data such as timestamps and the user’s IP address are
                stored in clear text to ensure correct functionality.</p>

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
                <tr>
                    <th>Purpose</th>
                    <th>Legal Basis (GDPR)</th>
                    <th>Details</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Provide link shortening and redirection</td>
                    <td>Performance of a contract / user request</td>
                    <td>Processing is necessary to create and manage shortened links.</td>
                </tr>
                <tr>
                    <td>Enforce expiry, access codes, click limits</td>
                    <td>Legitimate interest</td>
                    <td>Necessary to provide requested link protection and validity features.</td>
                </tr>
                <tr>
                    <td>Prevent abuse and maintain security</td>
                    <td>Legitimate interest</td>
                    <td>Monitoring to protect service from spam, abuse, or attacks.</td>
                </tr>
                <tr>
                    <td>Technical logs and analytics</td>
                    <td>Legitimate interest</td>
                    <td>Used for reliability, debugging, and improving performance.</td>
                </tr>
                <tr>
                    <td>Legal compliance</td>
                    <td>Legal obligation</td>
                    <td>To respond to lawful requests or enforce rights.</td>
                </tr>
                </tbody>
            </table>
            <p>We do not process data for profiling, marketing, or advertising.</p>
        </section>

        <section class="horizontal-center-p text-align-justify">
            <h4>4. Data Sharing and Disclosure</h4>
            <p><strong>We do not share, sell, rent, or disclose any user data to third parties under any
                    circumstance.</strong></p>
            <p>Data are never transferred to external companies, analytics providers, or advertisers. Access to data is
                strictly limited to the service’s system administrator for operational and security purposes only.</p>
            <p><strong>Exceptions:</strong> Data may be disclosed only when required by law or court order (e.g.,
                judicial or police request) or to enforce our Terms of Service or defend legal claims, if necessary.</p>
            <p>No data are transferred outside the European Economic Area (EEA).</p>
        </section>

        <section class="horizontal-center-p text-align-justify">
            <h4>5. Data Retention</h4>
            <ul>
                <li>Data related to a link (original URL, creation date, IP address, expiry, click count) are kept until
                    the link is <strong>expired</strong> or <strong>deleted</strong>.
                </li>
                <li>Expired or deleted links may be purged after a short retention period.</li>
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
            <p>Despite precautions, no system is completely immune to unauthorized access or cyberattacks. The service
                provider is not liable for breaches or incidents beyond reasonable control.</p>
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
                <li>Lodge a complaint with the supervisory authority (Garante per la Protezione dei Dati Personali).
                </li>
            </ul>
            <p>Requests should be sent to the contact below. We may need to verify identity before acting on
                requests.</p>
        </section>

        <section class="horizontal-center-p text-align-justify">
            <h4>8. Disclaimer and Limitation of Liability</h4>
            <p>The Service is provided "as is" without warranties. The operator of <strong>savmrl.it</strong> is not
                responsible for:</p>
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
                Contact: <a
                        href="https://www.saveriomorelli.com/contact-me/">https://www.saveriomorelli.com/contact-me/</a>
            </p>
            <p>
                For privacy inquiries or requests regarding your personal data, contact the email above. If unresolved,
                you may file a complaint with the Italian Data Protection Authority (Garante per la Protezione dei Dati
                Personali): <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener">https://www.garanteprivacy.it</a>.
            </p>
        </section>

        <section class="horizontal-center-p text-align-justify">
            <h4>10. Changes to This Privacy Policy</h4>
            <p>We may revise this Privacy Policy from time to time. The latest version will always be available at <a
                        href="https://www.savmrl.it/privacy">https://www.savmrl.it/privacy</a> with the updated "last
                modified" date. Please review periodically.</p>
        </section>

        <section class="horizontal-center-p text-align-justify">
            <p>
                By using the savmrl.it shortener service, you acknowledge that you have read, understood, and agreed to
                this Privacy Policy.
            </p>
            <p>
                Last updated: 16 Oct 2025
            </p>
        </section>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>