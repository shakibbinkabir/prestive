<?php
declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private function createDompdf(): Dompdf
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $tmpDir = sys_get_temp_dir();
        $options->set('tempDir', $tmpDir);
        return new Dompdf($options);
    }

    /**
     * Render application PDF (membership or trainee)
     * @param string $type 'membership'|'trainee'
     * @param array $app Application data
     * @param array $uploads Optional uploads grouped by category
     * @return string Binary PDF contents
     */
    public function renderApplication(string $type, array $app, array $uploads = []): string
    {
        $type = $type === 'trainee' ? 'trainee' : 'membership';
        // Build HTML using a simple template include
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

    public function streamDownload(string $filename, string $pdfBinary): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfBinary));
        echo $pdfBinary;
    }
}
