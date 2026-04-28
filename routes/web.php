<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudySessionController;
use App\Http\Controllers\TestSessionController;
use App\Http\Controllers\DailyPlanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SettingsController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// مطالعه
Route::resource('study', StudySessionController::class)->except(['show']);
Route::get('api/topics', [StudySessionController::class, 'getTopics'])->name('api.topics');

// تست
Route::resource('test', TestSessionController::class)->except(['show']);

// برنامه روزانه
Route::get('plan', [DailyPlanController::class, 'index'])->name('plan.index');
Route::get('plan/{jalali?}', [DailyPlanController::class, 'show'])->name('plan.show')->where('jalali', '[\d\-]+');
Route::post('plan', [DailyPlanController::class, 'storeOrUpdate'])->name('plan.store');
Route::post('plan/item', [DailyPlanController::class, 'addItem'])->name('plan.item.store');
Route::patch('plan/item/{item}/toggle', [DailyPlanController::class, 'toggleItem'])->name('plan.item.toggle');
Route::delete('plan/item/{item}', [DailyPlanController::class, 'destroyItem'])->name('plan.item.destroy');

// گزارش
Route::get('report', [ReportController::class, 'index'])->name('report.index');

// درس‌ها
Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
Route::get('subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
Route::post('subjects/{subject}/topics', [SubjectController::class, 'storeTopic'])->name('subjects.topics.store');
Route::post('subjects/{subject}/resources', [SubjectController::class, 'storeResource'])->name('subjects.resources.store');
Route::delete('topics/{topic}', [SubjectController::class, 'destroyTopic'])->name('topics.destroy');
Route::delete('resources/{resource}', [SubjectController::class, 'destroyResource'])->name('resources.destroy');

// تنظیمات
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
