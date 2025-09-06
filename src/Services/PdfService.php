<?php
declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private function createDompdf(?string $chroot = null, ?string $basePath = null): Dompdf
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $tmpDir = sys_get_temp_dir();
        $options->set('tempDir', $tmpDir);
        if ($chroot && is_dir($chroot)) {
            // Restrict Dompdf to this directory so relative assets resolve safely
            if (method_exists($options, 'setChroot')) {
                $options->setChroot($chroot);
            } else {
                $options->set('chroot', $chroot);
            }
        }
        $dompdf = new Dompdf($options);
        if ($basePath) {
            // Helps css/img relative URLs
            if (method_exists($dompdf, 'setBasePath')) {
                $dompdf->setBasePath($basePath);
            }
        }
        return $dompdf;
    }

    /**
     * Render application PDF (membership or trainee)
     * @param string $type 'membership'|'trainee'
     * @param array $app Application data
     * @param array $uploads Optional uploads grouped by category
     * @return string Binary PDF contents
     */
    public function renderApplication(string $type, array $app, array $uploads = [], string $template = 'default'): string
    {
        $type = $type === 'trainee' ? 'trainee' : 'membership';
        $template = strtolower($template ?: 'default');

        if ($template === 'default') {
            // Build HTML using existing PHP views
            ob_start();
            $title = ucfirst($type) . ' Application';
            $admission = $app['admission_id'] ?? '';
            $viewFile = __DIR__ . '/../Views/pdf/' . $type . '.php';
            if (!file_exists($viewFile)) {
                throw new \RuntimeException('PDF view missing: ' . $viewFile);
            }
            $data = ['title' => $title, 'app' => $app, 'uploads' => $uploads];
            extract($data);
            include $viewFile;
            $html = ob_get_clean();

            $dompdf = $this->createDompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return $dompdf->output();
        }

        // Static HTML templates under /PDF
        [$html, $baseDir] = $this->loadStaticTemplate($template, $type, $app);
        // Auto-fill ARGC template with membership data
        if ($template === 'argc' && $type === 'membership') {
            $html = $this->fillArgcTemplate($html, $app);
        }
    $dompdf = $this->createDompdf($baseDir, $baseDir);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }

    public function streamDownload(string $filename, string $pdfBinary): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfBinary));
        echo $pdfBinary;
    }

    /**
     * Locate and load a static HTML template from /PDF directory.
     * @return array{0:string,1:string} [html, baseDir]
     */
    private function loadStaticTemplate(string $template, string $type, array $app): array
    {
        $root = realpath(__DIR__ . '/../../');
        $envDir = defined('PDF_TEMPLATES_DIR') ? (string)PDF_TEMPLATES_DIR : '';
        $pdfRoot = null;
        if ($envDir) {
            $pdfRoot = realpath($envDir) ?: $envDir; // allow absolute or relative
            if ($root && !preg_match('/^([A-Za-z]:\\\\|\\/|\w+:)/', $envDir)) {
                // relative: resolve against project root
                $candidate = $root . DIRECTORY_SEPARATOR . $envDir;
                $pdfRoot = realpath($candidate) ?: $candidate;
            }
        } else {
            $pdfRoot = $root ? ($root . DIRECTORY_SEPARATOR . 'PDF') : null;
        }
        if (!$pdfRoot || !is_dir($pdfRoot)) {
            throw new \RuntimeException('PDF templates directory not found');
        }

        $dir = null;
        if ($template === 'argc') {
            $dir = $pdfRoot . DIRECTORY_SEPARATOR . 'argc';
        } elseif ($template === 'trainee') {
            // Choose junior/senior if available
            $isSenior = ($app['trainee_type'] ?? '') === 'senior';
            $dir = $pdfRoot . DIRECTORY_SEPARATOR . ($isSenior ? 'trainee_senior' : 'trainee_junior');
        } else {
            // Fallback to default PHP view if unknown
            throw new \InvalidArgumentException('Unknown template: ' . $template);
        }
        $index = $dir . DIRECTORY_SEPARATOR . 'index.html';
        if (!is_file($index)) {
            throw new \RuntimeException('Template index not found: ' . $index);
        }
        $html = file_get_contents($index);
        if ($html === false) { throw new \RuntimeException('Failed to read template: ' . $index); }

    return [$html, $dir];
    }

    private function fsPathToFileUrl(string $path): string
    {
        $path = rtrim($path, "\\/");
        $path = str_replace('\\', '/', $path);
        // Ensure file URI like file:///D:/path
        if (preg_match('/^[A-Za-z]:\//', $path)) {
            return 'file:///' . $path . '/';
        }
        return 'file://' . (strpos($path, '/') === 0 ? '' : '/') . $path . '/';
    }

    private function fillArgcTemplate(string $html, array $app): string
    {
        // Basic mapping for simple two-cell rows (label -> app field)
        $map = [
            'nameincapitalletter' => fn() => $app['full_name'] ?? '',
            'dateofbirth' => fn() => $app['dob'] ?? '',
            'gender' => fn() => $app['gender'] ?? '',
            'fathersname' => fn() => $app['father_name'] ?? '',
            'mothersname' => fn() => $app['mother_name'] ?? '',
            'nationality' => fn() => $app['nationality'] ?? '',
            'nidno' => fn() => $app['nid_no'] ?? '',
            'passportno' => fn() => $app['passport_no'] ?? '',
            'permanentaddress' => fn() => $app['address_permanent'] ?? '',
            'presentaddress' => fn() => $app['address_present'] ?? '',
            'cellnoandemailid' => fn() => trim(($app['mobile'] ?? '') . (empty($app['email']) ? '' : (', ' . $app['email']))),
            'bloodgroup' => fn() => $app['blood_group'] ?? '',
            'emergencycontactnamerelationship' => fn() => trim(($app['emergency_name'] ?? '') . (empty($app['emergency_relationship']) ? '' : (' (' . $app['emergency_relationship'] . ')'))),
            'emergencycontactnumber' => fn() => $app['emergency_phone'] ?? '',
        ];

        $doc = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        // Preserve original meta charset; DOMDocument may add doctype; acceptable for rendering
        $doc->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $xpath = new \DOMXPath($doc);
        // Fill simple rows where there are 3 tds (no colspan in value cell)
        foreach ($xpath->query('//tr') as $tr) {
            if (!$tr instanceof \DOMElement) continue;
            $tds = [];
            foreach ($tr->getElementsByTagName('td') as $td) { $tds[] = $td; }
            if (count($tds) === 3) {
                [$tdNum, $tdLabel, $tdValue] = $tds;
                $labelText = $this->normalizeLabel($tdLabel->textContent ?? '');
                if (isset($map[$labelText])) {
                    $val = (string)($map[$labelText])();
                    if ($val !== '') {
                        // Put into first <p> or replace content entirely
                        $p = null;
                        foreach ($tdValue->getElementsByTagName('p') as $pp) { $p = $pp; break; }
                        if ($p) {
                            while ($p->firstChild) { $p->removeChild($p->firstChild); }
                            $p->appendChild($doc->createTextNode($val));
                        } else {
                            while ($tdValue->firstChild) { $tdValue->removeChild($tdValue->firstChild); }
                            $tdValue->appendChild($doc->createTextNode($val));
                        }
                    }
                }
            }
        }

        // Occupational details row (combined cell with inner prompts)
        foreach ($xpath->query('//p[contains(translate(normalize-space(.),"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"occupational details")]') as $p) {
            $td = $p->parentNode; while ($td && $td->nodeName !== 'td') { $td = $td->parentNode; }
            if ($td instanceof \DOMElement) {
                $outer = $this->outerHTML($td, $doc);
                $org = htmlspecialchars((string)($app['organization'] ?? ''), ENT_QUOTES, 'UTF-8');
                $des = htmlspecialchars((string)($app['designation'] ?? ''), ENT_QUOTES, 'UTF-8');
                $replaced = $outer;
                if ($org !== '') { $replaced = preg_replace('/(Name\s*of\s*organization\s*:)/i', '$1 ' . $org, $replaced, 1); }
                if ($des !== '') { $replaced = preg_replace('/(Designation\s*:)/i', '$1 ' . $des, $replaced, 1); }
                if ($replaced && $replaced !== $outer) {
                    $this->replaceOuterHTML($td, $replaced, $doc);
                }
            }
        }

        // Family details block (spouse, num children, names)
        foreach ($xpath->query('//p[contains(translate(normalize-space(.),"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"family details")]') as $p) {
            $td = $p->parentNode; while ($td && $td->nodeName !== 'td') { $td = $td->parentNode; }
            if ($td instanceof \DOMElement) {
                $outer = $this->outerHTML($td, $doc);
                $sp = htmlspecialchars((string)($app['spouse_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                $num = htmlspecialchars((string)($app['num_children'] ?? ''), ENT_QUOTES, 'UTF-8');
                $names = htmlspecialchars((string)($app['children_names'] ?? ''), ENT_QUOTES, 'UTF-8');
                $replaced = $outer;
                if ($sp !== '') { $replaced = preg_replace('/(Name\s*of\s*Spouse\s*:)/i', '$1 ' . $sp, $replaced, 1); }
                if ($num !== '') { $replaced = preg_replace('/(Number\s*of\s*children\s*:)/i', '$1 ' . $num, $replaced, 1); }
                if ($names !== '') { $replaced = preg_replace('/(Names\s*of\s*children\s*:)/i', '$1 ' . $names, $replaced, 1); }
                if ($replaced && $replaced !== $outer) {
                    $this->replaceOuterHTML($td, $replaced, $doc);
                }
            }
        }

        // Proposer and Seconder blocks (third td content lines)
        foreach ($xpath->query('//tr') as $tr) {
            $tds = [];
            foreach ($tr->getElementsByTagName('td') as $td) { $tds[] = $td; }
            if (count($tds) === 3) {
                $label = $this->normalizeLabel($tds[1]->textContent ?? '');
                if (strpos($label, 'proposer') !== false) {
                    $outer = $this->outerHTML($tds[2], $doc);
                    $name = htmlspecialchars((string)($app['proposer_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $no = htmlspecialchars((string)($app['proposer_membership_no'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $replaced = $outer;
                    if ($name !== '') { $replaced = preg_replace('/(Name\s*:)/i', '$1 ' . $name, $replaced, 1); }
                    if ($no !== '') { $replaced = preg_replace('/(ARGC\s*membership\s*number\s*:)/i', '$1 ' . $no, $replaced, 1); }
                    if ($replaced && $replaced !== $outer) { $this->replaceOuterHTML($tds[2], $replaced, $doc); }
                }
                if (strpos($label, 'seconder') !== false) {
                    $outer = $this->outerHTML($tds[2], $doc);
                    $name = htmlspecialchars((string)($app['seconder_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $no = htmlspecialchars((string)($app['seconder_membership_no'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $replaced = $outer;
                    if ($name !== '') { $replaced = preg_replace('/(Name\s*:)/i', '$1 ' . $name, $replaced, 1); }
                    if ($no !== '') { $replaced = preg_replace('/(ARGC\s*membership\s*number\s*:)/i', '$1 ' . $no, $replaced, 1); }
                    if ($replaced && $replaced !== $outer) { $this->replaceOuterHTML($tds[2], $replaced, $doc); }
                }
            }
        }

        // Declaration paragraph: insert applicant name after 'I'
        $fullName = trim((string)($app['full_name'] ?? ''));
        if ($fullName !== '') {
            foreach ($xpath->query('//p[contains(translate(normalize-space(.),"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"the undersigned hereby")]') as $p) {
                // Replace 'I ...,' up to the first comma
                $htmlP = $this->outerHTML($p, $doc);
                $newHtmlP = preg_replace('/I[^,]*,/', 'I ' . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ', ', $htmlP, 1);
                if ($newHtmlP && $newHtmlP !== $htmlP) {
                    $this->replaceOuterHTML($p, $newHtmlP, $doc);
                }
            }
        }

        return $doc->saveHTML();
    }

    private function normalizeLabel(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/','', $text);
        return $text ?? '';
    }

    private function outerHTML(\DOMElement $el, \DOMDocument $doc): string
    {
        return $doc->saveHTML($el) ?: '';
    }

    private function replaceOuterHTML(\DOMElement $el, string $newOuterHtml, \DOMDocument $doc): void
    {
        // Replace element by parsing new HTML snippet and importing first element back
        $tmp = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $tmp->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $newOuterHtml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $body = $tmp->getElementsByTagName('body')->item(0);
        if ($body && $body->firstChild) {
            $newNode = $doc->importNode($body->firstChild, true);
            $el->parentNode->replaceChild($newNode, $el);
        }
    }
}
