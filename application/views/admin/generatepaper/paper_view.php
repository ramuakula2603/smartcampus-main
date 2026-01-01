<?php
// Expected variables:
// $school_name, $school_address, $logo_url (optional), $receipt_header_url (optional)
// $paper_html: already formatted HTML for the question paper body (preferred)
// $paper_text: plain text fallback if $paper_html is not provided

$school_name        = isset($school_name) && $school_name !== '' ? $school_name : 'AMARAVATHI JUNIOR COLLEGE';
$school_address     = isset($school_address) ? $school_address : '';
$logo_url           = isset($logo_url) ? $logo_url : '';
$receipt_header_url = isset($receipt_header_url) ? $receipt_header_url : '';

// Helper: build structured HTML from plain/markdown-like text
if (!function_exists('build_question_paper_html')) {
    function build_question_paper_html($text)
    {
        if ($text === '' || $text === null) {
            return '<p>No question paper content available.</p>';
        }

        $lines  = preg_split("/\r\n|\n|\r/", $text);
        $parts  = [];
        $hasTitle = false;

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                continue;
            }

            // Skip generic AI intro lines such as:
            // "Here is/Here's a ... question paper ..." or
            // "Based on the retrieved material, here is a 50-mark question paper ..." or
            // "Here’s a 50 marks question paper including both objective and subjective questions based on the content retrieved from your pgvector store ..."
            $lower = strtolower($line);
            $normalized = str_replace(["'", '"'], '', $lower); // remove quotes for matching

            $isHereIntro = (
                strpos($normalized, 'here is a') === 0 ||
                strpos($normalized, 'here is an') === 0 ||
                strpos($normalized, 'heres a') === 0 ||
                strpos($normalized, 'heres an') === 0
            );

            $isBasedOnIntro = (
                strpos($normalized, 'based on the retrieved material') === 0 ||
                (strpos($normalized, 'based on') === 0 && strpos($normalized, 'question paper') !== false)
            );

            $containsGenericIntro = (
                (strpos($normalized, 'question paper') !== false &&
                 (
                     strpos($normalized, 'subjective and objective questions') !== false ||
                     strpos($normalized, 'objective and subjective questions') !== false ||
                     strpos($normalized, 'including both objective and subjective questions') !== false
                 )
                ) ||
                strpos($normalized, 'pgvector store') !== false
            );

            if ($isHereIntro || $isBasedOnIntro || $containsGenericIntro) {
                continue;
            }

            // Strip leading markdown horizontal rules
            if ($line === '---' || $line === '***') {
                continue;
            }

            // Handle markdown headings: ###, #### etc
            if (preg_match('/^(#{2,6})\s*(.+)$/', $line, $m)) {
                $hashes = $m[1];
                $textPart = $m[2];

                // Convert basic bold **text** to <strong>
                $textPart = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1<\/strong>', $textPart);
                $escaped  = htmlspecialchars($textPart, ENT_QUOTES, 'UTF-8');
                $escaped  = str_replace(['&lt;strong&gt;', '&lt;\/strong&gt;'], ['<strong>', '</strong>'], $escaped);

                if (!$hasTitle) {
                    $parts[] = '<h2 class="exam-main-title">' . $escaped . '</h2>';
                    $hasTitle = true;
                } else {
                    // Section headings
                    $parts[] = '<h3 class="section-title">' . $escaped . '</h3>';
                }
                continue;
            }

            // First non-empty non-intro line becomes main title if we don't have one
            if (!$hasTitle) {
                $title = $line;
                $title = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1<\/strong>', $title);
                $escaped  = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                $escaped  = str_replace(['&lt;strong&gt;', '&lt;\/strong&gt;'], ['<strong>', '</strong>'], $escaped);
                $parts[]  = '<h2 class="exam-main-title">' . $escaped . '</h2>';
                $hasTitle = true;
                continue;
            }

            // Section headings like "Section A: Objective Questions (20 Marks)"
            if (stripos($line, 'section ') === 0) {
                $sec = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1<\/strong>', $line);
                $escaped  = htmlspecialchars($sec, ENT_QUOTES, 'UTF-8');
                $escaped  = str_replace(['&lt;strong&gt;', '&lt;\/strong&gt;'], ['<strong>', '</strong>'], $escaped);
                $parts[] = '<h3 class="section-title">' . $escaped . '</h3>';
                continue;
            }

            // Normal paragraph line, allow basic bold
            $para = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1<\/strong>', $line);
            $escaped  = htmlspecialchars($para, ENT_QUOTES, 'UTF-8');
            $escaped  = str_replace(['&lt;strong&gt;', '&lt;\/strong&gt;'], ['<strong>', '</strong>'], $escaped);
            $parts[]  = '<p>' . $escaped . '</p>';
        }

        if (empty($parts)) {
            return '<p>No question paper content available.</p>';
        }

        return implode("\n", $parts);
    }
}

// Build body HTML
if (!empty($paper_html)) {
    $body_html = $paper_html; // assume already-safe HTML from backend/AI
} elseif (!empty($paper_text)) {
    $body_html = build_question_paper_html($paper_text);
} else {
    $body_html = '<p>No question paper content available.</p>';
}
?>

