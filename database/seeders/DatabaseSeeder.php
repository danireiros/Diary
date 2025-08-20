<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\DiaryCategory;
use App\Models\Task;
use App\Models\Note;
use App\Models\JournalEntry;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\CarbonImmutable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed example user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Categories (tasks/notes)
        $general = Category::create(['name' => 'General', 'color' => '#3b82f6']);
        $limpieza = Category::create(['name' => 'Limpieza', 'color' => '#10b981']);
        $compras = Category::create(['name' => 'Compras', 'color' => '#ef4444']);

        // Diary categories
        $personal = DiaryCategory::create(['name' => 'Personal', 'color' => '#8b5cf6']);
        $salud = DiaryCategory::create(['name' => 'Salud', 'color' => '#22c55e']);
        $ideas = DiaryCategory::create(['name' => 'Ideas', 'color' => '#f59e0b']);

        // Sample tasks
        $now = CarbonImmutable::now('Atlantic/Canary');

        // Today timed
        Task::create([
            'title' => 'Reunión de proyecto',
            'category_id' => $general->id,
            'status' => 'todo',
            'all_day' => false,
            'start_at' => $now->setTime(10, 0)->utc(),
            'end_at' => $now->setTime(11, 0)->utc(),
        ]);

        // Daily recurrence at 08:00
        Task::create([
            'title' => 'Chequeo diario',
            'category_id' => $general->id,
            'status' => 'pending',
            'all_day' => false,
            'start_at' => $now->setTime(8, 0)->utc(),
            'end_at' => $now->setTime(8, 30)->utc(),
            'recurrence_json' => [
                'freq' => 'DAILY',
                'interval' => 1,
                'time' => '08:00',
            ],
        ]);

        // All-day multi-day
        Task::create([
            'title' => 'Limpieza general',
            'category_id' => $limpieza->id,
            'status' => 'todo',
            'all_day' => true,
            'start_at' => $now->startOfDay()->utc(),
            'end_at' => $now->addDays(2)->endOfDay()->utc(),
        ]);

        // Monthly byMonthDay([17])
        Task::create([
            'title' => 'Informe mensual',
            'category_id' => $general->id,
            'status' => 'todo',
            'all_day' => false,
            'start_at' => $now->setTime(9, 0)->utc(),
            'end_at' => $now->setTime(10, 0)->utc(),
            'recurrence_json' => [
                'freq' => 'MONTHLY',
                'interval' => 1,
                'byMonthDay' => [17],
                'time' => '09:00',
            ],
        ]);

        // Weekly byWeekday([1]) - Monday
        Task::create([
            'title' => 'Plan semanal',
            'category_id' => $general->id,
            'status' => 'todo',
            'all_day' => false,
            'start_at' => $now->setTime(12, 0)->utc(),
            'end_at' => $now->setTime(13, 0)->utc(),
            'recurrence_json' => [
                'freq' => 'WEEKLY',
                'interval' => 1,
                'byWeekday' => [1],
                'time' => '12:00',
            ],
        ]);

        // Monthly 1st day payment
        Task::create([
            'title' => 'Pago de suscripción',
            'category_id' => $compras->id,
            'status' => 'todo',
            'all_day' => false,
            'start_at' => $now->setTime(7, 30)->utc(),
            'end_at' => $now->setTime(7, 45)->utc(),
            'recurrence_json' => [
                'freq' => 'MONTHLY',
                'interval' => 1,
                'byMonthDay' => [1],
                'time' => '07:30',
            ],
        ]);

        // Sample notes
        Note::create(['title' => 'Comprar leche', 'content' => 'Entera y desnatada', 'category_id' => $compras->id, 'status' => 'todo']);
        Note::create(['title' => 'Recordatorio limpieza', 'content' => 'Cocina y baño', 'category_id' => $limpieza->id, 'status' => 'pending']);

        // Sample journal entries
        JournalEntry::create(['date' => $now->toDateString(), 'title' => 'Día productivo', 'content' => 'Avancé mucho.', 'diary_category_id' => $personal->id, 'hidden' => false]);
        JournalEntry::create(['date' => $now->subDay()->toDateString(), 'title' => 'Idea nueva', 'content' => 'Probar nueva app.', 'diary_category_id' => $ideas->id, 'hidden' => true]);
    }
}
