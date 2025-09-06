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
        $pdfRoot = $root ? ($root . DIRECTORY_SEPARATOR . 'PDF') : null;
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
}
