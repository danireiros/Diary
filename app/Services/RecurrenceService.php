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

        // Never generate occurrences before the later of baseStart and the requested rangeStart
        $durationSeconds = $end->diffInSeconds($start);
        if ($rangeStart->gt($start)) {
            $start = $rangeStart->setTimeFromTimeString($baseStart->toTimeString());
            $end = $start->addSeconds($durationSeconds);
        }

        // Align the first occurrence to the next valid date on/after baseStart
        if ($freq === 'WEEKLY' && is_array($byWeekday) && count($byWeekday) > 0) {
            $aligned = $this->alignToWeekday($start, $byWeekday);
            $diffDays = $aligned->diffInDays($start);
            $start = $start->addDays($diffDays);
            $end = $end->addDays($diffDays);
        }
        if ($freq === 'MONTHLY' && is_array($byMonthDay) && count($byMonthDay) > 0) {
            $aligned = $this->alignToMonthDay($start, $byMonthDay);
            $diffDays = $aligned->diffInDays($start);
            $start = $start->addDays($diffDays);
            $end = $end->addDays($diffDays);
        }

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
                        // Step forward day-by-day until reaching an allowed weekday and week that matches the interval
                        $allowed = array_values(array_map('intval', $byWeekday));
                        sort($allowed);
                        $anchorWeekStart = $baseStart->startOfDay()->subDays((int) $baseStart->dayOfWeek);
                        do {
                            $start = $start->addDay();
                            $end = $end->addDay();
                            $dow = (int) $start->dayOfWeek; // 0=Sun..6=Sat
                            $candWeekStart = $start->startOfDay()->subDays($dow);
                            $weeksDiff = $anchorWeekStart->diffInWeeks($candWeekStart);
                        } while (!in_array($dow, $allowed, true) || ($weeksDiff % $interval !== 0));
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
        // Normalize all-day occurrences to avoid timezone day-shift issues in UIs
        if ((bool) $task->all_day) {
            $startLocal = $start->shiftTimezone('Atlantic/Canary')->startOfDay();
            $endLocal = $end->shiftTimezone('Atlantic/Canary')->endOfDay();
            // Emit at 12:00 UTC to make it safe across timezones
            $start = $startLocal->shiftTimezone('UTC')->setTime(12, 0);
            $end = $endLocal->shiftTimezone('UTC')->setTime(12, 0);
        }

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
            if ($d > $currentDow) {
                return $current->startOfDay()->addDays($d - $currentDow)->setTimeFromTimeString($current->toTimeString());
            }
        }
        // next interval weeks to first day
        $first = (int) $weekdays[0];
        $days = (7 - $currentDow) + $first + (7 * ($interval - 1));
        return $current->startOfDay()->addDays($days)->setTimeFromTimeString($current->toTimeString());
    }

    private function nextWeeklyOccurrence(CarbonImmutable $current, array $weekdays, int $interval): CarbonImmutable
    {
        // Returns the next occurrence strictly after $current on the allowed weekdays.
        sort($weekdays);
        $currentDow = (int) $current->dayOfWeek; // 0=Sun..6=Sat
        foreach ($weekdays as $d) {
            if ($d > $currentDow) {
                return $current->startOfDay()->addDays($d - $currentDow)->setTimeFromTimeString($current->toTimeString());
            }
        }
        // Jump to the first allowed weekday in the next interval week block
        $first = (int) $weekdays[0];
        $days = (7 - $currentDow) + $first + 7 * ($interval - 1);
        return $current->startOfDay()->addDays($days)->setTimeFromTimeString($current->toTimeString());
    }

    private function advanceToNextMonthDay(CarbonImmutable $current, array $monthDays, int $interval): CarbonImmutable
    {
        sort($monthDays);
        $dom = (int) $current->day;
        foreach ($monthDays as $d) {
            if ($d > $dom && $d <= $current->daysInMonth) {
                return $current->setDay($d);
            }
        }
        // next interval months to first day
        $first = (int) $monthDays[0];
        $next = $current->addMonths($interval)->setDay(min($first, $current->addMonths($interval)->daysInMonth));
        return $next;
    }

    private function alignToWeekday(CarbonImmutable $current, array $weekdays): CarbonImmutable
    {
        sort($weekdays);
        $currentDow = (int) $current->dayOfWeek; // 0=Sun
        foreach ($weekdays as $d) {
            if ($d >= $currentDow) {
                return $current->startOfDay()->addDays($d - $currentDow)->setTimeFromTimeString($current->toTimeString());
            }
        }
        $first = (int) $weekdays[0];
        $days = (7 - $currentDow) + $first;
        return $current->startOfDay()->addDays($days)->setTimeFromTimeString($current->toTimeString());
    }

    private function alignToMonthDay(CarbonImmutable $current, array $monthDays): CarbonImmutable
    {
        sort($monthDays);
        $dom = (int) $current->day;
        foreach ($monthDays as $d) {
            if ($d >= $dom && $d <= $current->daysInMonth) {
                return $current->setDay($d);
            }
        }
        $first = (int) $monthDays[0];
        return $current->addMonth()->setDay(min($first, $current->addMonth()->daysInMonth));
    }
}


