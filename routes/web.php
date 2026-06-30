<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Member\BookController as MemberBookController;
use App\Http\Controllers\Member\LoanController as MemberLoanController;
use App\Http\Controllers\Member\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
*/
Route::
//    middleware(['auth'])->
prefix('member')->name('member.')->group(function () {

    // Katalog Buku
    Route::get('/books', [MemberBookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [MemberBookController::class, 'show'])->name('books.show');

    // Peminjaman
    Route::get('/loans', [MemberLoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [MemberLoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/history', [MemberLoanController::class, 'history'])->name('loans.history');
    Route::get('/loans/{id}', [MemberLoanController::class, 'show'])->name('loans.show');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Kelola Buku
    Route::get('/books', [AdminBookController::class, 'index'])
        ->name('books.index');

    Route::get('/books/create', [AdminBookController::class, 'create'])
        ->name('books.create');

    Route::post('/books', [AdminBookController::class, 'store'])
        ->name('books.store');

    Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])
        ->name('books.edit');

    Route::put('/books/{id}', [AdminBookController::class, 'update'])
        ->name('books.update');

    Route::delete('/books/{id}', [AdminBookController::class, 'destroy'])
        ->name('books.destroy');

    // Kategori
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('categories.store');

    Route::put('/categories/{id}', [CategoryController::class, 'update'])
        ->name('categories.update');

    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    // Peminjaman
    Route::get('/loans', [AdminLoanController::class, 'index'])
        ->name('loans.index');

    Route::get('/loans/{id}', [AdminLoanController::class, 'show'])
        ->name('loans.show');

    // Pengembalian
    Route::get('/returns', [ReturnController::class, 'index'])
        ->name('returns.index');

    Route::post('/returns', [ReturnController::class, 'store'])
        ->name('returns.store');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

//    // Export Laporan
//    Route::get('/reports/export', [ReportController::class, 'export'])
//        ->name('reports.export');

    Route::get(
        '/notifications',
        [\App\Http\Controllers\Admin\NotificationController::class, 'index']
    )->name('notifications.index');

    Route::post(
        '/notifications/{id}/read',
        [\App\Http\Controllers\Admin\NotificationController::class, 'markRead']
    )->name('notifications.read');

    Route::post(
        '/notifications/read-all',
        [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead']
    )->name('notifications.read-all');
});
