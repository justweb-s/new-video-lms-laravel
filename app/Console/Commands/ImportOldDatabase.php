<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;

class ImportOldDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:old-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from the old SQL dump file';

    /**
     * Execute the console command.
     */
    private function importUsers($sqlFilePath)
    {
        $this->info('Importing users...');
        $file = fopen($sqlFilePath, 'r');

        if ($file) {
            $inserted = 0;
            $capturing = false;
            $buffer = '';
            while (($line = fgets($file)) !== false) {
                if (!$capturing && strpos($line, 'INSERT INTO `users`') !== false) {
                    $capturing = true;
                    $buffer = $line;
                    continue;
                }

                if ($capturing) {
                    $buffer .= $line;
                    if (strpos($line, ');') !== false) {
                        // Parse the full INSERT statement (multiline)
                        if (preg_match('/INSERT INTO `users`.*?VALUES\s*\((.*)\);/si', $buffer, $match)) {
                            $valuesString = $match[1];
                            // Split records by '), (' safely
                            $records = preg_split('/\),\s*\(/', $valuesString);
                            foreach ($records as $record) {
                                $record = trim($record, "() \r\n\t");
                                $values = str_getcsv($record, ',', "'");
                                $values = array_map(function($v) {
                                    return $v === 'NULL' ? null : $v;
                                }, $values);

                                try {
                                    User::unguarded(function () use ($values, &$inserted) {
                                        User::create([
                                            'id' => $values[0],
                                            'email' => $values[1],
                                            'password' => $values[2],
                                            'name' => $values[3] . ' ' . $values[4],
                                            'first_name' => $values[3] ?? null,
                                            'last_name' => $values[4] ?? null,
                                            'phone' => $values[5] ?? null,
                                            'is_active' => $values[6] ?? true,
                                            'created_at' => $values[7],
                                            'updated_at' => $values[8] ?? $values[7],
                                            'email_verified_at' => $values[8] ?? null,
                                        ]);
                                        $inserted++;
                                    });
                                } catch (\Throwable $e) {
                                    $this->error('User import error: ' . $e->getMessage());
                                    $this->error('Values: ' . json_encode($values));
                                }
                            }
                        }
                        // Reset buffer
                        $capturing = false;
                        $buffer = '';
                    }
                }
            }
            fclose($file);
        }

        $this->info("Users imported: {$inserted}");
    }

    private function importCourses($sqlFilePath)
    {
        $this->info('Importing courses...');
        $file = fopen($sqlFilePath, 'r');

        if ($file) {
            $capturing = false;
            $buffer = '';
            while (($line = fgets($file)) !== false) {
                if (!$capturing && strpos($line, 'INSERT INTO `courses`') !== false) {
                    $capturing = true;
                    $buffer = $line;
                    continue;
                }

                if ($capturing) {
                    $buffer .= $line;
                    if (strpos($line, ');') !== false) {
                        if (preg_match('/INSERT INTO `courses`.*?VALUES\s*\((.*)\);/si', $buffer, $match)) {
                            $valuesString = $match[1];
                            $records = preg_split('/\),\s*\(/', $valuesString);
                            foreach ($records as $record) {
                                $record = trim($record, "() \r\n\t");
                                $values = str_getcsv($record, ',', "'");
                                $values = array_map(function($v) { return $v === 'NULL' ? null : $v; }, $values);

                                Course::unguarded(function () use ($values, &$inserted) {
                                    Course::create([
                                        'id' => $values[0],
                                        'name' => $values[1],
                                        'description' => $values[2],
                                        'image_url' => $values[3],
                                        'is_active' => true,
                                        'created_at' => $values[4],
                                        'updated_at' => $values[4],
                                    ]);
                                });
                            }
                        }
                        $capturing = false;
                        $buffer = '';
                    }
                }
            }
            fclose($file);
        }

        $this->info("Courses imported: {$inserted}");
    }

    private function importSections($sqlFilePath)
    {
        $this->info('Importing sections...');
        $file = fopen($sqlFilePath, 'r');

        if ($file) {
            $capturing = false;
            $buffer = '';
            while (($line = fgets($file)) !== false) {
                if (!$capturing && strpos($line, 'INSERT INTO `sections`') !== false) {
                    $capturing = true;
                    $buffer = $line;
                    continue;
                }

                if ($capturing) {
                    $buffer .= $line;
                    if (strpos($line, ');') !== false) {
                        if (preg_match('/INSERT INTO `sections`.*?VALUES\s*\((.*)\);/si', $buffer, $match)) {
                            $valuesString = $match[1];
                            $records = preg_split('/\),\s*\(/', $valuesString);
                            foreach ($records as $record) {
                                $record = trim($record, "() \r\n\t");
                                $values = str_getcsv($record, ',', "'");
                                $values = array_map(function($v) { return $v === 'NULL' ? null : $v; }, $values);

                                Section::unguarded(function () use ($values, &$inserted) {
                                    Section::create([
                                        'id' => $values[0],
                                        'course_id' => $values[1],
                                        'name' => $values[2],
                                        'section_order' => $values[3],
                                        'is_active' => true,
                                        'created_at' => $values[4],
                                        'updated_at' => $values[4],
                                    ]);
                                });
                            }
                        }
                        $capturing = false;
                        $buffer = '';
                    }
                }
            }
            fclose($file);
        }

        $this->info("Sections imported: {$inserted}");
    }

    private function importLessons($sqlFilePath)
    {
        $this->info('Importing lessons...');
        $file = fopen($sqlFilePath, 'r');

        if ($file) {
            $capturing = false;
            $buffer = '';
            while (($line = fgets($file)) !== false) {
                if (!$capturing && strpos($line, 'INSERT INTO `lessons`') !== false) {
                    $capturing = true;
                    $buffer = $line;
                    continue;
                }

                if ($capturing) {
                    $buffer .= $line;
                    if (strpos($line, ');') !== false) {
                        if (preg_match('/INSERT INTO `lessons`.*?VALUES\s*\((.*)\);/si', $buffer, $match)) {
                            $valuesString = $match[1];
                            $records = preg_split('/\),\s*\(/', $valuesString);
                            foreach ($records as $record) {
                                $record = trim($record, "() \r\n\t");
                                $values = str_getcsv($record, ',', "'");
                                $values = array_map(function($v) { return $v === 'NULL' ? null : $v; }, $values);

                                Lesson::unguarded(function () use ($values, &$inserted) {
                                    Lesson::create([
                                        'id' => $values[0],
                                        'section_id' => $values[1],
                                        'title' => $values[2],
                                        'video_url' => $values[3],
                                        'lesson_order' => $values[4],
                                        'is_active' => true,
                                        'created_at' => $values[5],
                                        'updated_at' => $values[5],
                                    ]);
                                });
                            }
                        }
                        $capturing = false;
                        $buffer = '';
                    }
                }
            }
            fclose($file);
        }

        $this->info("Lessons imported: {$inserted}");
    }

    private function importEnrollments($sqlFilePath)
    {
        $this->info('Importing enrollments...');
        $file = fopen($sqlFilePath, 'r');

        if ($file) {
            $capturing = false;
            $buffer = '';
            while (($line = fgets($file)) !== false) {
                if (!$capturing && strpos($line, 'INSERT INTO `enrollments`') !== false) {
                    $capturing = true;
                    $buffer = $line;
                    continue;
                }

                if ($capturing) {
                    $buffer .= $line;
                    if (strpos($line, ');') !== false) {
                        if (preg_match('/INSERT INTO `enrollments`.*?VALUES\s*\((.*)\);/si', $buffer, $match)) {
                            $valuesString = $match[1];
                            $records = preg_split('/\),\s*\(/', $valuesString);
                            foreach ($records as $record) {
                                $record = trim($record, "() \r\n\t");
                                $values = str_getcsv($record, ',', "'");
                                $values = array_map(function($v) { return $v === 'NULL' ? null : $v; }, $values);

                                Enrollment::unguarded(function () use ($values, &$inserted) {
                                    Enrollment::create([
                                        'id' => $values[0],
                                        'user_id' => $values[1],
                                        'course_id' => $values[2],
                                        'enrolled_at' => $values[3],
                                        'expires_at' => $values[4],
                                        'is_active' => $values[5],
                                        'created_at' => $values[3],
                                        'updated_at' => $values[3],
                                    ]);
                                });
                            }
                        }
                        $capturing = false;
                        $buffer = '';
                    }
                }
            }
            fclose($file);
        }

        $this->info("Enrollments imported: {$inserted}");
    }

    private function importLessonProgress($sqlFilePath)
    {
        $this->info('Importing lesson progress...');
        $file = fopen($sqlFilePath, 'r');

        if ($file) {
            $capturing = false;
            $buffer = '';
            while (($line = fgets($file)) !== false) {
                if (!$capturing && strpos($line, 'INSERT INTO `lesson_progress`') !== false) {
                    $capturing = true;
                    $buffer = $line;
                    continue;
                }

                if ($capturing) {
                    $buffer .= $line;
                    if (strpos($line, ');') !== false) {
                        if (preg_match('/INSERT INTO `lesson_progress`.*?VALUES\s*\((.*)\);/si', $buffer, $match)) {
                            $valuesString = $match[1];
                            $records = preg_split('/\),\s*\(/', $valuesString);
                            foreach ($records as $record) {
                                $record = trim($record, "() \r\n\t");
                                $values = str_getcsv($record, ',', "'");
                                $values = array_map(function($v) { return $v === 'NULL' ? null : $v; }, $values);

                                LessonProgress::unguarded(function () use ($values, &$inserted) {
                                    LessonProgress::create([
                                        'id' => $values[0],
                                        'user_id' => $values[1],
                                        'lesson_id' => $values[2],
                                        'completed' => $values[3],
                                        'completed_at' => $values[4],
                                        'watch_time_seconds' => $values[5],
                                        'created_at' => $values[6],
                                        'updated_at' => $values[7],
                                    ]);
                                });
                            }
                        }
                        $capturing = false;
                        $buffer = '';
                    }
                }
            }
            fclose($file);
        }

        $this->info("Lesson progress imported: {$inserted}");
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data import...');

        $sqlFilePath = database_path('../Sql1807360_4.sql');

        if (!file_exists($sqlFilePath)) {
            $this->error('SQL dump file not found at: ' . $sqlFilePath);
            return 1;
        }

        $dbDriver = DB::connection()->getDriverName();
        $this->info("Using DB driver: {$dbDriver}");

        // Disable Foreign Key Checks
        if ($dbDriver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($dbDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Truncate tables
        $tables = ['users', 'courses', 'sections', 'lessons', 'enrollments', 'lesson_progress'];
        foreach ($tables as $table) {
            if ($dbDriver === 'sqlite') {
                DB::statement("DELETE FROM \"{$table}\"");
            } else {
                DB::table($table)->truncate();
            }
        }

        // Import data
        $this->importUsers($sqlFilePath);
        $this->importCourses($sqlFilePath);
        $this->importSections($sqlFilePath);
        $this->importLessons($sqlFilePath);
        $this->importEnrollments($sqlFilePath);
        $this->importLessonProgress($sqlFilePath);

        // Reset auto-increment values (handled differently by drivers)
        if ($dbDriver === 'mysql') {
            $maxUserId = User::max('id') + 1;
            DB::statement("ALTER TABLE users AUTO_INCREMENT = $maxUserId;");
            $maxCourseId = Course::max('id') + 1;
            DB::statement("ALTER TABLE courses AUTO_INCREMENT = $maxCourseId;");
            $maxSectionId = Section::max('id') + 1;
            DB::statement("ALTER TABLE sections AUTO_INCREMENT = $maxSectionId;");
            $maxLessonId = Lesson::max('id') + 1;
            DB::statement("ALTER TABLE lessons AUTO_INCREMENT = $maxLessonId;");
            $maxEnrollmentId = Enrollment::max('id') > 0 ? Enrollment::max('id') + 1 : 1;
            DB::statement("ALTER TABLE enrollments AUTO_INCREMENT = $maxEnrollmentId;");
            $maxLessonProgressId = LessonProgress::max('id') > 0 ? LessonProgress::max('id') + 1 : 1;
            DB::statement("ALTER TABLE lesson_progress AUTO_INCREMENT = $maxLessonProgressId;");
        } elseif ($dbDriver === 'sqlite') {
            // For SQLite, truncating via `DELETE FROM` and then cleaning the sequence table is the way.
            foreach ($tables as $table) {
                DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$table}';");
            }
        }

        // Enable Foreign Key Checks
        if ($dbDriver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($dbDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $this->info('Data import completed successfully.');
    }
}
