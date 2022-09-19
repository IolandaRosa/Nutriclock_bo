<?php

use App\Http\Controllers\AcceptanceTermsControllerAPI;
use App\Http\Controllers\BiometricCollectionControllerAPI;
use App\Http\Controllers\BiometricProcedureControllerAPI;
use App\Http\Controllers\ConfigurationControllerAPI;
use App\Http\Controllers\DiseaseControllerAPI;
use App\Http\Controllers\EvaluationControllerAPI;
use App\Http\Controllers\ExerciseControllerAPI;
use App\Http\Controllers\ExerciseStaticControllerAPI;
use App\Http\Controllers\HouseholdStaticControllerAPI;
use App\Http\Controllers\IngredientControllerAPI;
use App\Http\Controllers\LoginControllerAPI;
use App\Http\Controllers\MealControllerAPI;
use App\Http\Controllers\MealPlanTypeControllerAPI;
use App\Http\Controllers\MedicationControllerAPI;
use App\Http\Controllers\MessageControllerAPI;
use App\Http\Controllers\MobileStatsControllerAPI;
use App\Http\Controllers\NotificationControllerAPI;
use App\Http\Controllers\NutritionalInfoControllerAPI;
use App\Http\Controllers\NutritionalInfoStaticControllerAPI;
use App\Http\Controllers\PasswordResetControllerAPI;
use App\Http\Controllers\ProfessionalCategoryControllerAPI;
use App\Http\Controllers\SleepControllerAPI;
use App\Http\Controllers\SleepTipControllerAPI;
use App\Http\Controllers\UfcControllerAPI;
use App\Http\Controllers\UserControllerAPI;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Login API
Route::post('login', [LoginControllerAPI::class, 'login'])->name('login');
Route::middleware('auth:api')->post('logout',  [LoginControllerAPI::class, 'logout']);

