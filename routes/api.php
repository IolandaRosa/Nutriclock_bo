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
Route::middleware(['auth:api', 'intern'])->post('patients', 'UserControllerAPI@getPatients');
Route::middleware(['auth:api', 'professional'])->delete('patients/{id}', 'UserControllerAPI@deletePatient');
Route::middleware(['auth:api', 'admin'])->delete('users/{id}', 'UserControllerAPI@destroy');
Route::middleware(['auth:api', 'admin'])->delete('users/{id}/status', 'UserControllerAPI@toggleActive');
Route::middleware(['auth:api', 'professional'])->put('users/{id}', 'UserControllerAPI@updateProfessional');
Route::middleware(['auth:api', 'intern'])->put('users/profile/{id}', 'UserControllerAPI@updateProfessionalProfile');
Route::middleware(['auth:api', 'admin'])->put('users/terms/{id}', 'UserControllerAPI@updateAcceptanceTerms');
Route::middleware(['auth:api', 'patient'])->post('users/avatar', 'UserControllerAPI@updateAvatar');
Route::middleware(['auth:api', 'patient'])->post('users/profile', 'UserControllerAPI@updatePatientProfile');
Route::middleware(['auth:api', 'patient'])->post('users/diseases', 'UserControllerAPI@updatePatientDiseases');
Route::middleware(['auth:api', 'patient'])->get('/professionalsByUsf/{id}', 'UserControllerAPI@getProfessionalByUsf');
Route::middleware(['auth:api', 'patient'])->get('forgot-me', 'UserControllerAPI@forgetUserData');
Route::middleware(['auth:api', 'admin'])->get('forgot-me-count', 'UserControllerAPI@countForgetUserData');
Route::middleware(['auth:api', 'admin'])->get('undo-forgot/{id}', 'UserControllerAPI@undoForgot');
Route::middleware(['auth:api', 'patient'])->post('fcm', 'UserControllerAPI@fcmToken');
Route::middleware(['auth:api', 'professional'])->put('users/{id}/nutriclock-group', 'UserControllerAPI@updateNutriclockGroup');

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
Route::middleware(['auth:api', 'intern'])->get('ufcs/auth/description', 'UfcControllerAPI@getUserUfcsName');

// Reset Password
Route::post('password', 'PasswordResetControllerAPI@store');
Route::middleware(['auth:api'])->put('password/{id}', 'PasswordResetControllerAPI@update');
Route::middleware(['auth:api'])->put('email/{id}', 'PasswordResetControllerAPI@updateEmail');
Route::post('reset-password', 'PasswordResetControllerAPI@reset');

// Medication API
Route::middleware(['auth:api'])->get('medications', 'MedicationControllerAPI@index');
Route::post('medications/{userId}', 'MedicationControllerAPI@store');
Route::middleware(['auth:api'])->delete('medications/{id}', 'MedicationControllerAPI@destroy');
Route::middleware(['auth:api'])->put('medications/{id}', 'MedicationControllerAPI@update');
Route::middleware(['auth:api'])->get('medications/{id}', 'MedicationControllerAPI@getMedicationByUser');
Route::middleware(['auth:api'])->get('medication/{id}', 'MedicationControllerAPI@getMedication');
Route::middleware(['auth:api', 'patient'])->get('medications-auth', 'MedicationControllerAPI@getAuthMedication');
Route::middleware(['auth:api', 'patient'])->post('medications-auth', 'MedicationControllerAPI@storeAuth');

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
Route::middleware(['auth:api', 'patient'])->post('meals', 'MealControllerAPI@store');
Route::middleware(['auth:api', 'patient'])->delete('meals/{id}', 'MealControllerAPI@destroy');
Route::middleware(['auth:api', 'patient'])->post('meals/{id}/photo', 'MealControllerAPI@updateMealImage');
Route::middleware(['auth:api', 'patient'])->put('meals-update/{id}', 'MealControllerAPI@update');
Route::middleware(['auth:api', 'intern'])->put('meals/{id}', 'MealControllerAPI@updateQuantity');
Route::middleware(['auth:api', 'intern'])->get('meals/{id}/user', 'MealControllerAPI@getMealsByUser');
Route::middleware(['auth:api', 'intern'])->get('meals/{id}/nutritional', 'MealControllerAPI@getNutritionalInfoByUser');

