<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\TrainingController;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view ('admin.dashboard');
    })->name('admin.dashboard')->middleware('role:admin');

    Route::get('/user/dashboard', function () {
        return view ('user.dashboard');
    })->name('user.dashboard')->middleware('role:user');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/documents', [ProfileController::class, 'showDocuments'])->name('profile.documents');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.change-password');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Route::post('/profile/store', [ProfileController::class, 'storeProfile'])->name('profile.store');
Route::post('/profile/storeOrUpdate', [ProfileController::class, 'storeOrUpdateProfile'])->name('profile.storeOrUpdate');
Route::get('/profile/documents', [ProfileController::class, 'showDocuments'])->name('profile.documents');
Route::post('/profile/documents/storeOrUpdate', [ProfileController::class, 'storeOrUpdateDocuments'])->name('profile.documents.storeOrUpdate');
Route::post('/profile/storeOrUpdate', [ProfileController::class, 'storeOrUpdateProfile'])->name('profile.storeOrUpdate');
Route::post('/profile/documents/storeOrUpdate', [ProfileController::class, 'storeOrUpdateDocuments'])->name('profile.documents.storeOrUpdate');
Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.change-password');
Route::put('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
Route::get('/profile/check-completion', [TrainingController::class, 'checkProfileCompletion'])->name('profile.checkCompletion');
Route::get('/profile/preview', [ProfileController::class, 'preview'])->name('profile.preview');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/participants', [ParticipantController::class, 'index'])->name('admin.participant_management');
Route::get('/admin/participant/{id}', [ParticipantController::class, 'show'])->name('participant.show');
Route::delete('/admin/participant/{id}/delete', [ParticipantController::class, 'destroy'])->name('participant.destroy');
Route::get('/admin/trainings', [TrainingController::class, 'index'])->name('admin.training_management');
Route::get('/admin/account-participants', [AdminController::class, 'accountParticipants'])->name('admin.account_participants');
Route::patch('/admin/participant/{id}/change-role', [AdminController::class, 'changeRole'])->name('admin.change_role');

Route::resource('trainings', TrainingController::class);
Route::post('/trainings/{id}/process', [TrainingController::class, 'processRegistration'])->name('trainings.process');
Route::get('/trainings/{id}/register', [TrainingController::class, 'register'])->name('trainings.register');

Route::get('/participants', [ParticipantController::class, 'index'])->name('participant.index');
Route::post('/document/confirm/{id}', [ParticipantController::class, 'confirmDocument'])->name('document.confirm');
// Route untuk view dokumen
Route::post('/document/confirm/{id}/{type}', [ParticipantController::class, 'confirmDocument'])->name('document.confirm');
Route::post('/document/reject/{id}/{type}', [ParticipantController::class, 'rejectDocument'])->name('document.reject');
Route::get('view-document/{type}/{userId}', [ParticipantController::class, 'viewDocument'])->name('view.document');
Route::post('/profile/documents', [ParticipantController::class, 'storeOrUpdateDocuments'])->name('profile.documents.storeOrUpdate');
Route::get('/participants/export', [ParticipantController::class, 'export'])->name('participant.export');
Route::resource('trainings', TrainingController::class);
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/course', [CourseController::class, 'index'])->name('course');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/show/{user}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/admin/trainings', [TrainingController::class, 'index'])->name('trainings.index');
Route::get('/admin/trainings/create', [TrainingController::class, 'create'])->name('trainings.create');
Route::post('/admin/trainings', [TrainingController::class, 'store'])->name('trainings.store');
Route::get('/admin/trainings/{id}/edit', [TrainingController::class, 'edit'])->name('trainings.edit');
Route::put('/admin/trainings/{id}', [TrainingController::class, 'update'])->name('trainings.update');
Route::delete('/admin/trainings/{id}', [TrainingController::class, 'destroy'])->name('trainings.destroy');

Route::get('/admin/trainings', [TrainingController::class, 'index'])->name('admin.training_management');
// Route untuk menghapus peserta
Route::delete('/admin/participants/{id}', [ParticipantController::class, 'destroy'])->name('participant.destroy');