// User API
Route::middleware(['auth:api', 'admin'])->get('users', [UserControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->get('admin/users', [UserControllerAPI::class, 'getAdminUsers']);
Route::middleware(['auth:api', 'admin'])->post('users/register', [UserControllerAPI::class, 'registerUser']);
Route::middleware(['auth:api'])->get('users/me', [UserControllerAPI::class, 'getAuthenticatedUser']);
Route::middleware(['auth:api'])->get('users/{id}', [UserControllerAPI::class, 'show']);
Route::post('users', [UserControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'intern'])->post('patients', [UserControllerAPI::class, 'getPatients']);
Route::middleware(['auth:api', 'professional'])->delete('patients/{id}', [UserControllerAPI::class, 'deletePatient']);
Route::middleware(['auth:api', 'admin'])->delete('users/{id}', [UserControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'admin'])->delete('users/{id}/status', [UserControllerAPI::class, 'toggleActive']);
Route::middleware(['auth:api', 'professional'])->put('users/{id}', [UserControllerAPI::class, 'updateProfessional']);
Route::middleware(['auth:api', 'intern'])->put('users/profile/{id}', [UserControllerAPI::class, 'updateProfessionalProfile']);
Route::middleware(['auth:api', 'admin'])->put('users/terms/{id}', [UserControllerAPI::class, 'updateAcceptanceTerms']);
Route::middleware(['auth:api', 'patient'])->post('users/avatar', [UserControllerAPI::class, 'updateAvatar']);
Route::middleware(['auth:api', 'patient'])->post('users/profile', [UserControllerAPI::class, 'updatePatientProfile']);
Route::middleware(['auth:api', 'patient'])->post('users/diseases', [UserControllerAPI::class, 'updatePatientDiseases']);
Route::middleware(['auth:api', 'patient'])->get('/professionalsByUsf/{id}', [UserControllerAPI::class, 'getProfessionalByUsf']);
Route::middleware(['auth:api', 'patient'])->get('forgot-me', [UserControllerAPI::class, 'forgetUserData']);
Route::middleware(['auth:api', 'admin'])->get('forgot-me-count', [UserControllerAPI::class, 'countForgetUserData']);
Route::middleware(['auth:api', 'admin'])->get('undo-forgot/{id}', [UserControllerAPI::class, 'undoForgot']);
Route::middleware(['auth:api', 'patient'])->post('fcm', [UserControllerAPI::class, 'fcmToken']);
Route::middleware(['auth:api', 'professional'])->put('users/{id}/nutriclock-group', [UserControllerAPI::class, 'updateNutriclockGroup']);

// ProfessionalCategory API
Route::middleware(['auth:api'])->get('professionalCategories', [ProfessionalCategoryControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('professionalCategories', [ProfessionalCategoryControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->put('professionalCategories/{id}', [ProfessionalCategoryControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'admin'])->delete('professionalCategories/{id}', [ProfessionalCategoryControllerAPI::class, 'destroy']);

// UFC API
Route::get('ufcs', [UfcControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('ufcs', [UfcControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->put('ufcs/{id}', [UfcControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'admin'])->delete('ufcs/{id}', [UfcControllerAPI::class, 'destroy']);
Route::middleware(['auth:api'])->get('users/{id}/ufcs', [UfcControllerAPI::class, 'getUserUfcs']);
Route::middleware(['auth:api', 'intern'])->get('ufcs/auth/description', [UfcControllerAPI::class, 'getUserUfcsName']);

// Reset Password
Route::post('password', [PasswordResetControllerAPI::class, 'store']);
Route::middleware(['auth:api'])->put('password/{id}', [PasswordResetControllerAPI::class, 'update']);
Route::middleware(['auth:api'])->put('email/{id}', [PasswordResetControllerAPI::class, 'updateEmail']);
Route::post('reset-password', [PasswordResetControllerAPI::class, 'reset']);

// Medication API
Route::middleware(['auth:api'])->get('medications', [MedicationControllerAPI::class, 'index']);
Route::post('medications/{userId}', [MedicationControllerAPI::class, 'store']);
Route::middleware(['auth:api'])->delete('medications/{id}', [MedicationControllerAPI::class, 'destroy']);
Route::middleware(['auth:api'])->put('medications/{id}', [MedicationControllerAPI::class, 'update']);
Route::middleware(['auth:api'])->get('medications/{id}', [MedicationControllerAPI::class, 'getMedicationByUser']);
Route::middleware(['auth:api'])->get('medication/{id}', [MedicationControllerAPI::class, 'getMedication']);
Route::middleware(['auth:api', 'patient'])->get('medications-auth', [MedicationControllerAPI::class, 'getAuthMedication']);
Route::middleware(['auth:api', 'patient'])->post('medications-auth', [MedicationControllerAPI::class, 'storeAuth']);

// Acceptance Terms API
Route::get('terms', [AcceptanceTermsControllerAPI::class, 'getTerms']);
Route::middleware(['auth:api', 'admin'])->put('terms/{id}', [AcceptanceTermsControllerAPI::class, 'update']);

// Diseases API
Route::get('diseases', [DiseaseControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('diseases', [DiseaseControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->put('diseases/{id}', [DiseaseControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'admin'])->delete('diseases/{id}', [DiseaseControllerAPI::class, 'destroy']);

// Meals API
Route::middleware(['auth:api', 'patient'])->get('meals-user', [MealControllerAPI::class, 'getAuthUserMeals']);
Route::middleware(['auth:api'])->get('meals', [MealControllerAPI::class, 'index']);
Route::middleware(['auth:api'])->get('meals/{id}', [MealControllerAPI::class, 'show']);
Route::middleware(['auth:api', 'patient'])->post('meals', [MealControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'patient'])->delete('meals/{id}', [MealControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'patient'])->post('meals/{id}/photo', [MealControllerAPI::class, 'updateMealImage']);
Route::middleware(['auth:api', 'patient'])->put('meals-update/{id}', [MealControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'intern'])->put('meals/{id}', [MealControllerAPI::class, 'updateQuantity']);
Route::middleware(['auth:api', 'intern'])->get('meals/{id}/user', [MealControllerAPI::class, 'getMealsByUser']);
Route::middleware(['auth:api', 'intern'])->get('meals/{id}/nutritional', [MealControllerAPI::class, 'getNutritionalInfoByUser']);

// Static nutritional info
Route::get('meal-names', [NutritionalInfoStaticControllerAPI::class, 'getNames']);
Route::middleware(['auth:api', 'professional'])->get('meals-query/{query}', [NutritionalInfoStaticControllerAPI::class, 'getByQuery']);
Route::middleware(['auth:api', 'admin'])->get('nutritional-info-static', [NutritionalInfoStaticControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('nutritional-info-static', [NutritionalInfoStaticControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->delete('nutritional-info-static/{code}', [NutritionalInfoStaticControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'admin'])->put('nutritional-info-static/{code}', [NutritionalInfoStaticControllerAPI::class, 'update']);

// Nutritional info
Route::middleware(['auth:api', 'professional'])->put('nutritional-info/{id}', [NutritionalInfoControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'professional'])->put('nutrititional-info/meal/{id}', [NutritionalInfoStaticControllerAPI::class, 'updateByMeal']);

// Sleep API
Route::middleware(['auth:api', 'patient'])->post('sleeps', [SleepControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'patient'])->get('sleepsByDate', [SleepControllerAPI::class, 'getSleepDates']);
Route::middleware(['auth:api', 'patient'])->get('sleeps/myStats', [SleepControllerAPI::class, 'getSleepStatsForAuthUser']);
Route::middleware(['auth:api', 'intern'])->get('sleeps/export', [SleepControllerAPI::class, 'export']);
Route::middleware(['auth:api', 'intern'])->get('sleeps/{id}', [SleepControllerAPI::class, 'show']);
Route::middleware(['auth:api', 'intern'])->get('sleeps/stats/{id}', [SleepControllerAPI::class, 'getSleepStatsByUser']);

// Mobile Stats
Route::middleware(['auth:api', 'patient'])->get('stats', [MobileStatsControllerAPI::class, 'getStats']);
Route::middleware(['auth:api', 'intern'])->get('data-status/{id}', [MobileStatsControllerAPI::class, 'getUserFrequency']);
Route::middleware(['auth:api', 'patient'])->get('reports', [MobileStatsControllerAPI::class, 'getReport']);

// SleepTips API
Route::middleware(['auth:api'])->get('tips', [SleepTipControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('tips', [SleepTipControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->put('tips/{id}', [SleepTipControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'admin'])->delete('tips/{id}', [SleepTipControllerAPI::class, 'destroy']);

//Configurations API
Route::middleware(['auth:api'])->get('configs/tips', [ConfigurationControllerAPI::class, 'getTipStatus']);
Route::middleware(['auth:api'])->get('configs/contacts', [ConfigurationControllerAPI::class, 'getContacts']);
Route::middleware(['auth:api', 'admin'])->get('configs', [ConfigurationControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->put('configs/{id}', [ConfigurationControllerAPI::class, 'update']);

//Messages API
Route::middleware(['auth:api', 'professional'])->get('messages/unread', [MessageControllerAPI::class, 'getUnreadMessagesForAuthUser']);
Route::middleware(['auth:api'])->get('messages/unread-count', [MessageControllerAPI::class, 'countUnreadMessagesForAuthUser']);
Route::middleware(['auth:api', 'professional'])->put('messages/read/{id}', [MessageControllerAPI::class, 'markAsRead']);
Route::middleware(['auth:api', 'professional'])->delete('messages/{id}', [MessageControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'professional'])->put('messages/{id}', [MessageControllerAPI::class, 'update']);
Route::middleware(['auth:api'])->post('messages', [MessageControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'professional'])->get('messages', [MessageControllerAPI::class, 'messagesHistory']);
Route::middleware(['auth:api', 'patient'])->get('messagesFromUser/{id}', [MessageControllerAPI::class, 'getMessagesFromUser']);

// Exercise API
Route::middleware(['auth:api', 'patient'])->post('/exercises', [ExerciseControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'patient'])->get('/exercises/dates', [ExerciseControllerAPI::class, 'getExerciseDates']);
Route::middleware(['auth:api', 'patient'])->get('/exercises/detail/{date}', [ExerciseControllerAPI::class, 'getExerciseByDate']);
Route::middleware(['auth:api', 'patient'])->get('/exercises/stats', [ExerciseControllerAPI::class, 'getExerciseStats']);
Route::middleware(['auth:api', 'intern'])->get('/exercises/admin/{id}', [ExerciseControllerAPI::class, 'getExerciseByUser']);
Route::middleware(['auth:api', 'intern'])->get('/exercises/admin/stats/{id}', [ExerciseControllerAPI::class, 'getStatsByUser']);

// Exercises Static API
Route::middleware(['auth:api'])->get('/exercises/list', [ExerciseStaticControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('/exercises-static', [ExerciseStaticControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->put('/exercises-static/{id}', [ExerciseStaticControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'admin'])->delete('/exercises-static/{id}', [ExerciseStaticControllerAPI::class, 'destroy']);

// Household static API
Route::middleware(['auth:api'])->get('/households', [HouseholdStaticControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('/households', [HouseholdStaticControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->put('/households/{id}', [HouseholdStaticControllerAPI::class, 'update']);
Route::middleware(['auth:api', 'admin'])->delete('/households/{id}', [HouseholdStaticControllerAPI::class, 'destroy']);

// Meal Plan API
Route::middleware(['auth:api', 'intern'])->get('/meal-type-stats/{id}', [MealPlanTypeControllerAPI::class, 'statsByPlanDay']);
Route::middleware(['auth:api', 'professional'])->post('/meal-type-stats', [MealPlanTypeControllerAPI::class, 'statsMealType']);
Route::middleware(['auth:api', 'professional'])->post('/meal-plans', [MealPlanTypeControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'professional'])->post('/meal-type/{id}', [MealPlanTypeControllerAPI::class, 'storeMealType']);
Route::middleware(['auth:api', 'intern'])->get('/meal-plans-dates/{id}', [MealPlanTypeControllerAPI::class, 'getMealPlanDates']);
Route::middleware(['auth:api', 'intern'])->get('/meal-plans/{id}/{date}', [MealPlanTypeControllerAPI::class, 'show']);
Route::middleware(['auth:api', 'professional'])->delete('/meal-type/{id}', [MealPlanTypeControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'patient'])->get('/meal-types-patient/{date}', [MealPlanTypeControllerAPI::class, 'getPatientDailyPlan']);
Route::middleware(['auth:api', 'patient'])->get('/meal-history-patient/{date}', [MealPlanTypeControllerAPI::class, 'getPatientHistory']);
Route::middleware(['auth:api', 'patient'])->post('meal-plan-type-confirm/{id}', [MealPlanTypeControllerAPI::class, 'confirmAuthMealPlanType']);

// Ingredient API
Route::middleware(['auth:api', 'professional'])->delete('/ingredient/{id}', [IngredientControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'professional'])->post('/ingredient/{id}', [IngredientControllerAPI::class, 'store']);

// Notification API
Route::middleware(['auth:api', 'patient'])->post('/notifications', [NotificationControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'patient'])->get('/notifications', [NotificationControllerAPI::class, 'show']);

// Biometric Collection API
Route::middleware(['auth:api'])->get('/biometric-collection', [BiometricCollectionControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('/biometric-collection', [BiometricCollectionControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->delete('/biometric-collection/{id}', [BiometricCollectionControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'admin'])->get('/biometric-collection-up/{id}', [BiometricCollectionControllerAPI::class, 'movesUp']);
Route::middleware(['auth:api', 'admin'])->get('/biometric-collection-down/{id}', [BiometricCollectionControllerAPI::class, 'movesDown']);

// Biometric Procedure API
Route::middleware(['auth:api'])->get('/biometric-procedure', [BiometricProcedureControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->post('/biometric-procedure', [BiometricProcedureControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->post('/biometric-group', [BiometricProcedureControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'admin'])->delete('/biometric-procedure/{id}', [BiometricProcedureControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'admin'])->get('/biometric-procedure-up/{id}', [BiometricProcedureControllerAPI::class, 'movesUp']);
Route::middleware(['auth:api', 'admin'])->get('/biometric-procedure-down/{id}', [BiometricProcedureControllerAPI::class, 'movesDown']);
Route::middleware(['auth:api', 'admin'])->delete('/biometric-group/{id}', [BiometricProcedureControllerAPI::class, 'destroy']);
Route::middleware(['auth:api', 'admin'])->post('/biometric-group-user-add', [BiometricProcedureControllerAPI::class, 'addUserToGroup']);
Route::middleware(['auth:api', 'admin'])->post('/biometric-group-user-remove', [BiometricProcedureControllerAPI::class, 'removeUserFromGroup']);
Route::middleware(['auth:api', 'admin'])->post('/biometric-group-biometric-collection-remove', [BiometricProcedureControllerAPI::class, 'removeBiometricCollectionToGroup']);
Route::middleware(['auth:api', 'admin'])->get('/biometric-group', [BiometricProcedureControllerAPI::class, 'index']);
Route::middleware(['auth:api', 'admin'])->get('/biometric-group/users/{id}', [BiometricProcedureControllerAPI::class, 'usersByBiometricGroup']);

// Evaluation API
Route::middleware(['auth:api', 'patient'])->post('evaluation', [EvaluationControllerAPI::class, 'store']);
Route::middleware(['auth:api', 'patient'])->get('has-evaluation', [EvaluationControllerAPI::class, 'getUserEvaluation']);
Route::middleware(['auth:api', 'admin'])->get('average-evaluation', [EvaluationControllerAPI::class, 'getAverageEvaluation']);
