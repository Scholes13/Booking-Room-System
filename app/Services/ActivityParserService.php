<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ActivityType;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ActivityParserService
{
    /**
     * Parse natural language input and extract activity data
     *
     * @param string $input
     * @return array
     */
    public function parseActivityInput(string $input): array
    {
        $result = [
            'success' => true,
            'data' => [
                'employee_name' => null,
                'employee_id' => null,
                'department_id' => null,
                'activity_type' => null,
                'start_datetime' => null,
                'end_datetime' => null,
                'description' => $input,
                'location' => null,
            ],
            'matches' => [],
            'errors' => []
        ];

        try {
            // Extract employee/PIC information
            $employeeData = $this->extractEmployee($input);
            if ($employeeData) {
                $result['data']['employee_name'] = $employeeData['name'];
                $result['data']['employee_id'] = $employeeData['id'];
                $result['data']['department_id'] = $employeeData['department_id'];
                $result['matches'][] = "Found employee: {$employeeData['name']}";
            }

            // Extract time information
            $timeData = $this->extractTime($input);
            if ($timeData) {
                $result['data']['start_datetime'] = $timeData['start'];
                $result['data']['end_datetime'] = $timeData['end'];
                $result['matches'][] = "Found time: {$timeData['start']} - {$timeData['end']}";
            }

            // Extract activity type
            $activityType = $this->extractActivityType($input);
            if ($activityType) {
                $result['data']['activity_type'] = $activityType;
                $result['matches'][] = "Found activity type: {$activityType}";
            }

            // Extract location/room
            $location = $this->extractLocation($input);
            if ($location) {
                $result['data']['location'] = $location;
                $result['matches'][] = "Found location: {$location}";
            }

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Extract employee information from input text
     *
     * @param string $input
     * @return array|null
     */
    private function extractEmployee(string $input): ?array
    {
        // Patterns to match PIC/employee names
        $patterns = [
            '/PIC\s*:\s*(.+?)(?:\s*-|$)/i',
            '/pic\s*:\s*(.+?)(?:\s*-|$)/i',
            '/oleh\s+(.+?)(?:\s*-|$)/i',
            '/by\s+(.+?)(?:\s*-|$)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                $namePattern = trim($matches[1]);
                
                // Clean up common prefixes
                $namePattern = preg_replace('/^(bu|pak|bapak|ibu|mr|mrs|ms)\.?\s+/i', '', $namePattern);
                
                // Find employee with fuzzy matching
                $employee = $this->findEmployeeByName($namePattern);
                if ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'department_id' => $employee->department_id
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Find employee by name using fuzzy matching
     *
     * @param string $namePattern
     * @return Employee|null
     */
    private function findEmployeeByName(string $namePattern): ?Employee
    {
        // First try exact match
        $employee = Employee::where('name', 'LIKE', "%{$namePattern}%")->first();
        if ($employee) {
            return $employee;
        }

        // Try matching individual words
        $words = explode(' ', $namePattern);
        foreach ($words as $word) {
            if (strlen($word) >= 3) { // Only search for words with 3+ characters
                $employee = Employee::where('name', 'LIKE', "%{$word}%")->first();
                if ($employee) {
                    return $employee;
                }
            }
        }

        return null;
    }

    /**
     * Extract time information from input text
     *
     * @param string $input
     * @return array|null
     */
    private function extractTime(string $input): ?array
    {
        // Use today's date as default
        $today = Carbon::today();

        // First check for "Selesai" pattern - single time with completion indicator
        $selesaiPatterns = [
            '/(\d{1,2})[.:](\d{2})\s*(?:WIB)?\s*-?\s*(?:selesai|finish|done|end)/i',
            '/(\d{1,2})[.:](\d{2})\s*(?:WIB)?\s*(?:selesai|finish|done|end)/i',
        ];

        foreach ($selesaiPatterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                $startHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $startMinute = $matches[2];
                
                $startDateTime = $today->copy()->setTime($startHour, $startMinute);
                // Add 1 hour for end time when "selesai" is mentioned
                $endDateTime = $startDateTime->copy()->addHour();

                return [
                    'start' => $startDateTime->format('Y-m-d\TH:i'),
                    'end' => $endDateTime->format('Y-m-d\TH:i')
                ];
            }
        }

        // Patterns to match time ranges (existing functionality)
        $rangePatterns = [
            '/(\d{1,2})[.:](\d{2})\s*-\s*(\d{1,2})[.:](\d{2})/i',
            '/(\d{1,2})[.:](\d{2})\s*sampai\s*(\d{1,2})[.:](\d{2})/i',
            '/(\d{1,2})[.:](\d{2})\s*hingga\s*(\d{1,2})[.:](\d{2})/i',
            '/jam\s*(\d{1,2})[.:](\d{2})\s*-\s*(\d{1,2})[.:](\d{2})/i',
        ];

        foreach ($rangePatterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                $startHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $startMinute = $matches[2];
                $endHour = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
                $endMinute = $matches[4];
                
                $startDateTime = $today->copy()->setTime($startHour, $startMinute);
                $endDateTime = $today->copy()->setTime($endHour, $endMinute);

                return [
                    'start' => $startDateTime->format('Y-m-d\TH:i'),
                    'end' => $endDateTime->format('Y-m-d\TH:i')
                ];
            }
        }

        // Pattern for single time without range (fallback - add 1 hour)
        $singleTimePatterns = [
            '/(\d{1,2})[.:](\d{2})\s*(?:WIB)?/i',
            '/jam\s*(\d{1,2})[.:](\d{2})/i',
        ];

        foreach ($singleTimePatterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                $startHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $startMinute = $matches[2];
                
                $startDateTime = $today->copy()->setTime($startHour, $startMinute);
                // Default to 1 hour duration for single time
                $endDateTime = $startDateTime->copy()->addHour();

                return [
                    'start' => $startDateTime->format('Y-m-d\TH:i'),
                    'end' => $endDateTime->format('Y-m-d\TH:i')
                ];
            }
        }

        return null;
    }

    /**
     * Extract activity type from input text
     *
     * @param string $input
     * @return string|null
     */
    private function extractActivityType(string $input): ?string
    {
        // Get all active activity types from database
        $activityTypes = ActivityType::where('is_active', true)->pluck('name')->toArray();
        
        // Convert input to lowercase for matching
        $inputLower = strtolower($input);
        
        // Check for exact matches first
        foreach ($activityTypes as $type) {
            if (stripos($inputLower, strtolower($type)) !== false) {
                return $type;
            }
        }

        // Check for common activity type keywords
        $keywords = [
            'meeting' => ['meeting', 'rapat', 'pertemuan'],
            'training' => ['training', 'pelatihan', 'workshop'],
            'presentation' => ['presentation', 'presentasi', 'demo'],
            'interview' => ['interview', 'wawancara'],
            'conference' => ['conference', 'konferensi', 'seminar'],
            'call' => ['call', 'telepon', 'video call'],
        ];

        foreach ($keywords as $activityType => $keywordList) {
            foreach ($keywordList as $keyword) {
                if (stripos($inputLower, $keyword) !== false) {
                    // Check if this activity type exists in database
                    $dbType = ActivityType::where('name', 'LIKE', "%{$activityType}%")
                                         ->where('is_active', true)
                                         ->first();
                    if ($dbType) {
                        return $dbType->name;
                    }
                    // Return capitalized keyword if not found in database
                    return ucfirst($activityType);
                }
            }
        }

        return null;
    }

    /**
     * Extract location/room information from input text
     *
     * @param string $input
     * @return string|null
     */
    private function extractLocation(string $input): ?string
    {
        // Patterns to match location/room
        $patterns = [
            '/ruang\s+(\w+)/i',
            '/room\s+(\w+)/i',
            '/di\s+ruang\s+(\w+)/i',
            '/di\s+(\w+)/i',
            '/lokasi\s*:\s*(.+?)(?:\s*-|$)/i',
            '/tempat\s*:\s*(.+?)(?:\s*-|$)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Get parsing statistics for debugging
     *
     * @param string $input
     * @return array
     */
    public function getParsingStats(string $input): array
    {
        return [
            'input_length' => strlen($input),
            'word_count' => str_word_count($input),
            'has_time_pattern' => (bool) preg_match('/\d{1,2}[.:]\d{2}/', $input),
            'has_pic_pattern' => (bool) preg_match('/pic\s*:/i', $input),
            'has_location_pattern' => (bool) preg_match('/ruang\s+\w+/i', $input),
            'detected_keywords' => $this->detectKeywords($input),
        ];
    }

    /**
     * Detect keywords in input for debugging
     *
     * @param string $input
     * @return array
     */
    private function detectKeywords(string $input): array
    {
        $keywords = ['meeting', 'rapat', 'training', 'pelatihan', 'pic', 'ruang', 'jam'];
        $detected = [];
        
        foreach ($keywords as $keyword) {
            if (stripos($input, $keyword) !== false) {
                $detected[] = $keyword;
            }
        }
        
        return $detected;
    }
}