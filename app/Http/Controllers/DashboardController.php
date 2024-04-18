<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a dashboard.
     */
    public function index(Request $request)
    {
        $month = null;
        $year = null;
        $now = Carbon::now();

        //first get payed and unpayed for the current month
        $startOfMonth = Carbon::create($now->year, $now->month)->startOfMonth();
        $endOfMonth = Carbon::create($now->year, $now->month)->endOfMonth();

        $payed = WorkLog::TotalPayed($startOfMonth, $endOfMonth);
        $unpayed = WorkLog::TotalUnpayed($startOfMonth, $endOfMonth);

        //then get month and year from url params and get table data
        if ($request->query->has('month')) {
            $month = intval($request->query->get('month'));
        } else {
            $month = $now->month;
        }
        if ($request->query->has('year')) {
            $year = intval($request->query->get('year'));
        } else {
            $year = $now->year;
        }
        $startOfMonth = Carbon::create($year, $month)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month)->endOfMonth();

        $data = Developer::getDevelopersWorkLogHoursByMonth($startOfMonth, $endOfMonth);

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $monthName = Carbon::createFromFormat('m', $month)->format('F');
        $previousMonthName = Carbon::createFromFormat('m', $month)->subMonth()->format('F');
        $nextMonthName = Carbon::createFromFormat('m', $month)->addMonth()->format('F');

        $currentMonth = [
            'name' => $monthName,
            'link' => "?month=$month&year=$year"
        ];

        $prevMonth = [
            'name' => $previousMonthName,
            'link' => "?month=" . ($month == 1 ? "12&year=" . $year - 1 : $month - 1 . "&year=$year")
        ];
        $nextMonth = [
            'name' => $nextMonthName,
            'link' => "?month=" . ($month == 12 ? "1&year=" . $year + 1 : $month + 1 . "&year=$year")
        ];

        $table = [
            'data' => $data,
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'currentMonth' => $currentMonth,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth
        ];

        return Inertia::render('Dashboard', [
            'payed' => $payed,
            'unpayed' => $unpayed,
            'table' => $table,
        ]);
    }
}
