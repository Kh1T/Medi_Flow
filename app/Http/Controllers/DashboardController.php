<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\OpdVisit;
use App\Models\IpdAdmission;
use App\Models\Billing;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_opd_visits' => OpdVisit::whereDate('visit_date', today())->count(),
            'current_ipd_patients' => IpdAdmission::where('status', 'admitted')->orWhereNull('discharge_date')->count(),
            'doctors' => Doctor::count(),
            'appointments' => Appointment::count(),
            'revenue' => Billing::where('status', 'paid')->sum('total'),
        ];

        // 1. Revenue Chart Data (Last 6 Months: OPD vs IPD)
        $months = collect(range(5, 0))->map(function($i) { return now()->subMonths($i)->format('Y-m'); });
        $revenueData = [
            'labels' => $months->map(function($m) { return Carbon::parse($m)->format('M Y'); })->toArray(),
            'opd' => [],
            'ipd' => []
        ];

        foreach ($months as $month) {
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();

            $revenueData['opd'][] = Billing::where('status', 'paid')
                ->whereNotNull('opd_visit_id')->whereNull('ipd_admission_id')
                ->whereBetween('created_at', [$start, $end])->sum('total');

            $revenueData['ipd'][] = Billing::where('status', 'paid')
                ->whereNotNull('ipd_admission_id')
                ->whereBetween('created_at', [$start, $end])->sum('total');
        }

        // 2. Bed Occupancy Data
        $bedStats = [
            'labels' => ['Occupied', 'Available'],
            'data' => [
                Bed::where('is_occupied', true)->count(),
                Bed::where('is_occupied', false)->count()
            ]
        ];

        // 3. Doctor-wise Patient Count (Top 5)
        // Combines OPD and IPD visits per doctor
        $doctorStatsRaw = DB::query()
            ->select('doctor_id', DB::raw('COUNT(*) as total_patients'))
            ->fromSub(function ($query) {
                $query->select('doctor_id')->from('opd_visits')
                      ->unionAll(DB::table('ipd_admissions')->select('doctor_id'));
            }, 'all_visits')
            ->groupBy('doctor_id')
            ->orderByDesc('total_patients')
            ->limit(5)
            ->get();

        $doctorStats = [
            'labels' => [],
            'data' => []
        ];
        foreach ($doctorStatsRaw as $ds) {
            $doc = Doctor::with('user')->find($ds->doctor_id);
            if ($doc && $doc->user) {
                $doctorStats['labels'][] = 'Dr. ' . $doc->user->name;
                $doctorStats['data'][] = $ds->total_patients;
            }
        }

        // 4. Popular Diagnoses (Top 5)
         $diagnosesRaw = DB::query()
            ->select('diagnosis', DB::raw('COUNT(*) as count'))
            ->fromSub(function ($query) {
                $query->select('diagnosis')->from('opd_visits')->whereNotNull('diagnosis')->where('diagnosis', '!=', '')
                      ->unionAll(DB::table('ipd_admissions')->select('diagnosis')->whereNotNull('diagnosis')->where('diagnosis', '!=', ''));
            }, 'all_diagnoses')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $recent_appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('stats', 'recent_appointments', 'revenueData', 'bedStats', 'doctorStats', 'diagnosesRaw'));
    }
}
