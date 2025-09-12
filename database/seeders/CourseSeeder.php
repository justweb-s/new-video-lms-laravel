<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\WorkoutCard;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users
        $user1 = User::create([
            'name' => 'Mario Rossi',
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => Hash::make('password'),
            'phone' => '+39 123 456 7890',
            'is_active' => true,
        ]);

        $user2 = User::create([
            'name' => 'Giulia Bianchi',
            'first_name' => 'Giulia',
            'last_name' => 'Bianchi',
            'email' => 'giulia.bianchi@example.com',
            'password' => Hash::make('password'),
            'phone' => '+39 987 654 3210',
            'is_active' => true,
        ]);

        // Create sample courses
        $course1 = Course::create([
            'name' => 'Corso Base di Fitness',
            'description' => 'Un corso completo per iniziare il tuo percorso di fitness con esercizi base e tecniche fondamentali.',
            'price' => 99.99,
            'duration_days' => 8,
            'prerequisites' => 'Nessun prerequisito richiesto',
            'is_active' => true,
        ]);

        $course2 = Course::create([
            'name' => 'Allenamento Avanzato',
            'description' => 'Corso avanzato per chi vuole portare il proprio allenamento al livello successivo.',
            'price' => 149.99,
            'duration_days' => 12,
            'prerequisites' => 'Completamento del corso base o esperienza equivalente',
            'is_active' => true,
        ]);

        // Create sections for Course 1
        $section1_1 = Section::create([
            'course_id' => $course1->id,
            'name' => 'Introduzione al Fitness',
            'description' => 'Concetti base e preparazione',
            'section_order' => 1,
            'is_active' => true,
        ]);

        $section1_2 = Section::create([
            'course_id' => $course1->id,
            'name' => 'Esercizi Fondamentali',
            'description' => 'Esercizi base per tutto il corpo',
            'section_order' => 2,
            'is_active' => true,
        ]);

        // Create lessons for Section 1.1
        Lesson::create([
            'section_id' => $section1_1->id,
            'title' => 'Benvenuto al corso',
            'description' => 'Introduzione al corso e obiettivi',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 10,
            'lesson_order' => 1,
            'is_active' => true,
        ]);

        Lesson::create([
            'section_id' => $section1_1->id,
            'title' => 'Riscaldamento e preparazione',
            'description' => 'Come prepararsi correttamente all\'allenamento',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 15,
            'lesson_order' => 2,
            'is_active' => true,
        ]);

        // Create lessons for Section 1.2
        Lesson::create([
            'section_id' => $section1_2->id,
            'title' => 'Squat perfetto',
            'description' => 'Tecnica corretta per eseguire gli squat',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 20,
            'lesson_order' => 1,
            'is_active' => true,
        ]);

        Lesson::create([
            'section_id' => $section1_2->id,
            'title' => 'Push-up e varianti',
            'description' => 'Come eseguire push-up e le sue varianti',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 18,
            'lesson_order' => 2,
            'is_active' => true,
        ]);

        // Create sections for Course 2
        $section2_1 = Section::create([
            'course_id' => $course2->id,
            'name' => 'Tecniche Avanzate',
            'description' => 'Tecniche per atleti esperti',
            'section_order' => 1,
            'is_active' => true,
        ]);

        // Create lessons for Section 2.1
        Lesson::create([
            'section_id' => $section2_1->id,
            'title' => 'Allenamento HIIT',
            'description' => 'High Intensity Interval Training',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 25,
            'lesson_order' => 1,
            'is_active' => true,
        ]);

        // Create workout cards
        WorkoutCard::create([
            'course_id' => $course1->id,
            'title' => 'Scheda Allenamento Base',
            'content' => 'Esercizi:\n- Squat: 3x12\n- Push-up: 3x10\n- Plank: 3x30s\n- Jumping Jacks: 3x20',
            'warmup' => 'Riscaldamento:\n- 5 minuti di camminata\n- Stretching dinamico\n- Mobilità articolare',
            'venous_return' => 'Defaticamento:\n- 5 minuti di camminata lenta\n- Stretching statico\n- Respirazione profonda',
            'notes' => 'Riposo tra le serie: 60-90 secondi\nEseguire 3 volte a settimana',
            'is_active' => true,
        ]);

        WorkoutCard::create([
            'course_id' => $course2->id,
            'title' => 'Scheda Allenamento Avanzato',
            'content' => 'Esercizi:\n- Squat con peso: 4x8\n- Push-up diamante: 4x6\n- Plank laterale: 4x45s\n- Burpees: 4x10',
            'warmup' => 'Riscaldamento avanzato:\n- 10 minuti di cardio\n- Stretching dinamico completo\n- Attivazione muscolare',
            'venous_return' => 'Defaticamento completo:\n- 10 minuti di cardio leggero\n- Stretching completo\n- Foam rolling',
            'notes' => 'Riposo tra le serie: 90-120 secondi\nEseguire 4-5 volte a settimana',
            'is_active' => true,
        ]);

        // Create enrollments
        Enrollment::create([
            'user_id' => $user1->id,
            'course_id' => $course1->id,
            'enrolled_at' => now(),
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
            'progress_percentage' => 25.0,
        ]);

        Enrollment::create([
            'user_id' => $user2->id,
            'course_id' => $course1->id,
            'enrolled_at' => now()->subDays(10),
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
            'progress_percentage' => 50.0,
        ]);

        Enrollment::create([
            'user_id' => $user2->id,
            'course_id' => $course2->id,
            'enrolled_at' => now()->subDays(5),
            'expires_at' => now()->addMonths(12),
            'is_active' => true,
            'progress_percentage' => 10.0,
        ]);
    }
}
