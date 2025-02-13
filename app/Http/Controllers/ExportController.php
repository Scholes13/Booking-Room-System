<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExcelExport;
use App\Services\PdfExport;
use App\Services\CsvExport;
use App\Models\YourModel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class ExportController extends Controller
{
    /**
     * Handle the export request based on format
     *
     * @param Request $request
     * @return StreamedResponse|Response
     */
    public function export(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'format' => 'required|in:excel,pdf,csv',
                'include_charts' => 'boolean',
                'report_type' => 'required|string',
                // Add other filter validations here
            ]);

            // Get data based on filters
            $data = $this->getFilteredData($request);

            // Generate filename
            $filename = $this->generateFilename($validated['format'], $validated['report_type']);

            // Export based on format
            switch ($validated['format']) {
                case 'excel':
                    return app(ExcelExport::class)->download(
                        $data, 
                        $filename,
                        $validated['include_charts'] ?? false
                    );
                
                case 'pdf':
                    return app(PdfExport::class)->download(
                        $data, 
                        $filename,
                        $validated['include_charts'] ?? false
                    );
                
                case 'csv':
                    return app(CsvExport::class)->download(
                        $data, 
                        $filename
                    );
                
                default:
                    throw new Exception('Unsupported export format');
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered data based on request parameters
     *
     * @param Request $request
     * @return Collection
     */
    private function getFilteredData(Request $request)
    {
        $query = YourModel::query();

        // Apply filters based on request parameters
        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Add more filter conditions as needed
        
        return $query->get();
    }

    /**
     * Generate filename for export
     *
     * @param string $format
     * @param string $reportType
     * @return string
     */
    private function generateFilename(string $format, string $reportType): string
    {
        $timestamp = now()->format('Ymd');
        $extension = $this->getFileExtension($format);
        
        return sprintf(
            '%s-report-%s.%s',
            str_replace('_', '-', $reportType),
            $timestamp,
            $extension
        );
    }

    /**
     * Get file extension based on format
     *
     * @param string $format
     * @return string
     */
    private function getFileExtension(string $format): string
    {
        return match ($format) {
            'excel' => 'xlsx',
            'pdf' => 'pdf',
            'csv' => 'csv',
            default => 'xlsx',
        };
    }
}