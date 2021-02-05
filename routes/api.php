<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Login API
Route::post('login', 'LoginControllerAPI@login')->name('login');
Route::middleware('auth:api')->post('logout', 'LoginControllerAPI@logout');

// User API
Route::middleware(['auth:api', 'admin'])->get('users', 'UserControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->get('admin/users', 'UserControllerAPI@getAdminUsers');
Route::middleware(['auth:api', 'admin'])->post('users/register', 'UserControllerAPI@registerUser');
Route::middleware(['auth:api'])->get('users/me', 'UserControllerAPI@getAuthenticatedUser');
Route::middleware(['auth:api'])->get('users/{id}', 'UserControllerAPI@show');
Route::post('users', 'UserControllerAPI@store');
Route::put('users/{id}/activate', 'UserControllerAPI@activate');
Route::middleware(['auth:api'])->post('patients', 'UserControllerAPI@getPatients');
Route::middleware(['auth:api', 'professional'])->delete('patients/{id}', 'UserControllerAPI@deletePatient');
Route::middleware(['auth:api', 'admin'])->delete('users/{id}', 'UserControllerAPI@destroy');
Route::middleware(['auth:api', 'admin'])->delete('users/{id}/status', 'UserControllerAPI@toggleActive');
Route::middleware(['auth:api', 'professional'])->put('users/{id}', 'UserControllerAPI@updateProfessional');
Route::middleware(['auth:api', 'professional'])->put('users/profile/{id}', 'UserControllerAPI@updateProfessionalProfile');
Route::middleware(['auth:api', 'admin'])->put('users/terms/{id}', 'UserControllerAPI@updateAcceptanceTerms');
Route::middleware(['auth:api', 'patient'])->post('users/avatar', 'UserControllerAPI@updateAvatar');
Route::middleware(['auth:api', 'patient'])->post('users/profile', 'UserControllerAPI@updatePatientProfile');
Route::middleware(['auth:api', 'patient'])->post('users/diseases', 'UserControllerAPI@updatePatientDiseases');

// ProfessionalCategory API
Route::middleware(['auth:api'])->get('professionalCategories', 'ProfessionalCategoryControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('professionalCategories', 'ProfessionalCategoryControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('professionalCategories/{id}', 'ProfessionalCategoryControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('professionalCategories/{id}', 'ProfessionalCategoryControllerAPI@destroy');

// UFC API
Route::get('ufcs', 'UfcControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('ufcs', 'UfcControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('ufcs/{id}', 'UfcControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('ufcs/{id}', 'UfcControllerAPI@destroy');
Route::middleware(['auth:api'])->get('users/{id}/ufcs', 'UfcControllerAPI@getUserUfcs');

// Reset Password
Route::post('password', 'PasswordResetControllerAPI@store');
Route::put('password/{id}', 'PasswordResetControllerAPI@update');
Route::put('email/{id}', 'PasswordResetControllerAPI@updateEmail');
Route::post('reset-password', 'PasswordResetControllerAPI@reset');

// Medication API
Route::middleware(['auth:api'])->get('medications', 'MedicationControllerAPI@index');
Route::post('medications/{userId}', 'MedicationControllerAPI@store');
Route::middleware(['auth:api'])->delete('medications/{id}', 'MedicationControllerAPI@destroy');
Route::middleware(['auth:api'])->put('medications/{id}', 'MedicationControllerAPI@update');
Route::middleware(['auth:api'])->get('medications/{id}', 'MedicationControllerAPI@getMedicationByUser');
Route::middleware(['auth:api'])->get('medication/{id}', 'MedicationControllerAPI@getMedication');
Route::middleware(['auth:api', 'patient'])->get('medication/auth', 'MedicationControllerAPI@m');

// Acceptance Terms API
Route::get('terms', 'AcceptanceTermsControllerAPI@getTerms');
Route::middleware(['auth:api', 'admin'])->put('terms/{id}', 'AcceptanceTermsControllerAPI@update');

// Diseases API
Route::get('diseases', 'DiseaseControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('diseases', 'DiseaseControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('diseases/{id}', 'DiseaseControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('diseases/{id}', 'DiseaseControllerAPI@destroy');

// Meals API
Route::middleware(['auth:api', 'patient'])->get('meals-user', 'MealControllerAPI@getAuthUserMeals');
Route::middleware(['auth:api'])->get('meals', 'MealControllerAPI@index');
Route::middleware(['auth:api'])->get('meals/{id}', 'MealControllerAPI@show');
Route::middleware(['auth:api', 'patient'])->post('meals/{id}', 'MealControllerAPI@store');
Route::middleware(['auth:api', 'patient'])->delete('meals/{id}', 'MealControllerAPI@destroy');
Route::middleware(['auth:api', 'patient'])->post('meals/{id}/photo', 'MealControllerAPI@updateMealImage');
Route::middleware(['auth:api', 'patient'])->put('meals-update/{id}', 'MealControllerAPI@update');
Route::middleware(['auth:api', 'intern'])->put('meals/{id}', 'MealControllerAPI@updateQuantity');
Route::middleware(['auth:api', 'intern'])->get('meals/{id}/user', 'MealControllerAPI@getMealsByUser');
Route::middleware(['auth:api', 'intern'])->get('meals/{id}/nutritional', 'MealControllerAPI@getNutritionalInfoByUser');

// Static nutritional info
Route::get('meal-names', 'NutritionalInfoStaticControllerAPI@getNames');

// Nutritional info
Route::middleware(['auth:api', 'professional'])->put('nutritional-info/{id}', 'NutritionalInfoControllerAPI@update');
Route::middleware(['auth:api', 'professional'])->put('nutrititional-info/meal/{id}', 'NutritionalInfoControllerAPI@updateByMeal');

// Sleep API
Route::middleware(['auth:api', 'patient'])->post('sleeps', 'SleepControllerAPI@store');
Route::middleware(['auth:api', 'patient'])->get('sleepsByDate', 'SleepControllerAPI@getSleepDates');
Route::middleware(['auth:api', 'patient'])->get('sleeps/myStats', 'SleepControllerAPI@getSleepStatsForAuthUser');
Route::middleware(['auth:api', 'intern'])->get('sleeps/export', 'SleepControllerAPI@export');
Route::middleware(['auth:api', 'intern'])->get('sleeps/{id}', 'SleepControllerAPI@show');
Route::middleware(['auth:api', 'intern'])->get('sleeps/stats/{id}', 'SleepControllerAPI@getSleepStatsByUser');

// Mobile Stats
Route::middleware(['auth:api', 'patient'])->get('/stats', 'MobileStatsControllerAPI@getStats');

// SleepTips API
Route::middleware(['auth:api'])->get('tips', 'SleepTipControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('tips', 'SleepTipControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('tips/{id}', 'SleepTipControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('tips/{id}', 'SleepTipControllerAPI@destroy');

//Configurations API
Route::middleware(['auth:api'])->get('configs/tips', 'ConfigurationControllerAPI@getTipStatus');
Route::middleware(['auth:api', 'admin'])->get('configs', 'ConfigurationControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->put('configs/{id}', 'ConfigurationControllerAPI@update');

//Messages API
Route::middleware(['auth:api', 'professional'])->get('messages/unread', 'MessageControllerAPI@getUnreadMessagesForAuthUser');
Route::middleware(['auth:api'])->get('messages/unread-count', 'MessageControllerAPI@countUnreadMessagesForAuthUser');
Route::middleware(['auth:api', 'professional'])->put('messages/read/{id}', 'MessageControllerAPI@markAsRead');
Route::middleware(['auth:api', 'professional'])->delete('messages/{id}', 'MessageControllerAPI@destroy');
Route::middleware(['auth:api', 'professional'])->put('messages/{id}', 'MessageControllerAPI@update');
Route::middleware(['auth:api'])->post('messages', 'MessageControllerAPI@store');
Route::middleware(['auth:api', 'professional'])->get('messages', 'MessageControllerAPI@messagesHistory');

Route::middleware(['auth:api', 'patient'])->get('/professionalsByUsf/{id}', 'UserControllerAPI@getProfessionalByUsf');
Route::middleware(['auth:api', 'patient'])->get('/messagesFromUser/{id}', 'MessageControllerAPI@getMessagesFromUser');
