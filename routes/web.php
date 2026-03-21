<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OpdVisitController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\IpdAdmissionController;
use App\Http\Controllers\IpdManagementController;
use App\Http\Controllers\IpdDischargeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Users
    Route::resource('users', UserController::class);

    // Patients
    Route::resource('patients', PatientController::class);

    // Doctors
    Route::resource('doctors', DoctorController::class);

    // OPD Visits
    Route::post('opd/{opd}/invoice', [OpdVisitController::class, 'generateInvoice'])->name('opd.invoice');
    Route::get('opd/{opd}/prescription', [OpdVisitController::class, 'printPrescription'])->name('opd.prescription');
    Route::resource('opd', OpdVisitController::class);

    // IPD & Beds
    Route::resource('beds', BedController::class);
    
    Route::post('ipd/{ipd}/notes', [IpdManagementController::class, 'storeNote'])->name('ipd.notes.store');
    Route::post('ipd/{ipd}/medications', [IpdManagementController::class, 'storeMedication'])->name('ipd.meds.store');
    Route::post('ipd/{ipd}/labs', [IpdManagementController::class, 'storeLabRequest'])->name('ipd.labs.store');
    
    Route::get('ipd/{ipd}/discharge', [IpdDischargeController::class, 'create'])->name('ipd.discharge.create');
    Route::post('ipd/{ipd}/discharge', [IpdDischargeController::class, 'store'])->name('ipd.discharge.store');
    Route::get('ipd/{ipd}/certificate', [IpdDischargeController::class, 'printCertificate'])->name('ipd.certificate');

    Route::resource('ipd', IpdAdmissionController::class);

    // Appointments
    Route::resource('appointments', AppointmentController::class);

    // EMR (Medical Records)
    Route::resource('emr', MedicalRecordController::class);

    // Billing
    Route::resource('billing', BillingController::class);

    Route::get('/checkout/{billing}/payment', [CheckoutController::class, 'paymentPage'])->name('checkout.payment.page');
    Route::post('/checkout/{billing}/cash', [CheckoutController::class, 'payCash'])->name('checkout.pay.cash');
    Route::get('/checkout/{billing}/khqr', [CheckoutController::class, 'showKhqr'])->name('checkout.pay.khqr');
    Route::post('/checkout/verify-transaction', [CheckoutController::class, 'verifyTransaction'])->name('checkout.verify.transaction');
    Route::get('/checkout/{billing}/success', [CheckoutController::class, 'success'])->name('checkout.success');
});