<div class="content-wrapper">
    <section class="content-header" style="display:none;">
        <h1><i class="fa fa-file-text-o"></i> Question Paper Preview</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="border:none; box-shadow:none;">
                    <div class="box-header with-border" style="display:none;"></div>

                    <div class="box-body" style="background:#f8f9fa;">
                        <div id="paperContainer" style="background:#ffffff; padding:22px 24px 24px 24px; margin:0 auto; max-width:800px; box-shadow:0 0 10px rgba(0,0,0,0.06);">
                            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                                <tr>
                                    <td style="width:20%;"></td>
                                    <td style="width:60%; text-align:center; vertical-align:middle;">
                                        <?php if (!empty($receipt_header_url)) : ?>
                                            <div style="height:70px; width:100%; overflow:hidden; margin:0 auto 6px auto;">
                                                <img src="<?php echo htmlspecialchars($receipt_header_url, ENT_QUOTES, 'UTF-8'); ?>" alt="College Header" style="width:100%; min-height:70px; display:block; object-fit:cover; object-position:top center;">
                                            </div>
                                        <?php elseif (!empty($logo_url)) : ?>
                                            <img src="<?php echo htmlspecialchars($logo_url, ENT_QUOTES, 'UTF-8'); ?>" alt="College Logo" style="max-height:80px; max-width:100%; display:block; margin:0 auto 6px auto;">
                                        <?php else : ?>
                                            <div style="font-size:20px; font-weight:bold; text-transform:uppercase;">&nbsp;<?php echo htmlspecialchars($school_name, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php if (!empty($school_address)) : ?>
                                                <div style="font-size:11px; margin-top:4px;">&nbsp;<?php echo htmlspecialchars($school_address, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <div style="font-size:18px; font-weight:bold; margin-top:4px; letter-spacing:0.5px;">QUESTION PAPER</div>
                                    </td>
                                    <td style="width:20%;"></td>
                                </tr>
                            </table>

                            <hr style="border-top:2px solid #000; margin:6px 0 18px 0;" />

                            <div id="paperBody" style="font-size:13px; line-height:1.7; color:#000;">
                                <?php echo $body_html; ?>
                            </div>
                        </div>

                        <div style="margin-top:15px; text-align:right; max-width:800px; margin-left:auto; margin-right:auto;">
                            <button type="button" class="btn btn-default btn-sm" id="previewBtn" title="Open preview in new window">
                                <i class="fa fa-eye"></i> Preview
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadHtmlBtn" title="Download HTML template">
                                <i class="fa fa-download"></i> HTML
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="downloadPdfBtn" title="Download PDF">
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<style type="text/css">
    .exam-main-title {
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        margin: 10px 0 8px 0;
        text-transform: uppercase;
    }
    .section-title {
        font-size: 14px;
        font-weight: 600;
        margin: 16px 0 6px 0;
        text-align: left;
    }
</style>

<script type="text/javascript">
(function() {
    var htmlBtn    = document.getElementById('downloadHtmlBtn');
    var pdfBtn     = document.getElementById('downloadPdfBtn');
    var previewBtn = document.getElementById('previewBtn');
    var container  = document.getElementById('paperContainer');

    if (!container) return;

    function buildDocHtml() {
        return '<!DOCTYPE html>' +
            '<html><head><meta charset="utf-8"><title>Question Paper</title>' +
            '<style>body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:20px;background:#f8f9fa;}#paperContainer{background:#fff;padding:20px;margin:0 auto;max-width:800px;box-shadow:0 0 10px rgba(0,0,0,0.05);}#paperBody{font-size:13px;line-height:1.7;color:#000;}.exam-main-title{text-align:center;font-size:18px;font-weight:700;margin:10px 0 8px 0;text-transform:uppercase;}.section-title{font-size:14px;font-weight:600;margin:16px 0 6px 0;text-align:left;}</style>' +
            '</head><body>' + container.outerHTML + '</body></html>';
    }

    if (htmlBtn) {
        htmlBtn.addEventListener('click', function () {
            var docHtml = buildDocHtml();
            var blob = new Blob([docHtml], { type: 'text/html' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'question_paper.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    }

    if (previewBtn) {
        previewBtn.addEventListener('click', function () {
            var docHtml = buildDocHtml();
            var win = window.open('', '_blank');
            if (win) {
                win.document.open();
                win.document.write(docHtml);
                win.document.close();
            }
        });
    }

    if (pdfBtn) {
        pdfBtn.addEventListener('click', function () {
            function generatePDF() {
                var opt = {
                    margin:       [10, 10, 15, 10], // top, left, bottom, right (mm)
                    filename:     'question_paper.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  {
                        scale: 2,
                        useCORS: true,
                        allowTaint: true,
                        scrollY: 0,
                        logging: false,
                        windowWidth: container.scrollWidth
                    },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                // Render directly from the styled question paper container so header and body match HTML exactly
                html2pdf().set(opt).from(container).save();
            }

            if (typeof html2pdf === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                script.onload = generatePDF;
                document.head.appendChild(script);
            } else {
                generatePDF();
            }
        });
    }
})();
</script>