Route::get('/about', function () {
    return view('about');
})->name('about');

//course
Route::get('/courses', function () {
    return view('courses.index');
})->name('courses.index');

Route::get('/courses/barista', function () {
    return view('courses.details.barista');
})->name('courses.barista');

Route::get('/courses/barbershop', function () {
    return view('courses.details.barbershop');
})->name('courses.barbershop');

Route::get('/courses/digital', function () {
    return view('courses.details.digital');
})->name('courses.digital');

Route::get('/courses/fotografi', function () {
    return view('courses.details.fotografi');
})->name('courses.fotografi');

Route::get('/courses/grafis', function () {
    return view('courses.details.grafis');
})->name('courses.grafis');

Route::get('/courses/jaringan', function () {
    return view('courses.details.jaringan');
})->name('courses.jaringan');

Route::get('/courses/kue', function () {
    return view('courses.details.kue');
})->name('courses.kue');

Route::get('/courses/membatik', function () {
    return view('courses.details.membatik');
})->name('courses.membatik');

Route::get('/courses/menjahit', function () {
    return view('courses.details.menjahit');
})->name('courses.menjahit');

Route::get('/courses/service', function () {
    return view('courses.details.service');
})->name('courses.service');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/trainings/{id}/participants', [TrainingController::class, 'showParticipants'])->name('trainings.participants');
// Route untuk preview dan daftar pelatihan
Route::get('/pelatihan/preview', [TrainingController::class, 'preview'])->name('trainings.preview');
Route::post('/pelatihan/register', [TrainingController::class, 'register'])->name('course.register');

// Route untuk menampilkan halaman preview
Route::get('/pelatihan/{id}/preview', [TrainingController::class, 'preview'])->name('pelatihan.preview');
// Route untuk meng-handle pendaftaran
Route::post('/pelatihan/{id}/daftar', [TrainingController::class, 'register'])->name('course.register');

// Route untuk preview pelatihan sebelum daftar
Route::middleware('auth')->group(function () {
    Route::get('/pelatihan/{id}/preview', [TrainingController::class, 'preview'])->name('pelatihan.preview');
    Route::post('/pelatihan/{id}/daftar', [TrainingController::class, 'register'])->name('course.register');
});

Route::post('/course/register', [CourseController::class, 'register'])->name('course.register');

Route::post('/participants/{id}/revisi', [ParticipantController::class, 'sendRevision'])->name('participant.sendRevision');
Route::get('/profile/documents', [ParticipantController::class, 'showDocuments'])->name('profile.documents');

Route::post('/participants/{id}/send-revision', [ParticipantController::class, 'sendRevision'])->name('participant.sendRevision');
Route::post('/documents/{id}/{type}/revision', [ParticipantController::class, 'markAsRevision'])->name('document.revision');

Route::post('/documents/{id}/{type}/revision', [ParticipantController::class, 'markAsRevision'])->name('document.revision');
Route::post('/participant/{id}/send-revision', [ParticipantController::class, 'sendRevision'])->name('participant.sendRevision');

// Route untuk menampilkan halaman dokumen
Route::get('/profile/documents', [ProfileController::class, 'showDocuments'])->name('profile.documents');

// Route untuk menyimpan atau memperbarui dokumen
Route::post('/profile/documents', [ProfileController::class, 'storeOrUpdateDocuments'])->name('profile.documents.storeOrUpdate');
Route::delete('/admin/participants/{user_id}', [ParticipantController::class, 'destroy'])->name('participant.destroy');
Route::get('/training-participants', [TrainingController::class, 'showTrainingParticipants'])->name('training.participants');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/pelatihan/daftar', [TrainingController::class, 'register'])->middleware('profile.complete')->name('pelatihan.daftar');
Route::get('/profile/preview', [ProfileController::class, 'showPreview'])->name('profile.preview');

Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Route::get('/profile/preview', [ProfileController::class, 'showPreview'])->name('profile.preview');
Route::delete('/trainings/{id}', [TrainingController::class, 'destroy'])->name('trainings.destroy');
Route::post('/trainings/{trainingId}/register', [ParticipantController::class, 'registerForTraining'])->name('trainings.register');
