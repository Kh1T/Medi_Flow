<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Billing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'patients' => Patient::count(),
            'doctors' => Doctor::count(),
            'appointments' => Appointment::count(),
            'revenue' => Billing::where('status', 'paid')->sum('total'),
        ];

        $recent_appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('stats', 'recent_appointments'));
    }
}
