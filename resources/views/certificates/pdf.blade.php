<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $certificate->templateLabel() }} — {{ $certificate->recipient_name }}</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; }
        body {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Serif", "Times New Roman", serif;
            color: #2c2825;
            background: #f3eee6;
        }

        /* Full A4 landscape canvas */
        .page {
            position: relative;
            width: 841.89pt;
            height: 595.28pt;
            background: #f3eee6;
            overflow: hidden;
        }
        .outer {
            position: absolute;
            top: 14pt;
            left: 14pt;
            width: 813.89pt;
            height: 567.28pt;
            border: 2.5pt solid #7a1f2b;
            background: #fffefb;
        }
        .inner {
            position: absolute;
            top: 8pt;
            left: 8pt;
            width: 797.89pt;
            height: 551.28pt;
            border: 1.5pt solid #c9a961;
            background: #fffdf8;
        }

        /* Content block — vertically centered in the frame */
        .center-block {
            position: absolute;
            top: 55pt;
            left: 48pt;
            width: 701.89pt;
            text-align: center;
        }
        .footer-block {
            position: absolute;
            bottom: 28pt;
            left: 48pt;
            width: 701.89pt;
        }

        .brand {
            font-family: "DejaVu Sans", Helvetica, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            letter-spacing: 2.2pt;
            text-transform: uppercase;
            color: #7a1f2b;
        }
        .brand span {
            color: #6b645e;
            font-weight: normal;
        }
        .ornament {
            margin-top: 10pt;
            font-size: 9pt;
            color: #c9a961;
            letter-spacing: 3pt;
        }
        .eyebrow {
            margin-top: 14pt;
            font-family: "DejaVu Sans", Helvetica, sans-serif;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 2.8pt;
            text-transform: uppercase;
            color: #c9a961;
        }
        .ribbon { margin-top: 14pt; }
        .ribbon span {
            background: #7a1f2b;
            color: #ffffff;
            font-family: "DejaVu Sans", Helvetica, sans-serif;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 2pt;
            text-transform: uppercase;
            padding: 5pt 15pt;
        }
        .heading {
            margin-top: 10pt;
            font-size: 28pt;
            font-weight: bold;
            line-height: 1.12;
            color: #7a1f2b;
        }
        .heading-dark { color: #2c2825; }
        .presented {
            margin-top: 14pt;
            font-size: 12pt;
            font-style: italic;
            color: #6b645e;
        }
        .recipient {
            margin-top: 8pt;
            font-size: 24pt;
            font-weight: bold;
            line-height: 1.15;
            color: #2c2825;
        }
        .name-rule {
            width: 250pt;
            margin: 6pt auto 0;
            border-bottom: 1.4pt solid #c9a961;
        }
        .body {
            margin: 14pt auto 0;
            width: 92%;
            font-family: "DejaVu Sans", Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.55;
            color: #5c564f;
        }
        .body strong { color: #2c2825; font-weight: bold; }
        .verse {
            margin: 12pt auto 0;
            width: 84%;
            font-size: 10.5pt;
            font-style: italic;
            line-height: 1.4;
            color: #7a1f2b;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
        }
        .meta td {
            width: 33.33%;
            vertical-align: bottom;
            padding: 0 8pt;
            text-align: left;
        }
        .meta td.seal { text-align: center; }
        .meta-value {
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1pt solid #d4cbbd;
            padding-bottom: 4pt;
        }
        .meta-label {
            margin-top: 4pt;
            font-family: "DejaVu Sans", Helvetica, sans-serif;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 1pt;
            text-transform: uppercase;
            color: #6b645e;
        }
    </style>
</head>
<body>
@php
    $brand = settings('brand.name', 'Assemblies of God');
    $tagline = settings('brand.tagline', 'Rawalpindi');
    $template = $certificate->template;
    $purpose = $certificate->title;

    if ($template === 'completion') {
        $purpose = $purpose ?: 'the training programme';
        $heading = 'Successfully Completed';
        $presented = 'This certifies that';
        $body = 'has successfully completed <strong>'.e($purpose).'</strong> with dedication, attentiveness, and a heart to grow. We commend this achievement and pray that God continues to equip and use them for every good work.';
        $verse = '"I can do all things through Christ who strengthens me." - Philippians 4:13';
        $eyebrow = null;
        $ribbon = 'Certificate of Completion';
        $headingClass = 'heading heading-dark';
    } elseif ($template === 'participation') {
        $purpose = $purpose ?: 'our church gathering';
        $heading = 'Thank You for Taking Part';
        $presented = 'Awarded with joy to';
        $body = 'for active participation in <strong>'.e($purpose).'</strong>. Your presence, encouragement, and willing heart helped make this time meaningful for our church family. We celebrate your involvement and pray God continues to strengthen and guide you.';
        $verse = '"Let us not give up meeting together... but encouraging one another." - Hebrews 10:25';
        $eyebrow = 'Certificate of Participation';
        $ribbon = null;
        $headingClass = 'heading';
    } else {
        $purpose = $purpose ?: 'faithful service and dedication';
        $heading = 'With Sincere Gratitude';
        $presented = 'This certificate is proudly presented to';
        $body = 'in joyful recognition of <strong>'.e($purpose).'</strong>. Your willingness to serve, encourage, and walk with this church family has been a true blessing. We honour your faithfulness and give thanks to God for your life and ministry among us.';
        $verse = '"Well done, good and faithful servant." - Matthew 25:23';
        $eyebrow = 'Certificate of Appreciation';
        $ribbon = null;
        $headingClass = 'heading';
    }
@endphp

<div class="page">
    <div class="outer">
        <div class="inner">
            <div class="center-block">
                <div class="brand">
                    {{ mb_strtoupper($brand) }}@if($tagline)<span> · {{ mb_strtoupper($tagline) }}</span>@endif
                </div>
                <div class="ornament">* * *</div>

                @if($ribbon)
                    <div class="ribbon"><span>{{ $ribbon }}</span></div>
                @else
                    <div class="eyebrow">{{ $eyebrow }}</div>
                @endif

                <div class="{{ $headingClass }}">{{ $heading }}</div>
                <div class="presented">{{ $presented }}</div>
                <div class="recipient">{{ $certificate->recipient_name }}</div>
                <div class="name-rule"></div>
                <div class="body">{!! $body !!}</div>
                <div class="verse">{{ $verse }}</div>
            </div>

            <div class="footer-block">
                <table class="meta" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <div class="meta-value">{{ $certificate->issued_on?->format('F j, Y') }}</div>
                            <div class="meta-label">Date</div>
                        </td>
                        <td class="seal">
                            <svg width="56" height="56" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="28" cy="28" r="25" fill="#f3e6c2" stroke="#c9a961" stroke-width="2.2"/>
                                <circle cx="28" cy="28" r="20" fill="none" stroke="#7a1f2b" stroke-width="0.9"/>
                                <text x="28" y="33" text-anchor="middle" font-family="DejaVu Serif" font-size="12" font-weight="bold" fill="#7a1f2b">AG</text>
                            </svg>
                        </td>
                        <td>
                            <div class="meta-value">{{ $certificate->assigned_by }}</div>
                            <div class="meta-label">Assigned by</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
