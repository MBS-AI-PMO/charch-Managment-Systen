<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>&nbsp;</title>
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            background: #e8e2d8;
            font-family: system-ui, sans-serif;
            color: #2c2825;
        }
        .cert-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.65rem;
            padding: 0.85rem 1rem 1rem;
            background: rgba(255,255,255,0.97);
            border-bottom: 1px solid #e8e2d8;
        }
        .cert-toolbar-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .cert-toolbar a,
        .cert-toolbar button {
            appearance: none;
            border: 1px solid #d8d0c4;
            background: #fff;
            color: #2c2825;
            border-radius: 0.5rem;
            padding: 0.45rem 0.9rem;
            font: 600 0.85rem/1.2 system-ui, sans-serif;
            cursor: pointer;
            text-decoration: none;
        }
        .cert-toolbar .primary {
            background: #7a1f2b;
            border-color: #7a1f2b;
            color: #fff;
        }
        .cert-stage {
            min-height: calc(100vh - 7rem);
            padding: 1.5rem 1rem 2.5rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* Exact A4 landscape, no printer chrome margins from CSS side */
        @page {
            size: 297mm 210mm;
            margin: 0;
        }

        @media print {
            @page {
                size: 297mm 210mm;
                margin: 0;
            }

            html, body {
                width: 297mm !important;
                height: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .cert-stage {
                position: static !important;
                min-height: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            /* Pin certificate dead-center / full page — nothing else visible */
            .certificate {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 297mm !important;
                height: 210mm !important;
                max-width: none !important;
                max-height: none !important;
                margin: 0 !important;
                padding: 5mm !important;
                box-shadow: none !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }
        }
    </style>
</head>
<body>
    <div class="cert-toolbar no-print">
        <div class="cert-toolbar-actions">
            <button type="button" class="primary" id="cert-print-btn">Print certificate</button>
            @if(!empty($pdfUrl))
                <a href="{{ $pdfUrl }}">Export PDF</a>
            @endif
            @if(!empty($backUrl))
                <a href="{{ $backUrl }}">Back to details</a>
            @endif
            @if(!empty($editUrl))
                <a href="{{ $editUrl }}">Edit</a>
            @endif
        </div>
    </div>

    <div class="cert-stage" id="cert-stage">
        @include($certificate->templateView(), ['certificate' => $certificate])
    </div>

    <script>
        (function () {
            function runPrint() {
                var prev = document.title;
                document.title = ' ';
                window.print();
                setTimeout(function () { document.title = prev; }, 500);
            }

            var btn = document.getElementById('cert-print-btn');
            if (btn) btn.addEventListener('click', runPrint);

            window.addEventListener('beforeprint', function () {
                document.title = ' ';
            });

            @if(!empty($autoPrint))
            window.addEventListener('load', function () {
                setTimeout(runPrint, 450);
            });
            @endif
        })();
    </script>
</body>
</html>
