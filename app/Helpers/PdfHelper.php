<?php

namespace App\Helpers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfHelper
{
    /**
     * Create optimized DomPDF instance for VPS server
     * 
     * @param array $customOptions Custom options to override defaults
     * @return Dompdf
     */
    public static function createInstance($customOptions = [])
    {
        // Set memory limit for PDF generation
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 5 minutes

        // Default options optimized for VPS
        $defaultOptions = [
            'isRemoteEnabled' => false, // Disable remote resources for security and performance
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false, // Disable PHP for security
            'isFontSubsettingEnabled' => true,
            'defaultMediaType' => 'print',
            'defaultPaperSize' => 'A4',
            'defaultFont' => 'DejaVu Sans', // Better font support
            'fontHeightRatio' => 1.1,
            'isJavascriptEnabled' => false,
            'debugPng' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ];

        // Merge custom options
        $options = array_merge($defaultOptions, $customOptions);

        $dompdfOptions = new Options();

        // Apply all options
        foreach ($options as $key => $value) {
            $dompdfOptions->set($key, $value);
        }

        // Set font directory to writable location
        $fontDir = WRITEPATH . 'dompdf_fonts';
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }
        $dompdfOptions->setFontDir($fontDir);
        $dompdfOptions->setFontCache($fontDir);

        // Set temporary directory
        $tempDir = WRITEPATH . 'dompdf_temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $dompdfOptions->setTempDir($tempDir);

        return new Dompdf($dompdfOptions);
    }

    /**
     * Generate PDF with error handling
     * 
     * @param string $html HTML content
     * @param string $filename Output filename
     * @param string $paper Paper size (A4, A3, etc.)
     * @param string $orientation Portrait or landscape
     * @param bool $download Force download or inline display
     * @param array $customOptions Custom DomPDF options
     * @return mixed
     */
    public static function generatePdf($html, $filename, $paper = 'A4', $orientation = 'portrait', $download = false, $customOptions = [])
    {
        try {
            // Clean the HTML to prevent issues
            $html = self::cleanHtml($html);

            $dompdf = self::createInstance($customOptions);
            $dompdf->loadHtml($html);
            $dompdf->setPaper($paper, $orientation);
            $dompdf->render();

            // Clean filename
            $filename = self::cleanFilename($filename);

            $output = $dompdf->output();

            // Clear memory
            unset($dompdf);

            return [
                'success' => true,
                'output' => $output,
                'filename' => $filename,
                'size' => strlen($output)
            ];
        } catch (\Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'fallback_html' => self::createErrorPage($e->getMessage())
            ];
        }
    }

    /**
     * Stream PDF to browser with proper headers
     * 
     * @param array $pdfResult Result from generatePdf
     * @param bool $download Force download
     * @return \CodeIgniter\HTTP\Response
     */
    public static function streamPdf($pdfResult, $download = false)
    {
        $response = service('response');

        if (!$pdfResult['success']) {
            return $response
                ->setStatusCode(500)
                ->setContentType('text/html')
                ->setBody($pdfResult['fallback_html']);
        }

        $disposition = $download ? 'attachment' : 'inline';

        return $response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $pdfResult['filename'] . '"')
            ->setHeader('Content-Length', $pdfResult['size'])
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($pdfResult['output']);
    }

    /**
     * Clean HTML content to prevent PDF generation issues
     * 
     * @param string $html
     * @return string
     */
    private static function cleanHtml($html)
    {
        // Remove potential problematic elements
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<link[^>]*>/i', '', $html);

        // Convert relative URLs to absolute for images (if needed)
        $baseUrl = base_url();
        $html = preg_replace('/src="(?!http|data:)([^"]*)"/', 'src="' . $baseUrl . '$1"', $html);

        // Ensure proper UTF-8 encoding
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        return $html;
    }

    /**
     * Clean filename for safe download
     * 
     * @param string $filename
     * @return string
     */
    private static function cleanFilename($filename)
    {
        // Remove or replace unsafe characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Ensure .pdf extension
        if (!str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        return $filename;
    }

    /**
     * Create error page HTML
     * 
     * @param string $error
     * @return string
     */
    private static function createErrorPage($error)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error Generating PDF</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .error { color: #d32f2f; background: #ffebee; padding: 15px; border-radius: 4px; }
                .retry { margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>Error Generating PDF</h1>
            <div class="error">
                <strong>Error:</strong> ' . htmlspecialchars($error) . '
            </div>
            <div class="retry">
                <p>Silakan coba lagi atau hubungi administrator jika masalah berlanjut.</p>
                <button onclick="history.back()">Kembali</button>
            </div>
        </body>
        </html>';
    }
}
