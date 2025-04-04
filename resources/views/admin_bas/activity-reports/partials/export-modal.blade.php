<!-- Export Options Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="min-h-screen px-4 text-center">
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Export Options</h3>
                <button id="closeExportModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select id="export_format" class="w-full rounded-lg border-gray-300">
                        <option value="xlsx">Excel (.xlsx) - For data processing</option>
                        <option value="csv">CSV (.csv) - For spreadsheet compatibility</option>
                        <option value="pdf">PDF (.pdf) - For printing/sharing</option>
                    </select>
                </div>

                <div id="export_description" class="text-sm text-gray-600 mt-2">
                    <p class="xlsx_description">Excel format is best for data analysis and manual processing.</p>
                    <p class="csv_description hidden">CSV format is compatible with all spreadsheet applications.</p>
                    <p class="pdf_description hidden">PDF format is best for printing and sharing visually appealing reports.</p>
                </div>

                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="include_charts" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm text-gray-700">Include Charts (if available)</span>
                    </label>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button id="cancelExport" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button id="confirmExport" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-hover">
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 