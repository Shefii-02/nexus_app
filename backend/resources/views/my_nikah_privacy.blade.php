<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — My Nikah Islamic Matrimony App</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Amiri:wght@400;700&display=swap');

        :root {
            --r: #8e1a2e;
            --rl: #c0392b;
            --g: #c9a227;
            --gd: #8B6914;
            --t: #1a1a1a;
            --m: #555;
            --li: #f9f9f9;
            --br: #eee
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: Inter, sans-serif;
            background: #fafafa;
            color: var(--t);
            line-height: 1.7
        }

        /* HEADER */
        header {
            background: linear-gradient(135deg, #6e0a1e, #8e1a2e, #c0392b);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2)
        }

        .nav-inner {
            max-width: 900px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px
        }

        .logo {
            font-family: Amiri, serif;
            font-size: 22px;
            color: #fff;
            letter-spacing: 1px;
            text-decoration: none
        }

        .logo span {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 2px;
            text-transform: uppercase;
            display: block;
            margin-top: -2px
        }

        .nav-badge {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 10px;
            padding: 3px 10px;
            border-radius: 15px;
            font-weight: 500;
            margin-left: auto;
            border: 1px solid rgba(255, 255, 255, 0.25)
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #8e1a2e 0%, #c0392b 60%, #e8956d 100%);
            padding: 52px 24px 44px;
            text-align: center
        }

        .hero-logo {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            border: 1.5px solid rgba(255, 255, 255, 0.25)
        }

        .hero-logo svg {
            width: 38px;
            height: 38px
        }

        .hero h1 {
            font-family: Amiri, serif;
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px
        }

        .hero p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.75);
            max-width: 500px;
            margin: 0 auto 16px
        }

        .hero-meta {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap
        }

        .hero-meta span {
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.85);
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2)
        }

        /* CONTENT */
        .container {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 20px
        }

        /* TOC */
        .toc-wrap {
            background: #fff;
            border: 1px solid var(--br);
            border-radius: 14px;
            padding: 24px 28px;
            margin: 32px auto;
            max-width: 860px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05)
        }

        .toc-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--r);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .toc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 24px
        }

        .toc-item {
            font-size: 13px;
            color: var(--r);
            text-decoration: none;
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px dashed #eee;
            transition: color 0.2s
        }

        .toc-item:hover {
            color: #c0392b
        }

        .toc-item span {
            color: #aaa;
            font-size: 11px;
            font-weight: 600;
            min-width: 22px
        }

        /* SECTIONS */
        .section {
            background: #fff;
            border-radius: 14px;
            padding: 32px 36px;
            margin-bottom: 18px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0
        }

        .section-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1.5px solid #f0f0f0
        }

        .section-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fef0ee, #fde8e4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0
        }

        .section-num {
            font-size: 11px;
            color: var(--r);
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--t);
            margin-top: 2px
        }

        h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--t)
        }

        h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--t);
            margin: 16px 0 8px
        }

        p {
            font-size: 14px;
            color: var(--m);
            margin-bottom: 12px
        }

        p:last-child {
            margin-bottom: 0
        }

        ul,
        ol {
            padding-left: 20px;
            margin-bottom: 12px
        }

        li {
            font-size: 14px;
            color: var(--m);
            margin-bottom: 6px;
            line-height: 1.6
        }

        li strong {
            color: var(--t)
        }

        .highlight-box {
            background: linear-gradient(135deg, #fff8f0, #fef0ee);
            border: 1.5px solid #f0c8c0;
            border-radius: 10px;
            padding: 14px 18px;
            margin: 16px 0
        }

        .highlight-box p {
            color: #8e1a2e;
            font-weight: 500;
            margin: 0
        }

        .highlight-box.green {
            background: linear-gradient(135deg, #f0fff4, #e8f8f0);
            border-color: #a8d5b5
        }

        .highlight-box.green p {
            color: #1a5c1a
        }

        .highlight-box.blue {
            background: linear-gradient(135deg, #f0f4ff, #e8f0fe);
            border-color: #a8b5d5
        }

        .highlight-box.blue p {
            color: #1a3c8e
        }

        .highlight-box.gold {
            background: linear-gradient(135deg, #fffdf0, #fff8e8);
            border-color: #d5c07a
        }

        .highlight-box.gold p {
            color: #7a5c00
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 13px
        }

        .data-table th {
            background: var(--r);
            color: #fff;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600
        }

        .data-table th:first-child {
            border-radius: 8px 0 0 0
        }

        .data-table th:last-child {
            border-radius: 0 8px 0 0
        }

        .data-table td {
            padding: 9px 14px;
            border-bottom: 1px solid #f0f0f0;
            color: var(--m);
            vertical-align: top
        }

        .data-table tr:last-child td {
            border-bottom: none
        }

        .data-table tr:nth-child(even) td {
            background: #fafafa
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600
        }

        .badge-r {
            background: #fef0ee;
            color: var(--r)
        }

        .badge-g {
            background: #e8f8f0;
            color: #1a5c1a
        }

        .badge-y {
            background: #fff8e0;
            color: #7a5c00
        }

        .badge-b {
            background: #e8f0fe;
            color: #1a3c8e
        }

        /* CONTACT */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin: 16px 0
        }

        .contact-card {
            background: var(--li);
            border-radius: 10px;
            padding: 14px;
            text-align: center;
            border: 1px solid var(--br)
        }

        .contact-card .icon {
            font-size: 22px;
            margin-bottom: 6px
        }

        .contact-card .label {
            font-size: 11px;
            color: #aaa;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px
        }

        .contact-card .value {
            font-size: 13px;
            color: var(--r);
            font-weight: 500;
            margin-top: 3px;
            word-break: break-all
        }

        /* FOOTER */
        footer {
            background: linear-gradient(135deg, #1a0a0e, #3a1020);
            padding: 40px 24px;
            text-align: center;
            margin-top: 40px
        }

        footer .footer-logo {
            font-family: Amiri, serif;
            font-size: 22px;
            color: #fff;
            margin-bottom: 8px
        }

        footer p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 4px
        }

        footer .footer-links {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 14px 0
        }

        footer .footer-links a {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.2s
        }

        footer .footer-links a:hover {
            color: #fff
        }

        footer .footer-divider {
            width: 40px;
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
            margin: 14px auto
        }

        /* LAST UPDATED BANNER */
        .update-banner {
            background: linear-gradient(135deg, #fff8f0, var(--li));
            border: 1px solid #f0d8c0;
            border-radius: 10px;
            padding: 10px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #8B6914
        }

        /* RESPONSIVE */
        @media(max-width:640px) {
            .toc-grid {
                grid-template-columns: 1fr
            }

            .contact-grid {
                grid-template-columns: 1fr
            }

            .section {
                padding: 22px 18px
            }

            .hero h1 {
                font-size: 24px
            }

            .data-table {
                font-size: 12px
            }

            .data-table th,
            .data-table td {
                padding: 8px 10px
            }
        }
    </style>
</head>

<body>

    <!-- NAV -->
    <header>
        <div class="nav-inner">
            <a href="#" class="logo">🌙 My Nikah<span>Islamic Matrimony</span></a>
            <div class="nav-badge">Privacy Policy</div>
        </div>
    </header>

    <!-- HERO -->
    <div class="hero">
        <div class="hero-logo">
            <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 3C20 3 8 10 8 20C8 26 13 31 20 33C27 31 32 26 32 20C32 10 20 3 20 3Z"
                    fill="rgba(255,255,255,0.9)" />
                <circle cx="20" cy="8" r="3" fill="#c0392b" />
                <path d="M14 20 Q20 14 26 20" stroke="#c0392b" stroke-width="1.5" fill="none" />
            </svg>
        </div>
        <h1>Privacy Policy</h1>
        <p>We are committed to protecting your personal information and your right to privacy in accordance with Islamic
            values and applicable law.</p>
        <div class="hero-meta">
            <span>📅 Effective: June 22, 2025</span>
            <span>🔄 Last Updated: June 22, 2026</span>
            <span>📱 My Nikah App v1.0</span>
        </div>
    </div>

    <div class="container">

        <!-- UPDATE BANNER -->
        <div class="update-banner" style="margin-top:28px">
            🌙 &nbsp;This Privacy Policy applies to the <strong>My Nikah – Islamic Matrimony</strong> mobile application
            (Android &amp; iOS) and our website. By using My Nikah, you agree to the terms described here.
        </div>

        <!-- TABLE OF CONTENTS -->
        <div class="toc-wrap">
            <div class="toc-title">📋 &nbsp;Table of Contents</div>
            <div class="toc-grid">
                <a href="#s1" class="toc-item"><span>01</span> Information We Collect</a>
                <a href="#s2" class="toc-item"><span>02</span> How We Use Your Information</a>
                <a href="#s3" class="toc-item"><span>03</span> Information Sharing</a>
                <a href="#s4" class="toc-item"><span>04</span> Profile Lock &amp; Photo Privacy</a>
                <a href="#s5" class="toc-item"><span>05</span> Data Security</a>
                <a href="#s6" class="toc-item"><span>06</span> Your Rights &amp; Choices</a>
                <a href="#s7" class="toc-item"><span>07</span> Children's Privacy</a>
                <a href="#s8" class="toc-item"><span>08</span> Cookies &amp; Tracking</a>
                <a href="#s9" class="toc-item"><span>09</span> Third-Party Services</a>
                <a href="#s10" class="toc-item"><span>10</span> Data Retention</a>
                <a href="#s11" class="toc-item"><span>11</span> International Transfers</a>
                <a href="#s12" class="toc-item"><span>12</span> Changes to This Policy</a>
                <a href="#s13" class="toc-item"><span>13</span> Contact Us</a>
                <a href="#s14" class="toc-item"><span>14</span> Grievance Officer</a>
            </div>
        </div>

        <!-- SECTION 1 -->
        <div class="section" id="s1">
            <div class="section-header">
                <div class="section-icon">📋</div>
                <div>
                    <div class="section-num">Section 01</div>
                    <div class="section-title">Information We Collect</div>
                </div>
            </div>

            <h3>1.1 Information You Provide Directly</h3>
            <table class="data-table">
                <tr>
                    <th>Category</th>
                    <th>Data Collected</th>
                    <th>Purpose</th>
                </tr>
                <tr>
                    <td><strong>Account</strong></td>
                    <td>Full name, email address, phone number, date of birth, gender</td>
                    <td>Creating and verifying your account</td>
                </tr>
                <tr>
                    <td><strong>Profile</strong></td>
                    <td>Photos, height, weight, complexion, sect/madhab, prayer frequency, education, profession,
                        income, marital status, family details</td>
                    <td>Building your matrimony profile</td>
                </tr>
                <tr>
                    <td><strong>Islamic Details</strong></td>
                    <td>Sect, prayer habits, Hijab/Beard status, Quran knowledge, Islamic education</td>
                    <td>Faith-based compatibility matching</td>
                </tr>
                <tr>
                    <td><strong>Family Info</strong></td>
                    <td>Father's/mother's occupation, family type, number of siblings, Wali name &amp; contact</td>
                    <td>Family-supervised matchmaking</td>
                </tr>
                <tr>
                    <td><strong>Preferences</strong></td>
                    <td>Partner age range, location, education, profession, deen preferences</td>
                    <td>Personalising match recommendations</td>
                </tr>
                <tr>
                    <td><strong>Communications</strong></td>
                    <td>Chat messages, voice/video call logs (metadata only), interest requests</td>
                    <td>Facilitating halal communication</td>
                </tr>
                <tr>
                    <td><strong>Verification</strong></td>
                    <td>Government ID (stored encrypted, deleted after verification), selfie photos</td>
                    <td>Profile verification &amp; safety</td>
                </tr>
            </table>

            <h3>1.2 Information Collected Automatically</h3>
            <ul>
                <li><strong>Device Information:</strong> Device type, OS version, unique device identifiers, app version
                </li>
                <li><strong>Usage Data:</strong> Features used, screens visited, time spent, swipe patterns (anonymised)
                </li>
                <li><strong>Location Data:</strong> Approximate location (city/district level) for "Nearby Profiles" —
                    only when you grant permission</li>
                <li><strong>Log Data:</strong> IP address, access timestamps, crash reports</li>
                <li><strong>Shake/Gesture Events:</strong> When you use the "Shake to Match" feature, we record the
                    trigger event only — no motion data is stored</li>
            </ul>

            <h3>1.3 Information from Third Parties</h3>
            <ul>
                <li>If you sign in with Google, Apple, or Facebook, we receive your name, email, and profile picture
                    from that provider</li>
                <li>Payment processors (Razorpay / Google Pay / Apple Pay) share transaction confirmation — we do not
                    store card details</li>
            </ul>

            <div class="highlight-box gold">
                <p>🌙 <strong>Islamic Principle:</strong> We collect only the information necessary to provide our halal
                    matchmaking service. We do not collect unnecessary personal data and treat all user information with
                    the trust (Amanah) it deserves.</p>
            </div>
        </div>

        <!-- SECTION 2 -->
        <div class="section" id="s2">
            <div class="section-header">
                <div class="section-icon">🎯</div>
                <div>
                    <div class="section-num">Section 02</div>
                    <div class="section-title">How We Use Your Information</div>
                </div>
            </div>

            <p>We use the information we collect for the following purposes:</p>

            <h3>2.1 Core Matchmaking Services</h3>
            <ul>
                <li>Creating, displaying, and managing your matrimony profile</li>
                <li>Generating AI-powered compatibility scores and match recommendations</li>
                <li>Enabling you to send and receive interests, chat, and conduct supervised video calls</li>
                <li>Powering the "Nearby Profiles" and "Shake to Match" features</li>
                <li>Facilitating My Nikah Referral and Wali (guardian) supervision features</li>
            </ul>

            <h3>2.2 Safety &amp; Verification</h3>
            <ul>
                <li>Verifying your identity to maintain a trusted community</li>
                <li>Detecting and preventing fake profiles, fraud, and harassment</li>
                <li>Reviewing reported content and taking enforcement actions</li>
                <li>Monitoring for Islamically inappropriate behaviour</li>
            </ul>

            <h3>2.3 Service Improvement</h3>
            <ul>
                <li>Analysing aggregated, anonymised usage patterns to improve our AI matching algorithm</li>
                <li>Conducting research to improve compatibility scoring</li>
                <li>Fixing bugs and improving app performance</li>
            </ul>

            <h3>2.4 Communications</h3>
            <ul>
                <li>Sending you notifications about new interests, matches, messages, and profile views</li>
                <li>Sending prayer time reminders (if you enable this feature)</li>
                <li>Service-related announcements, updates, and security alerts</li>
                <li>Promotional communications (only if you opt in)</li>
            </ul>

            <h3>2.5 Legal Compliance</h3>
            <ul>
                <li>Complying with applicable laws, regulations, and legal processes</li>
                <li>Enforcing our Terms of Service and community guidelines</li>
            </ul>

            <div class="highlight-box green">
                <p>✅ <strong>We do NOT:</strong> sell your personal data to third parties, use your data for targeted
                    advertising by external parties, or use your data for any purpose unrelated to the matrimony
                    service.</p>
            </div>
        </div>

        <!-- SECTION 3 -->
        <div class="section" id="s3">
            <div class="section-header">
                <div class="section-icon">🤝</div>
                <div>
                    <div class="section-num">Section 03</div>
                    <div class="section-title">Information Sharing &amp; Disclosure</div>
                </div>
            </div>

            <p>We share your information only in the following limited circumstances:</p>

            <h3>3.1 With Other Users (Your Choice)</h3>
            <ul>
                <li>Your <strong>public profile</strong> (name, age, location, photos you choose to make public, Islamic
                    details) is visible to other registered users</li>
                <li>Your <strong>contact details</strong> are shared only after both parties accept an interest request
                    — and only if you have enabled this</li>
                <li>Your <strong>locked photos</strong> are shared only when you explicitly approve a photo unlock
                    request</li>
                <li>In <strong>Wali (Family Chat)</strong> mode, all parties in the group can see messages sent in that
                    group</li>
            </ul>

            <h3>3.2 With Service Providers</h3>
            <table class="data-table">
                <tr>
                    <th>Provider Type</th>
                    <th>Purpose</th>
                    <th>Data Shared</th>
                </tr>
                <tr>
                    <td>Cloud Hosting (AWS / Firebase)</td>
                    <td>Storing app data securely</td>
                    <td>All app data (encrypted)</td>
                </tr>
                <tr>
                    <td>Payment Processors (Razorpay)</td>
                    <td>Processing subscription payments</td>
                    <td>Transaction amount, reference ID</td>
                </tr>
                <tr>
                    <td>SMS / OTP Gateway</td>
                    <td>Sending OTP verification codes</td>
                    <td>Phone number only</td>
                </tr>
                <tr>
                    <td>Push Notifications (FCM / APNs)</td>
                    <td>Delivering app notifications</td>
                    <td>Device token, notification content</td>
                </tr>
                <tr>
                    <td>Analytics (anonymised)</td>
                    <td>Understanding app usage</td>
                    <td>Anonymised usage events</td>
                </tr>
                <tr>
                    <td>Video Call Infrastructure (WebRTC)</td>
                    <td>Powering video calls</td>
                    <td>Encrypted media streams only</td>
                </tr>
            </table>
            <p>All service providers are bound by data processing agreements and are prohibited from using your data for
                their own purposes.</p>

            <h3>3.3 Legal Disclosures</h3>
            <p>We may disclose your information if required to do so by law, court order, or governmental authority, or
                when we believe in good faith that disclosure is necessary to protect rights, property, or safety.</p>

            <h3>3.4 Business Transfers</h3>
            <p>In the event of a merger, acquisition, or sale of assets, your data may be transferred. We will notify
                you via email or in-app notice and give you the opportunity to delete your account before such transfer
                occurs.</p>
        </div>

        <!-- SECTION 4 -->
        <div class="section" id="s4">
            <div class="section-header">
                <div class="section-icon">🔒</div>
                <div>
                    <div class="section-num">Section 04</div>
                    <div class="section-title">Profile Lock &amp; Photo Privacy</div>
                </div>
            </div>

            <p>My Nikah offers unique privacy features designed with Islamic modesty values in mind:</p>

            <h3>4.1 Profile Lock</h3>
            <ul>
                <li>When <strong>Profile Lock</strong> is enabled, your full profile details (education, contact, family
                    info) are hidden from all users</li>
                <li>Only users whose interest you accept can view your complete profile</li>
                <li>Your name, age, city, and basic Islamic details remain visible to help others find you</li>
            </ul>

            <h3>4.2 Photo Lock</h3>
            <ul>
                <li>When <strong>Photo Lock</strong> is enabled, your photos are blurred/hidden from general viewers
                </li>
                <li>Users must send a formal photo unlock request with a stated intention</li>
                <li>You receive a notification and can <strong>Allow</strong> or <strong>Decline</strong> each request
                    individually</li>
                <li>You can revoke photo access at any time — previously unlocked photos will be re-locked</li>
            </ul>

            <h3>4.3 Privacy Mode</h3>
            <ul>
                <li>In <strong>Privacy Mode</strong>, you do not appear in search results or "Nearby Profiles"</li>
                <li>You can still browse profiles, but others cannot find you unless you send them an interest first
                </li>
            </ul>

            <h3>4.4 Profile Pause</h3>
            <ul>
                <li>You can <strong>Pause</strong> your profile for a set period — it will be hidden from all searches
                    and recommendations</li>
                <li>Your data, matches, and chat history are preserved during the pause</li>
            </ul>

            <div class="highlight-box">
                <p>🔐 <strong>Important:</strong> Even with privacy features enabled, users who have already received
                    and saved your information before you activated these settings may retain that information. We
                    encourage users to activate privacy settings before sharing details.</p>
            </div>
        </div>

        <!-- SECTION 5 -->
        <div class="section" id="s5">
            <div class="section-header">
                <div class="section-icon">🛡️</div>
                <div>
                    <div class="section-num">Section 05</div>
                    <div class="section-title">Data Security</div>
                </div>
            </div>

            <p>We implement industry-standard security measures to protect your personal information:</p>

            <ul>
                <li><strong>Encryption in Transit:</strong> All data transmitted between the app and our servers is
                    encrypted using TLS 1.3</li>
                <li><strong>Encryption at Rest:</strong> Sensitive data (including verification documents and private
                    messages) is encrypted at rest using AES-256</li>
                <li><strong>Two-Factor Authentication (2FA):</strong> Available via SMS OTP, email OTP, and biometric
                    (Face ID / Fingerprint)</li>
                <li><strong>Secure Video Calls:</strong> All video calls are end-to-end encrypted using WebRTC with
                    DTLS-SRTP</li>
                <li><strong>Government ID Handling:</strong> ID documents submitted for verification are encrypted,
                    verified, and permanently deleted within 30 days of verification — they are never shared with other
                    users</li>
                <li><strong>Regular Security Audits:</strong> We conduct periodic security assessments and penetration
                    testing</li>
                <li><strong>Access Controls:</strong> Employee access to user data is role-based, logged, and requires
                    multi-factor authentication</li>
            </ul>

            <div class="highlight-box">
                <p>⚠️ <strong>Your Responsibility:</strong> While we work hard to protect your information, no method of
                    internet transmission is 100% secure. Please use a strong password, enable 2FA, and do not share
                    your login credentials with anyone.</p>
            </div>
        </div>

        <!-- SECTION 6 -->
        <div class="section" id="s6">
            <div class="section-header">
                <div class="section-icon">⚖️</div>
                <div>
                    <div class="section-num">Section 06</div>
                    <div class="section-title">Your Rights &amp; Choices</div>
                </div>
            </div>

            <p>You have the following rights regarding your personal data:</p>

            <table class="data-table">
                <tr>
                    <th>Right</th>
                    <th>What It Means</th>
                    <th>How to Exercise</th>
                </tr>
                <tr>
                    <td><span class="badge badge-g">Access</span></td>
                    <td>Request a copy of the personal data we hold about you</td>
                    <td>Settings → Privacy → Download My Data</td>
                </tr>
                <tr>
                    <td><span class="badge badge-b">Correction</span></td>
                    <td>Update or correct inaccurate information in your profile</td>
                    <td>Edit Profile anytime in the app</td>
                </tr>
                <tr>
                    <td><span class="badge badge-r">Deletion</span></td>
                    <td>Request deletion of your account and personal data</td>
                    <td>Settings → Account Actions → Delete Account</td>
                </tr>
                <tr>
                    <td><span class="badge badge-y">Portability</span></td>
                    <td>Receive your data in a machine-readable format</td>
                    <td>Contact us at privacy@mynikah.com</td>
                </tr>
                <tr>
                    <td><span class="badge badge-g">Restriction</span></td>
                    <td>Restrict how we process your data in certain circumstances</td>
                    <td>Contact us at privacy@mynikah.com</td>
                </tr>
                <tr>
                    <td><span class="badge badge-b">Objection</span></td>
                    <td>Object to processing based on legitimate interests or for direct marketing</td>
                    <td>Settings → Notifications → Opt-Out</td>
                </tr>
                <tr>
                    <td><span class="badge badge-r">Withdraw Consent</span></td>
                    <td>Withdraw any consent you have given us at any time</td>
                    <td>In-app toggles or contact us</td>
                </tr>
            </table>

            <h3>Account Deletion</h3>
            <p>When you delete your account:</p>
            <ul>
                <li>Your profile is immediately removed from all search results and other users' views</li>
                <li>Your data is permanently deleted within <strong>30 days</strong> from our active systems</li>
                <li>Backup copies are purged within <strong>90 days</strong></li>
                <li>Some data may be retained if required by law (e.g., payment records for 7 years per Indian tax law)
                </li>
                <li>Messages you have sent to other users in accepted chat conversations may remain visible to those
                    recipients</li>
            </ul>

            <div class="highlight-box green">
                <p>✅ To exercise any right, email us at <strong>privacy@mynikah.com</strong> with your registered phone
                    number. We will respond within <strong>30 days</strong>. For Indian users, we comply with the
                    Digital Personal Data Protection Act, 2023.</p>
            </div>
        </div>

        <!-- SECTION 7 -->
        <div class="section" id="s7">
            <div class="section-header">
                <div class="section-icon">👶</div>
                <div>
                    <div class="section-num">Section 07</div>
                    <div class="section-title">Children's Privacy</div>
                </div>
            </div>

            <div class="highlight-box">
                <p>🚫 <strong>My Nikah is strictly for users aged 18 and above.</strong> We do not knowingly collect
                    personal information from anyone under the age of 18. If you are under 18, please do not use this
                    app or provide any information.</p>
            </div>

            <p>If we become aware that we have inadvertently collected personal information from a person under 18
                without parental consent, we will take steps to delete that information immediately. If you believe we
                have collected data from a minor, please contact us immediately at
                <strong>privacy@mynikah.com</strong>.</p>

            <p>During registration, we collect your date of birth and verify that you are at least 18 years old.
                Accounts that appear to belong to minors are suspended pending verification and deleted if confirmed.
            </p>
        </div>

        <!-- SECTION 8 -->
        <div class="section" id="s8">
            <div class="section-header">
                <div class="section-icon">🍪</div>
                <div>
                    <div class="section-num">Section 08</div>
                    <div class="section-title">Cookies &amp; Tracking Technologies</div>
                </div>
            </div>

            <h3>Mobile App</h3>
            <p>Our mobile app uses the following local storage technologies:</p>
            <ul>
                <li><strong>Session Tokens:</strong> Secure tokens stored locally to keep you logged in</li>
                <li><strong>App Preferences:</strong> Your language, theme, notification, and privacy settings</li>
                <li><strong>Cache:</strong> Temporarily stored profile images to improve loading speed — cleared when
                    you log out</li>
                <li><strong>Analytics SDK:</strong> Anonymised usage events (no personally identifiable information)
                </li>
            </ul>

            <h3>Website (if applicable)</h3>
            <ul>
                <li><strong>Essential Cookies:</strong> Required for the website to function (session management,
                    security) — cannot be disabled</li>
                <li><strong>Analytics Cookies:</strong> Anonymised data about page visits — can be disabled in cookie
                    settings</li>
                <li><strong>No Advertising Cookies:</strong> We do not use cookies for advertising or tracking across
                    other websites</li>
            </ul>

            <p>You can manage cookie preferences via your browser settings or our in-app Privacy Settings.</p>
        </div>

        <!-- SECTION 9 -->
        <div class="section" id="s9">
            <div class="section-header">
                <div class="section-icon">🔗</div>
                <div>
                    <div class="section-num">Section 09</div>
                    <div class="section-title">Third-Party Services &amp; Links</div>
                </div>
            </div>

            <p>Our app integrates with the following third-party services. Each has its own privacy policy:</p>

            <table class="data-table">
                <tr>
                    <th>Service</th>
                    <th>Purpose</th>
                    <th>Privacy Policy</th>
                </tr>
                <tr>
                    <td>Google Firebase</td>
                    <td>Push notifications, authentication, cloud storage</td>
                    <td>policies.google.com/privacy</td>
                </tr>
                <tr>
                    <td>Google Sign-In</td>
                    <td>Optional social login</td>
                    <td>policies.google.com/privacy</td>
                </tr>
                <tr>
                    <td>Apple Sign In</td>
                    <td>Optional social login (iOS)</td>
                    <td>apple.com/privacy</td>
                </tr>
                <tr>
                    <td>Razorpay</td>
                    <td>Payment processing for subscriptions</td>
                    <td>razorpay.com/privacy</td>
                </tr>
                <tr>
                    <td>WebRTC / Agora</td>
                    <td>Video and audio calls</td>
                    <td>agora.io/en/privacy-policy</td>
                </tr>
                <tr>
                    <td>Twilio</td>
                    <td>SMS OTP delivery</td>
                    <td>twilio.com/legal/privacy</td>
                </tr>
            </table>

            <p>We are not responsible for the privacy practices of these third parties. We recommend reviewing their
                policies. If you have concerns about a specific integration, please contact us.</p>
        </div>

        <!-- SECTION 10 -->
        <div class="section" id="s10">
            <div class="section-header">
                <div class="section-icon">⏳</div>
                <div>
                    <div class="section-num">Section 10</div>
                    <div class="section-title">Data Retention</div>
                </div>
            </div>

            <p>We retain your information only for as long as necessary:</p>

            <table class="data-table">
                <tr>
                    <th>Data Type</th>
                    <th>Retention Period</th>
                    <th>Reason</th>
                </tr>
                <tr>
                    <td>Active account data</td>
                    <td>Duration of account + 30 days</td>
                    <td>Service delivery</td>
                </tr>
                <tr>
                    <td>Government ID (verification)</td>
                    <td>Deleted within 30 days of verification</td>
                    <td>Minimise sensitive data storage</td>
                </tr>
                <tr>
                    <td>Chat messages</td>
                    <td>Deleted when account is deleted</td>
                    <td>Service delivery; user control</td>
                </tr>
                <tr>
                    <td>Payment records</td>
                    <td>7 years</td>
                    <td>Indian tax &amp; accounting law</td>
                </tr>
                <tr>
                    <td>Security/audit logs</td>
                    <td>1 year</td>
                    <td>Fraud prevention, legal compliance</td>
                </tr>
                <tr>
                    <td>Anonymised analytics</td>
                    <td>3 years</td>
                    <td>Service improvement</td>
                </tr>
                <tr>
                    <td>Deleted account data</td>
                    <td>Purged within 90 days</td>
                    <td>Backup systems clearance</td>
                </tr>
            </table>

            <p>After the applicable retention period, data is securely deleted or anonymised so it can no longer be
                linked to you.</p>
        </div>

        <!-- SECTION 11 -->
        <div class="section" id="s11">
            <div class="section-header">
                <div class="section-icon">🌍</div>
                <div>
                    <div class="section-num">Section 11</div>
                    <div class="section-title">International Data Transfers</div>
                </div>
            </div>

            <p>My Nikah is primarily designed for users in India and the Gulf region. Our servers are located in India (AWS
                Mumbai region). However, some of our service providers (such as Google Firebase and Apple) may process
                data in other countries.</p>

            <p>When we transfer data internationally, we ensure appropriate safeguards are in place including:</p>
            <ul>
                <li>Standard contractual clauses approved by relevant data protection authorities</li>
                <li>Adequacy decisions where applicable</li>
                <li>Ensuring service providers maintain equivalent data protection standards</li>
            </ul>

            <p>For users in the European Economic Area (EEA) or United Kingdom, we rely on Standard Contractual Clauses
                as the legal mechanism for international transfers.</p>
        </div>

        <!-- SECTION 12 -->
        <div class="section" id="s12">
            <div class="section-header">
                <div class="section-icon">📝</div>
                <div>
                    <div class="section-num">Section 12</div>
                    <div class="section-title">Changes to This Privacy Policy</div>
                </div>
            </div>

            <p>We may update this Privacy Policy from time to time. When we make significant changes, we will:</p>
            <ul>
                <li>Update the "Last Updated" date at the top of this page</li>
                <li>Send you an in-app notification and/or email about the changes</li>
                <li>For material changes, ask for your renewed consent where required by law</li>
            </ul>

            <p>We encourage you to review this Privacy Policy periodically. Your continued use of NiMy Nikahkah after changes
                are posted constitutes your acceptance of the updated policy.</p>

            <div class="highlight-box blue">
                <p>📌 <strong>Previous versions</strong> of this Privacy Policy are available upon request. Email us at
                    <strong>privacy@mynikah.com</strong> with the subject line "Previous Privacy Policy" and the
                    approximate date you need.</p>
            </div>
        </div>

        <!-- SECTION 13 -->
        <div class="section" id="s13">
            <div class="section-header">
                <div class="section-icon">📬</div>
                <div>
                    <div class="section-num">Section 13</div>
                    <div class="section-title">Contact Us</div>
                </div>
            </div>

            <p>If you have any questions, concerns, or requests regarding this Privacy Policy or how we handle your
                data, please contact us:</p>

            <div class="contact-grid">
                <div class="contact-card">
                    <div class="icon">📧</div>
                    <div class="label">Email</div>
                    <div class="value">privacy@mynikah.com</div>
                </div>
                <div class="contact-card">
                    <div class="icon">📱</div>
                    <div class="label">Support</div>
                    <div class="value">support@mynikah.com</div>
                </div>
                <div class="contact-card">
                    <div class="icon">🌐</div>
                    <div class="label">Website</div>
                    <div class="value">www.mynikah.com</div>
                </div>
            </div>

            <h3>Mailing Address</h3>
            <div
                style="background:var(--li);border-radius:10px;padding:16px 20px;margin-top:10px;border:1px solid var(--br)">
                <p style="margin:0;font-size:14px;color:var(--t);line-height:1.8">
                    <strong>My Nikah Technologies Pvt. Ltd.</strong><br>
                    [Your Company Address]<br>
                    [City, State, PIN Code]<br>
                    India
                </p>
            </div>

            <p style="margin-top:14px">We aim to respond to all privacy-related requests within <strong>30
                    days</strong> of receipt. For urgent matters, please mark your email with the subject line
                <strong>"URGENT PRIVACY REQUEST"</strong>.</p>
        </div>

        <!-- SECTION 14 -->
        <div class="section" id="s14">
            <div class="section-header">
                <div class="section-icon">🏛️</div>
                <div>
                    <div class="section-num">Section 14</div>
                    <div class="section-title">Grievance Officer (India)</div>
                </div>
            </div>

            <p>In accordance with the Information Technology Act, 2000 and the Digital Personal Data Protection Act,
                2023 (India), the details of the Grievance Officer are:</p>

            <div style="background:var(--li);border-radius:10px;padding:18px 22px;border:1px solid var(--br)">
                <table style="width:100%;font-size:14px">
                    <tr style="margin-bottom:8px">
                        <td style="color:#aaa;width:140px;padding-bottom:8px">Name:</td>
                        <td style="color:var(--t);font-weight:600;padding-bottom:8px">[Grievance Officer Name]</td>
                    </tr>
                    <tr style="margin-bottom:8px">
                        <td style="color:#aaa;padding-bottom:8px">Designation:</td>
                        <td style="color:var(--t);padding-bottom:8px">Data Protection Officer / Grievance Officer</td>
                    </tr>
                    <tr style="margin-bottom:8px">
                        <td style="color:#aaa;padding-bottom:8px">Email:</td>
                        <td style="color:var(--r);padding-bottom:8px">grievance@mynikah.com</td>
                    </tr>
                    <tr style="margin-bottom:8px">
                        <td style="color:#aaa;padding-bottom:8px">Phone:</td>
                        <td style="color:var(--t);padding-bottom:8px">+91 [Your Phone Number]</td>
                    </tr>
                    <tr>
                        <td style="color:#aaa">Address:</td>
                        <td style="color:var(--t)">[Company Address], India</td>
                    </tr>
                </table>
            </div>

            <p style="margin-top:14px">The Grievance Officer shall acknowledge your complaint within <strong>48
                    hours</strong> and resolve it within <strong>30 days</strong> from the date of receipt.</p>

            <div class="highlight-box gold">
                <p>⚖️ <strong>Dispute Resolution:</strong> Any dispute arising from this Privacy Policy shall be
                    governed by the laws of India. If you are not satisfied with our response, you may approach the Data
                    Protection Board of India (once constituted under the DPDP Act, 2023) or any appropriate regulatory
                    authority.</p>
            </div>
        </div>

        <!-- FINAL NOTE -->
        <div class="section" style="background:linear-gradient(135deg,#8e1a2e,#c0392b);border:none">
            <div style="text-align:center;padding:10px 0">
                <div style="font-size:28px;margin-bottom:10px">🌙</div>
                <h2 style="color:#fff;margin-bottom:8px;font-family:Amiri,serif">Our Commitment to You</h2>
                <p style="color:rgba(255,255,255,0.8);max-width:600px;margin:0 auto;font-size:14px;line-height:1.7">
                    At Nikah, we believe that finding a life partner is one of the most sacred journeys in life. We
                    treat your personal information with the same trust (Amanah) that this sacred process deserves. Your
                    privacy is not just a legal obligation — it is a moral and Islamic responsibility we take seriously.
                </p>
                <p style="color:rgba(255,255,255,0.6);margin-top:16px;font-size:12px">
                    <em>"And He created from water a human being and made him [related by] lineage and marriage. And
                        ever is your Lord competent [concerning creation]." — Quran 25:54</em>
                </p>
            </div>
        </div>

    </div><!-- end container -->

    <!-- FOOTER -->
    <footer>
        <div class="footer-logo">🌙 My Nikah</div>
        <p style="color:rgba(255,255,255,0.6)">Islamic Matrimony App</p>
        <div class="footer-divider"></div>
        <div class="footer-links">
            <a href="#">Home</a>
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Cookie Policy</a>
            <a href="#">Contact Us</a>
            <a href="#">Grievance Officer</a>
        </div>
        <div class="footer-divider"></div>
        <p>© 2026 My Nikah Technologies Pvt. Ltd. All rights reserved.</p>
        <p style="margin-top:6px">Effective Date: June 22, 2025 &nbsp;|&nbsp; Last Updated: June 22, 2026</p>
        <p style="margin-top:10px;font-size:11px;color:rgba(255,255,255,0.3)">This Privacy Policy is designed for
            submission to Google Play Store, Apple App Store, and applicable regulatory bodies.</p>
    </footer>

</body>

</html>
