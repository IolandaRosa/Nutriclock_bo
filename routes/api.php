<?php

use Illuminate\Http\Request;
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
Route::middleware(['auth:api','admin'])->get('users', 'UserControllerAPI@index');
Route::middleware(['auth:api','admin'])->get('admin/users', 'UserControllerAPI@getAdminUsers');
Route::middleware(['auth:api','admin'])->post('users/register', 'UserControllerAPI@registerUser');
Route::middleware(['auth:api'])->get('users/me', 'UserControllerAPI@getAuthenticatedUser');
Route::middleware(['auth:api'])->get('users/{id}', 'UserControllerAPI@show');
Route::post('users', 'UserControllerAPI@store');
Route::put('users/{id}/activate', 'UserControllerAPI@activate');
Route::middleware(['auth:api'])->post('patients', 'UserControllerAPI@getPatients');
Route::middleware(['auth:api','admin'])->delete('users/{id}', 'UserControllerAPI@destroy');
Route::middleware(['auth:api','admin'])->delete('users/{id}/status', 'UserControllerAPI@toggleActive');
Route::middleware(['auth:api'])->put('users/{id}', 'UserControllerAPI@updateProfessional');
Route::middleware(['auth:api'])->put('users/profile/{id}', 'UserControllerAPI@updateProfessionalProfile');
Route::middleware(['auth:api'])->put('users/terms/{id}', 'UserControllerAPI@updateAcceptanceTerms');

// ProfessionalCategory API
Route::middleware(['auth:api'])->get('professionalCategories', 'ProfessionalCategoryControllerAPI@index');
Route::middleware(['auth:api','admin'])->post('professionalCategories', 'ProfessionalCategoryControllerAPI@store');
Route::middleware(['auth:api','admin'])->put('professionalCategories/{id}', 'ProfessionalCategoryControllerAPI@update');
Route::middleware(['auth:api','admin'])->delete('professionalCategories/{id}', 'ProfessionalCategoryControllerAPI@destroy');

// UFC API
Route::get('ufcs', 'UfcControllerAPI@index');
Route::middleware(['auth:api','admin'])->post('ufcs', 'UfcControllerAPI@store');
Route::middleware(['auth:api','admin'])->put('ufcs/{id}', 'UfcControllerAPI@update');
Route::middleware(['auth:api','admin'])->delete('ufcs/{id}', 'UfcControllerAPI@destroy');
Route::middleware(['auth:api'])->get('users/{id}/ufcs', 'UfcControllerAPI@getUserUfcs');

// Reset Password
Route::post('password', 'PasswordResetControllerAPI@store');
Route::put('password/{id}', 'PasswordResetControllerAPI@update');
Route::post('reset-password', 'PasswordResetControllerAPI@reset');

// Medication API
Route::middleware(['auth:api'])->get('medications', 'MedicationControllerAPI@index');
Route::post('medications/{userId}', 'MedicationControllerAPI@store');
Route::middleware(['auth:api'])->delete('medications/{id}', 'MedicationControllerAPI@destroy');
Route::middleware(['auth:api'])->put('medications/{id}', 'MedicationControllerAPI@update');
Route::middleware(['auth:api'])->get('medications/{id}', 'MedicationControllerAPI@getMedicationByUser');
Route::middleware(['auth:api'])->get('medication/{id}', 'MedicationControllerAPI@getMedication');

// Acceptance Terms API
Route::get('terms', 'AcceptanceTermsControllerAPI@getTerms');
Route::middleware(['auth:api','admin'])->put('terms/{id}', 'AcceptanceTermsControllerAPI@update');

// Diseases API
Route::get('diseases', 'DiseaseControllerAPI@index');
Route::middleware(['auth:api','admin'])->post('diseases', 'DiseaseControllerAPI@store');
Route::middleware(['auth:api','admin'])->put('diseases/{id}', 'DiseaseControllerAPI@update');
Route::middleware(['auth:api','admin'])->delete('diseases/{id}', 'DiseaseControllerAPI@destroy');

// Meals API
Route::middleware(['auth:api'])->get('meals', 'MealControllerAPI@index');
Route::middleware(['auth:api'])->get('meals/{id}', 'MealControllerAPI@show');
Route::middleware(['auth:api'])->post('meals/{id}', 'MealControllerAPI@store');
Route::middleware(['auth:api'])->get('meals/{id}/user', 'MealControllerAPI@getMealsByUser');
Route::middleware(['auth:api'])->get('meals-user', 'MealControllerAPI@getAuthUserMeals');
Route::middleware(['auth:api'])->get('meals/stats/{id}', 'MealControllerAPI@mealDaysCount');

// Static nutritional info
Route::get('meal-names', 'NutritionalInfoStaticControllerAPI@getNames');
