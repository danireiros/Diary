<?php

namespace App\Services;

use App\Models\Task;
use Carbon\CarbonImmutable;

class RecurrenceService
{
    public function expand(Task $task, CarbonImmutable $rangeStart, CarbonImmutable $rangeEnd): array
    {
        $occurrences = [];
        $max = 2000;

        $recurrence = $task->recurrence_json ?? null;
        $baseStart = $task->start_at ? CarbonImmutable::parse($task->start_at) : null;
        $baseEnd = $task->end_at ? CarbonImmutable::parse($task->end_at) : null;

        if (!$recurrence) {
            if ($baseStart && $baseEnd && $this->overlaps($baseStart, $baseEnd, $rangeStart, $rangeEnd)) {
                $occurrences[] = $this->formatOccurrence($task, $baseStart, $baseEnd);
            }
            return $occurrences;
        }

        $freq = $recurrence['freq'] ?? null;
        $interval = max(1, (int) ($recurrence['interval'] ?? 1));
        $until = isset($recurrence['until']) ? CarbonImmutable::parse($recurrence['until']) : null;
        $count = isset($recurrence['count']) ? (int) $recurrence['count'] : null;
        $time = $recurrence['time'] ?? null; // HH:mm

        $byWeekday = $recurrence['byWeekday'] ?? null; // 0..6 (Sun..Sat)
        $byMonthDay = $recurrence['byMonthDay'] ?? null; // 1..31

        if (!$baseStart) {
            return $occurrences;
        }

        $start = $baseStart;
        $end = $baseEnd ?? $baseStart;
        $occurrenceNum = 0;

        while ($max-- > 0) {
            if ($until && $start->gt($until)) break;
            if ($count && $occurrenceNum >= $count) break;

            if ($this->overlaps($start, $end, $rangeStart, $rangeEnd)) {
                $occurrences[] = $this->formatOccurrence($task, $start, $end);
            }

            $occurrenceNum++;

            switch ($freq) {
                case 'DAILY':
                    $start = $start->addDays($interval);
                    $end = $end->addDays($interval);
                    break;
                case 'WEEKLY':
                    if (is_array($byWeekday) && count($byWeekday) > 0) {
                        // Advance to next listed weekday
                        $next = $this->advanceToNextWeekday($start, $byWeekday, $interval);
                        $diffDays = $next->diffInDays($start);
                        $start = $start->addDays($diffDays);
                        $end = $end->addDays($diffDays);
                    } else {
                        $start = $start->addWeeks($interval);
                        $end = $end->addWeeks($interval);
                    }
                    break;
                case 'MONTHLY':
                    if (is_array($byMonthDay) && count($byMonthDay) > 0) {
                        $next = $this->advanceToNextMonthDay($start, $byMonthDay, $interval);
                        $diffDays = $next->diffInDays($start);
                        $start = $start->addDays($diffDays);
                        $end = $end->addDays($diffDays);
                    } else {
                        $start = $start->addMonths($interval);
                        $end = $end->addMonths($interval);
                    }
                    break;
                default:
                    // Unsupported
                    $max = 0;
            }
        }

        return $occurrences;
    }

    public function expandMany($tasks, CarbonImmutable $start, CarbonImmutable $end): array
    {
        $all = [];
        foreach ($tasks as $task) {
            foreach ($this->expand($task, $start, $end) as $occ) {
                $all[] = $occ;
            }
        }
        return $all;
    }

    private function formatOccurrence(Task $task, CarbonImmutable $start, CarbonImmutable $end): array
    {
        return [
            'task' => (new \App\Http\Resources\TaskResource($task))->toArray(request()),
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String(),
            'allDay' => (bool) $task->all_day,
        ];
    }

    private function overlaps(CarbonImmutable $aStart, CarbonImmutable $aEnd, CarbonImmutable $bStart, CarbonImmutable $bEnd): bool
    {
        return $aStart <= $bEnd && $aEnd >= $bStart;
    }

    private function advanceToNextWeekday(CarbonImmutable $current, array $weekdays, int $interval): CarbonImmutable
    {
        sort($weekdays);
        $currentDow = (int) $current->dayOfWeek; // 0=Sun
        foreach ($weekdays as $d) {
            if ($d >= $currentDow) {
                return $current->startOfDay()->addDays($d - $currentDow)->setTimeFromTimeString($current->toTimeString());
            }
        }
        // next interval weeks to first day
        $first = (int) $weekdays[0];
        $days = (7 - $currentDow) + $first + (7 * ($interval - 1));
        return $current->startOfDay()->addDays($days)->setTimeFromTimeString($current->toTimeString());
    }

    private function advanceToNextMonthDay(CarbonImmutable $current, array $monthDays, int $interval): CarbonImmutable
    {
        sort($monthDays);
        $dom = (int) $current->day;
        foreach ($monthDays as $d) {
            if ($d >= $dom && $d <= $current->daysInMonth) {
                return $current->setDay($d);
            }
        }
        // next interval months to first day
        $first = (int) $monthDays[0];
        $next = $current->addMonths($interval)->setDay(min($first, $current->addMonths($interval)->daysInMonth));
        return $next;
    }
}


