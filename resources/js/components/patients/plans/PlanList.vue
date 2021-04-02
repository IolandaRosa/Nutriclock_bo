<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Plano Alimentar
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" type="button" data-toggle="tooltip"
                                v-on:click="showCalendar" title="Calendario">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-week" viewBox="0 0 16 16">
                                <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                        </button>
                        <button class="btn-bold btn btn-primary" data-toggle="tooltip" title="Nova Data"
                                type="button" v-on:click.prevent="openDateModal" v-show="!readonly">
                            <span v-if="isFetching" aria-hidden="true" class="spinner-border spinner-border-sm"
                                  role="status"></span>
                            <span class="full-text">Nova Data</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-header" v-show="isShowCalendar">
                    <div class="component-wrapper-left" />
                    <div class="component-wrapper-right">
                        <Datepicker
                            v-model="date"
                            input-class="form-control"
                            calendar-class="text-secondary"
                            :disabledDates="disabledDates"
                            :language="pt"
                            inline
                            :highlighted="highlighted"
                            @selected="onDayClick"
                            :monday-first="true"
                        />
                    </div>
                </div>
                <div class="component-wrapper-body mt-2 text-dark">
                    <div v-if="!this.plan || this.plan.length === 0">
                        Não existem planos alimentares registados
                    </div>
                    <div v-else v-for="(p, planIndex) in this.plan" :key="p.id" class="bg-white rounded with-shadow p-4 mb-2">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="flex-grow-1 d-flex text-secondary text-uppercase font-weight-bolder mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-calendar mr-2" viewBox="0 0 16 16">
                                  <path
                                      d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                </svg>
                                {{ renderDay(p.dayOfWeek) }} {{ p.date }}
                            </span>

                            <button type="button" class="btn btn-link mr-2" v-on:click="() => { showPlanListStatsPage(p.id, p.dayOfWeek, p.date)}">
                                Consultar Informação Nutricional
                            </button>
                            <button v-show="!readonly" type="button" class="btn btn-primary" @click="() => { addMealType(p.id, p.dayOfWeek, p.date, planIndex) }">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-plus" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                            </button>
                        </div>

                        <div v-for="(mealType, mealTypeIndex) in p.mealTypes" :key="mealType.id"
                             class="bg-light rounded with-shadow p-2 mb-2">
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="mb-1 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 0 24 24" width="18">
                                        <path color="#6c757d" d="M0 0h24v24H0z" fill="none"/>
                                        <path
                                            d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                                    </svg>
                                </div>
                                <span class="flex-grow-1 font-weight-bold text-secondary col-xs-4"
                                      style="font-size: 16px">
                                    {{ renderMealType(mealType.type) }}
                                </span>
                                <div class="text-secondary mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-clock mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                        <path
                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                    </svg>
                                    <span>
                                        {{ mealType.hour }}
                                    </span>
                                </div>
                                <div class="text-secondary mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-people mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                    </svg>
                                    <span>
                                        {{ renderPortion(mealType.portion) }}
                                    </span>
                                </div>
                                <div v-show="!readonly">
                                    <span @click="() => { openAddIngredientsModal(mealTypeIndex, mealType.id, planIndex) }">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill text-info mr-2" viewBox="0 0 16 16">
                                          <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                        </svg>
                                    </span>
                                    <span @click="() => { deleteMealType(mealType.id, planIndex, mealTypeIndex) }">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                             fill="currentColor" class="bi bi-trash-fill text-danger"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2" style="border-top: 1px solid rgba(0, 0, 0, 0.1);"/>
                            <div v-for="(i, index) in mealType.ingredients" :key="i.id">
                                <div class="d-flex flex-wrap align-items-center text-secondary mb-1">
                                    <div class="flex-grow-1" style="font-size: 14px">{{ i.name }}</div>
                                    <div class="mr-2" style="font-size: 12px; min-width: 110px"> {{ i.quantity }} {{ i.unit }}</div>
                                    <div class="mr-2" style="font-size: 12px; font-weight: 900; min-width: 60px">{{ i.grams }} gr.</div>
                                    <div v-show="!readonly" @click="() => { deleteIngredient(i.id, index, mealTypeIndex, planIndex) }">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             fill="currentColor" class="bi bi-trash-fill text-danger"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="index < mealType.ingredients.length -1" class="mb-2"
                                     style="border-top: 1px solid rgba(0, 0, 0, 0.1);"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <PlanDate @close="closeModal" v-if="showPlanDateModal" @save="openMealTypeModal"/>
        <MealTypeModal @close="closeModal" v-show="showMealTypeModal" :date="mealDate" @save="openIngredients"/>
        <AddIngredientModal @close="closeModal" v-show="showAddIngredientModal" @save="saveIngredientsToMeal" :mealTypeId="selectedMealType" />
    </div>
</template>


<script type="text/javascript">
import { sortBy } from 'lodash';
import Datepicker from 'vuejs-datepicker';
import { ptBR } from 'vuejs-datepicker/dist/locale'
import PlanDate from '../../modals/PlanDateModal';
import MealTypeModal from '../../modals/MealTypeModal';
import AddIngredientModal from '../../modals/AddIngredientModal';
import {parseDayEnumToString, parseMealTypeToString, renderDate} from '../../../utils/misc';
import {ERROR_MESSAGES} from '../../../utils/validations';
import {UserRoles} from '../../../constants/misc';

