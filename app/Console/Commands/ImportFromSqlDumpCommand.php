<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\WorkoutCard;

class ImportFromSqlDumpCommand extends Command
{
    protected $signature = 'import:sql-dump {file_path} {--dry-run : Run without actually importing data}';
    protected $description = 'Import data from the provided SQL dump file';

    public function handle()
    {
        $filePath = $this->argument('file_path');
        $dryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Starting data import from SQL dump...');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will actually be imported');
        }

        // Leggi e processa il file SQL
        $sqlContent = file_get_contents($filePath);
        
        // Estrai i dati dalle INSERT statements
        $data = $this->extractDataFromSql($sqlContent);
        
        if ($dryRun) {
            $this->displayDataSummary($data);
            return 0;
        }

        DB::beginTransaction();
        
        try {
            $this->importAdmins($data['admins'] ?? []);
            $this->importCourses($data['courses'] ?? []);
            $this->importSections($data['sections'] ?? []);
            $this->importLessons($data['lessons'] ?? []);
            $this->importUsers($data['users'] ?? []);
            $this->importEnrollments($data['enrollments'] ?? []);
            $this->importLessonProgress($data['lesson_progress'] ?? []);
            $this->importWorkoutCards($data['workout_cards'] ?? []);

            DB::commit();
            $this->info('Data import completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function extractDataFromSql($sqlContent)
    {
        $data = [];
        
        // Pattern per estrarre le INSERT statements
        $tables = ['admins', 'courses', 'sections', 'lessons', 'users', 'enrollments', 'lesson_progress', 'workout_cards'];
        
        foreach ($tables as $table) {
            $data[$table] = $this->extractTableData($sqlContent, $table);
        }
        
        return $data;
    }

    private function extractTableData($sqlContent, $tableName)
    {
        // Cerca il pattern INSERT INTO `tableName`
        $pattern = "/INSERT INTO `{$tableName}` \(`([^`]+)`\) VALUES\s*(.*?);/s";
        
        if (!preg_match($pattern, $sqlContent, $matches)) {
            $this->warn("No data found for table: {$tableName}");
            return [];
        }
        
        $columns = explode('`, `', $matches[1]);
        $columns = array_map(function($col) { return trim($col, '`'); }, $columns);
        
        $valuesString = $matches[2];
        
        // Estrai i gruppi di valori
        preg_match_all('/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/', $valuesString, $valueMatches);
        
        $rows = [];
        foreach ($valueMatches[1] as $valueString) {
            $values = $this->parseValueString($valueString);
            if (count($values) === count($columns)) {
                $rows[] = array_combine($columns, $values);
            }
        }
        
        return $rows;
    }

    private function parseValueString($valueString)
    {
        $values = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';
        $i = 0;
        
        while ($i < strlen($valueString)) {
            $char = $valueString[$i];
            
            if (!$inQuotes && ($char === "'" || $char === '"')) {
                $inQuotes = true;
                $quoteChar = $char;
                $i++;
                continue;
            }
            
            if ($inQuotes && $char === $quoteChar) {
                // Check for escaped quote
                if ($i + 1 < strlen($valueString) && $valueString[$i + 1] === $quoteChar) {
                    $current .= $char;
                    $i += 2;
                    continue;
                } else {
                    $inQuotes = false;
                    $i++;
                    continue;
                }
            }
            
            if (!$inQuotes && $char === ',') {
                $values[] = $this->normalizeValue(trim($current));
                $current = '';
                $i++;
                continue;
            }
            
            $current .= $char;
            $i++;
        }
        
        if (trim($current) !== '') {
            $values[] = $this->normalizeValue(trim($current));
        }
        
        return $values;
    }

    private function normalizeValue($value)
    {
        if ($value === 'NULL') {
            return null;
        }
        
        // Remove quotes if present
        if ((str_starts_with($value, "'") && str_ends_with($value, "'")) ||
            (str_starts_with($value, '"') && str_ends_with($value, '"'))) {
            $value = substr($value, 1, -1);
        }
        
        // Unescape quotes
        $value = str_replace(["''", '""'], ["'", '"'], $value);
        
        return $value;
    }

    private function displayDataSummary($data)
    {
        $this->info('Data summary:');
        foreach ($data as $table => $rows) {
            $this->line("- {$table}: " . count($rows) . ' records');
        }
    }

    private function importAdmins($admins)
    {
        $this->info('Importing admins...');
        $count = 0;
        
        foreach ($admins as $adminData) {
            // Check if admin already exists
            if (Admin::where('username', $adminData['username'])->exists()) {
                continue;
            }
            
            Admin::create([
                'username' => $adminData['username'],
                'email' => $adminData['username'] . '@imported.local', // Fallback email
                'password' => $adminData['password'], // Already hashed
                'first_name' => 'Imported',
                'last_name' => 'Admin',
                'is_active' => true,
                'created_at' => $adminData['created_at'] ?? now(),
            ]);
            $count++;
        }
        
        $this->info("Imported {$count} admins");
    }

    private function importCourses($courses)
    {
        $this->info('Importing courses...');
        $count = 0;
        
        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['id' => $courseData['id']],
                [
                    'name' => $courseData['name'],
                    'description' => $courseData['description'] ?? '',
                    'image_url' => $courseData['image_url'],
                    'price' => 0, // Default price
                    'is_active' => true,
                    'created_at' => $courseData['created_at'] ?? now(),
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} courses");
    }

    private function importSections($sections)
    {
        $this->info('Importing sections...');
        $count = 0;
        
        foreach ($sections as $sectionData) {
            Section::updateOrCreate(
                ['id' => $sectionData['id']],
                [
                    'course_id' => $sectionData['course_id'],
                    'name' => $sectionData['name'],
                    'description' => '', // Default empty description
                    'section_order' => $sectionData['section_order'],
                    'is_active' => true,
                    'created_at' => $sectionData['created_at'] ?? now(),
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} sections");
    }

    private function importLessons($lessons)
    {
        $this->info('Importing lessons...');
        $count = 0;
        
        foreach ($lessons as $lessonData) {
            Lesson::updateOrCreate(
                ['id' => $lessonData['id']],
                [
                    'section_id' => $lessonData['section_id'],
                    'title' => $lessonData['title'],
                    'description' => '', // Default empty description
                    'video_url' => $lessonData['video_url'],
                    'duration_minutes' => null, // Will be calculated later if needed
                    'lesson_order' => $lessonData['lesson_order'],
                    'is_active' => true,
                    'created_at' => $lessonData['created_at'] ?? now(),
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} lessons");
    }

    private function importUsers($users)
    {
        $this->info('Importing users...');
        $count = 0;
        
        foreach ($users as $userData) {
            // Check if user already exists
            if (User::where('email', $userData['email'])->exists()) {
                continue;
            }
            
            $name = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
            if (empty($name)) {
                $name = explode('@', $userData['email'])[0];
            }
            
            User::create([
                'name' => $name,
                'email' => $userData['email'],
                'password' => $userData['password'], // Already hashed
                'first_name' => $userData['first_name'] ?? '',
                'last_name' => $userData['last_name'] ?? '',
                'phone' => $userData['phone'],
                'is_active' => $userData['is_active'] ?? true,
                'last_login' => $userData['last_login'],
                'email_verified_at' => now(), // Assume verified
                'created_at' => $userData['created_at'] ?? now(),
            ]);
            $count++;
        }
        
        $this->info("Imported {$count} users");
    }

    private function importEnrollments($enrollments)
    {
        $this->info('Importing enrollments...');
        $count = 0;
        
        foreach ($enrollments as $enrollmentData) {
            // Check if user and course exist
            if (!User::find($enrollmentData['user_id']) || !Course::find($enrollmentData['course_id'])) {
                continue;
            }
            
            Enrollment::updateOrCreate(
                [
                    'user_id' => $enrollmentData['user_id'],
                    'course_id' => $enrollmentData['course_id']
                ],
                [
                    'enrolled_at' => $enrollmentData['enrolled_at'],
                    'expires_at' => $enrollmentData['expires_at'],
                    'is_active' => $enrollmentData['is_active'] ?? true,
                    'progress_percentage' => 0, // Will be calculated from lesson progress
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} enrollments");
    }

    private function importLessonProgress($progressData)
    {
        $this->info('Importing lesson progress...');
        $count = 0;
        
        foreach ($progressData as $progress) {
            // Check if user and lesson exist
            if (!User::find($progress['user_id']) || !Lesson::find($progress['lesson_id'])) {
                continue;
            }
            
            LessonProgress::updateOrCreate(
                [
                    'user_id' => $progress['user_id'],
                    'lesson_id' => $progress['lesson_id']
                ],
                [
                    'completed' => $progress['completed'] ?? false,
                    'completed_at' => $progress['completed_at'],
                    'watch_time_seconds' => $progress['watch_time_seconds'] ?? 0,
                    'progress_percentage' => $progress['completed'] ? 100 : 0,
                    'created_at' => $progress['created_at'] ?? now(),
                    'updated_at' => $progress['updated_at'] ?? now(),
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} lesson progress records");
        
        // Update enrollment progress percentages
        $this->updateEnrollmentProgress();
    }

    private function importWorkoutCards($workoutCards)
    {
        $this->info('Importing workout cards...');
        $count = 0;
        
        foreach ($workoutCards as $cardData) {
            // Check if course exists
            if (!Course::find($cardData['course_id'])) {
                continue;
            }
            
            WorkoutCard::updateOrCreate(
                ['course_id' => $cardData['course_id']],
                [
                    'title' => $cardData['title'],
                    'content' => $cardData['content'],
                    'warmup' => $cardData['warmup'],
                    'venous_return' => $cardData['venous_return'],
                    'notes' => '', // Default empty notes
                    'is_active' => true,
                    'created_at' => $cardData['created_at'] ?? now(),
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} workout cards");
    }

    private function updateEnrollmentProgress()
    {
        $this->info('Updating enrollment progress percentages...');
        
        $enrollments = Enrollment::with(['course.lessons', 'user.lessonProgress'])->get();
        
        foreach ($enrollments as $enrollment) {
            $totalLessons = $enrollment->course->lessons->count();
            
            if ($totalLessons === 0) {
                continue;
            }
            
            $completedLessons = $enrollment->user
                ->lessonProgress()
                ->whereIn('lesson_id', $enrollment->course->lessons->pluck('id'))
                ->where('completed', true)
                ->count();
            
            $progressPercentage = ($completedLessons / $totalLessons) * 100;
            
            $enrollment->update(['progress_percentage' => round($progressPercentage, 2)]);
        }
        
        $this->info('Progress percentages updated');
    }
}