// Static nutritional info
Route::get('meal-names', 'NutritionalInfoStaticControllerAPI@getNames');
Route::middleware(['auth:api', 'professional'])->get('meals-query/{query}', 'NutritionalInfoStaticControllerAPI@getByQuery');
Route::middleware(['auth:api', 'admin'])->get('nutritional-info-static', 'NutritionalInfoStaticControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('nutritional-info-static', 'NutritionalInfoStaticControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->delete('nutritional-info-static/{code}', 'NutritionalInfoStaticControllerAPI@destroy');
Route::middleware(['auth:api', 'admin'])->put('nutritional-info-static/{code}', 'NutritionalInfoStaticControllerAPI@update');

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
Route::middleware(['auth:api', 'patient'])->get('stats', 'MobileStatsControllerAPI@getStats');
Route::middleware(['auth:api', 'intern'])->get('data-status/{id}', 'MobileStatsControllerAPI@getUserFrequency');
Route::middleware(['auth:api', 'patient'])->get('reports', 'MobileStatsControllerAPI@getReport');

// SleepTips API
Route::middleware(['auth:api'])->get('tips', 'SleepTipControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('tips', 'SleepTipControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('tips/{id}', 'SleepTipControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('tips/{id}', 'SleepTipControllerAPI@destroy');

//Configurations API
Route::middleware(['auth:api'])->get('configs/tips', 'ConfigurationControllerAPI@getTipStatus');
Route::middleware(['auth:api'])->get('configs/contacts', 'ConfigurationControllerAPI@getContacts');
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
Route::middleware(['auth:api', 'patient'])->get('messagesFromUser/{id}', 'MessageControllerAPI@getMessagesFromUser');

// Exercise API
Route::middleware(['auth:api', 'patient'])->post('/exercises', 'ExerciseControllerAPI@store');
Route::middleware(['auth:api', 'patient'])->get('/exercises/dates','ExerciseControllerAPI@getExerciseDates');
Route::middleware(['auth:api', 'patient'])->get('/exercises/detail/{date}','ExerciseControllerAPI@getExerciseByDate');
Route::middleware(['auth:api', 'patient'])->get('/exercises/stats','ExerciseControllerAPI@getExerciseStats');
Route::middleware(['auth:api', 'intern'])->get('/exercises/admin/{id}','ExerciseControllerAPI@getExerciseByUser');
Route::middleware(['auth:api', 'intern'])->get('/exercises/admin/stats/{id}','ExerciseControllerAPI@getStatsByUser');

// Exercises Static API
Route::middleware(['auth:api'])->get('/exercises/list', 'ExerciseStaticControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('/exercises-static', 'ExerciseStaticControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('/exercises-static/{id}', 'ExerciseStaticControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('/exercises-static/{id}', 'ExerciseStaticControllerAPI@destroy');

// Household static API
Route::middleware(['auth:api'])->get('/households', 'HouseholdStaticControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('/households', 'HouseholdStaticControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->put('/households/{id}', 'HouseholdStaticControllerAPI@update');
Route::middleware(['auth:api', 'admin'])->delete('/households/{id}', 'HouseholdStaticControllerAPI@destroy');

// Meal Plan API
Route::middleware(['auth:api', 'intern'])->get('/meal-type-stats/{id}','MealPlanTypeControllerAPI@statsByPlanDay');
Route::middleware(['auth:api', 'professional'])->post('/meal-type-stats','MealPlanTypeControllerAPI@statsMealType');
Route::middleware(['auth:api', 'professional'])->post('/meal-plans','MealPlanTypeControllerAPI@store');
Route::middleware(['auth:api', 'professional'])->post('/meal-type/{id}','MealPlanTypeControllerAPI@storeMealType');
Route::middleware(['auth:api', 'intern'])->get('/meal-plans-dates/{id}', 'MealPlanTypeControllerAPI@getMealPlanDates');
Route::middleware(['auth:api', 'intern'])->get('/meal-plans/{id}/{date}','MealPlanTypeControllerAPI@show');
Route::middleware(['auth:api', 'professional'])->delete('/meal-type/{id}','MealPlanTypeControllerAPI@destroy');
Route::middleware(['auth:api', 'patient'])->get('/meal-types-patient/{date}', 'MealPlanTypeControllerAPI@getPatientDailyPlan');
Route::middleware(['auth:api', 'patient'])->get('/meal-history-patient/{date}', 'MealPlanTypeControllerAPI@getPatientHistory');
Route::middleware(['auth:api', 'patient'])->post('meal-plan-type-confirm/{id}', 'MealPlanTypeControllerAPI@confirmAuthMealPlanType');

// Ingredient API
Route::middleware(['auth:api', 'professional'])->delete('/ingredient/{id}','IngredientControllerAPI@destroy');
Route::middleware(['auth:api', 'professional'])->post('/ingredient/{id}','IngredientControllerAPI@store');

// Notification API
Route::middleware(['auth:api', 'patient'])->post('/notifications', 'NotificationControllerAPI@store');
Route::middleware(['auth:api', 'patient'])->get('/notifications', 'NotificationControllerAPI@show');

// Biometric Collection API
Route::middleware(['auth:api'])->get('/biometric-collection', 'BiometricCollectionControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('/biometric-collection', 'BiometricCollectionControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->delete('/biometric-collection/{id}', 'BiometricCollectionControllerAPI@destroy');
Route::middleware(['auth:api', 'admin'])->get('/biometric-collection-up/{id}', 'BiometricCollectionControllerAPI@movesUp');
Route::middleware(['auth:api', 'admin'])->get('/biometric-collection-down/{id}', 'BiometricCollectionControllerAPI@movesDown');

// Biometric Procedure API
Route::middleware(['auth:api'])->get('/biometric-procedure', 'BiometricProcedureControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->post('/biometric-procedure', 'BiometricProcedureControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->post('/biometric-group', 'BiometricGroupControllerAPI@store');
Route::middleware(['auth:api', 'admin'])->delete('/biometric-procedure/{id}', 'BiometricProcedureControllerAPI@destroy');
Route::middleware(['auth:api', 'admin'])->get('/biometric-procedure-up/{id}', 'BiometricProcedureControllerAPI@movesUp');
Route::middleware(['auth:api', 'admin'])->get('/biometric-procedure-down/{id}', 'BiometricProcedureControllerAPI@movesDown');
Route::middleware(['auth:api', 'admin'])->delete('/biometric-group/{id}', 'BiometricGroupControllerAPI@destroy');
Route::middleware(['auth:api', 'admin'])->post('/biometric-group-user-add', 'BiometricGroupControllerAPI@addUserToGroup');
Route::middleware(['auth:api', 'admin'])->post('/biometric-group-user-remove', 'BiometricGroupControllerAPI@removeUserFromGroup');
Route::middleware(['auth:api', 'admin'])->post('/biometric-group-biometric-collection-remove', 'BiometricGroupControllerAPI@removeBiometricCollectionToGroup');
Route::middleware(['auth:api', 'admin'])->get('/biometric-group', 'BiometricGroupControllerAPI@index');
Route::middleware(['auth:api', 'admin'])->get('/biometric-group/users/{id}', 'BiometricGroupControllerAPI@usersByBiometricGroup');

// Evaluation API
Route::middleware(['auth:api', 'patient'])->post('evaluation', 'EvaluationControllerAPI@store');
Route::middleware(['auth:api', 'patient'])->get('has-evaluation', 'EvaluationControllerAPI@getUserEvaluation');
Route::middleware(['auth:api', 'admin'])->get('average-evaluation', 'EvaluationControllerAPI@getAverageEvaluation');