export default {
    props: ['id'],
    data: function () {
        return {
            isFetching: false,
            showPlanDateModal: false,
            showMealTypeModal: false,
            showAddIngredientModal: false,
            updatePlanId: null,
            updatePlanIndex: null,
            selectedMealType: null,
            selectedMealTypeIndex: null,
            mealDate: '',
            plan: [],
            readonly: false,
            highlighted: {
                dates: [],
            },
            date: new Date(),
            disabledDates: {
                from: new Date(Date.now()),
            },
            pt: ptBR,
            isShowCalendar: false,
        };
    },
    methods: {
        onDayClick(e) {
            this.getMealPlans(renderDate(e));
            this.isShowCalendar = false;
        },
        showCalendar() {
            this.isShowCalendar = !this.isShowCalendar;
        },
        openAddIngredientsModal(index, mealTypeId, planIndex){
            this.selectedMealType = mealTypeId;
            this.selectedMealTypeIndex = index;
            this.updatePlanIndex = planIndex;
            this.showAddIngredientModal = true;
        },
        openDateModal() {
            this.showPlanDateModal = true;
        },
        saveIngredientsToMeal(ingredients){
            this.plan[this.updatePlanIndex].mealTypes[this.selectedMealTypeIndex].ingredients.push(...ingredients);
            this.updatePlanIndex = null;
            this.selectedMealType = null;
            this.selectedMealTypeIndex = null;
            this.showAddIngredientModal = false;
        },
        closeModal() {
            this.showPlanDateModal = false;
            this.showMealTypeModal = false;
            this.showAddIngredientModal = false;
            this.updatePlanId = null;
            this.updatePlanIndex = null;
            this.selectedMealType = null;
            this.selectedMealTypeIndex = null;
        },
        openMealTypeModal(dateString) {
            this.showPlanDateModal = false;
            this.showMealTypeModal = true;
            this.mealDate = dateString;
        },
        openIngredients(name, time, portion, dateString) {
            this.showMealTypeModal = false;

            if (this.updatePlanId) {
                axios.post(`api/meal-type/${this.updatePlanId}`, {
                    name,
                    portion,
                    time: time,
                }).then(response => {
                    this.isFetching = false;
                    this.updatePlanId = null;
                    if (this.updatePlanIndex !== null) {
                        this.plan[this.updatePlanIndex].mealTypes.push(response.data.data);
                        this.plan[this.updatePlanIndex].mealTypes = sortBy(this.plan[this.updatePlanIndex].mealTypes, 'hour');
                        this.updatePlanIndex = null;
                    }
                }).catch(error => {
                    this.isFetching = false;
                    const { response } = error;
                    let message = ERROR_MESSAGES.unknownError;
                    if (response) {
                        const { data } = response;
                        if (data && data.error) {
                            message = data.error;
                        }
                        this.showError(message);
                    }
                });
                return;
            }
            this.$emit('open-ingredient', name, time, portion, dateString);
        },
        showPlanListStatsPage(planId, dayOfWeek, date) {
            const dateString = `${this.renderDay(dayOfWeek)} ${date}`;
            this.$emit('open-stats', dateString, planId);
        },
        showError(message) {
            this.$toasted.show(message, {
                type: 'error',
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
        deleteIngredient(id, ingredientIndex, mealTypeIndex, planIndex) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.delete(`api/ingredient/${id}`).then(() => {
                this.isFetching = false;
                this.plan[planIndex].mealTypes[mealTypeIndex].ingredients.splice(ingredientIndex, 1);
            }).catch(() => {
                this.isFetching = false;
            });
        },
        deleteMealType(id, planIndex, index) {
            axios.delete(`api/meal-type/${id}`).then(() => {
                this.isFetching = false;
                this.plan[planIndex].mealTypes.splice(index, 1);
            }).catch(() => {
                this.isFetching = false;
            });
        },
        addMealType(id, dayOfWeek, date, index) {
            this.updatePlanId = id;
            this.updatePlanIndex = index;
            this.showMealTypeModal = true;
            this.mealDate = `${parseDayEnumToString(dayOfWeek) } ${ date }`;
        },
        renderDay(value) {
            return parseDayEnumToString(value);
        },
        renderMealType(value) {
            return parseMealTypeToString(value);
        },
        renderPortion(p) {
            if (p > 1) return `${p} porções`;
            return `${p} porção`;
        },
        getMealPlans(date) {
            axios.get(`api/meal-plans/${this.id}/${date}`).then(response => {
                this.isFetching = false;
                this.plan = response.data.data;
            }).catch(() => {
                this.isFetching = false;
            });

            if (this.$store.state.user) {
                this.readonly = this.$store.state.user.role === UserRoles.Intern
            }
        }
    },
    mounted() {
        if (this.isFetching) return;
        this.isFetching = true;
        axios.get(`api/meal-plans-dates/${this.id}`).then(response => {
            response.data.data.forEach(e => {
                const startDateParts = e.start.split('-');
                const endDateParts = e.end.split('-');
                let startDate = new Date(`${startDateParts[2]}/${startDateParts[1]}/${startDateParts[0]}`);
                const endDate = new Date(`${endDateParts[2]}/${endDateParts[1]}/${endDateParts[0]}`);
                while (startDate <= endDate) {
                    this.highlighted.dates.push(new Date(startDate));
                    startDate.setDate(startDate.getDate() + 1);
                }
            });
        }).catch(() => { });

        this.getMealPlans(renderDate(new Date()));
    },
    components: {
        PlanDate,
        MealTypeModal,
        AddIngredientModal,
        Datepicker,
    }
};
</script>

