<template>
    <div class="tab-wrapper">
        <div class="px-4 pb-4 pt-1 bg-light rounded with-shadow">
            <div class="component-wrapper-header">
                <div class="component-wrapper-left">
                    <img
                        :src="avatarUrl"
                        alt=""
                        class="profile-img"
                    />
                </div>
                <div class="component-wrapper-right">
                    <button v-show="!readonly" class="btn-bold btn btn-primary" v-on:click="this.savePatient"
                            type="button"
                            data-toggle="tooltip"
                            title="Guardar alterações">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                        <span>Guardar alterações</span>
                    </button>
                </div>
            </div>
            <div class="card-deck">
                <div class="card p-2 pt-3 mt-2 text-dark">
                    <h5 class="card-title">Dados Pessoais</h5>
                    <p class="card-text"><strong>Email:</strong> {{ email }} </p>
                    <p class="card-text"><strong>USF:</strong> {{ ufsName }}</p>
                    <div class="card-text">
                        <div class="form-group">
                            <label for="name" class="small-text">Nome</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                v-bind:class="{ 'is-invalid': errors.name !== null }"
                                v-model="name"
                                :disabled="readonly"
                            >
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.name }}
                            </div>
                        </div>
                    </div>
                    <div class="card-text">
                        <div class="form-group">
                            <label class="small-text text-secondary">Data de Nascimento</label>
                            <Datepicker
                                v-model="birthday"
                                input-class="form-control"
                                calendar-class="text-secondary"
                                :disabledDates="disabledDates"
                                :language="pt"
                                :disabled="readonly"
                            />
                            <div v-if="errors.birthday" class="invalid-feedback">
                                {{ errors.birthday }}
                            </div>
                        </div>
                    </div>
                    <div class="card-text">
                        <div class="form-group">
                            <label class="small-text">Género</label>
                            <div>
                                <select
                                    class="form-control"
                                    style="height: 40px"
                                    v-bind:class="{ 'is-invalid': errors.gender !== null }"
                                    :disabled="readonly"
                                    v-model="gender">
                                    <option value="MALE">Masculino</option>
                                    <option value="FEMALE">Feminino</option>
                                    <option value="NONE">Não me identifico</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card p-2 pt-3 mt-2 text-dark">
                    <h5 class="card-title">Dados Biométricos</h5>
                    <div class="card-text">
                        <div class="form-group">
                            <label for="weight" class="small-text text-secondary">Peso (kg)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="weight"
                                v-bind:class="{ 'is-invalid': errors.weight !== null }"
                                v-model="weight"
                                :disabled="readonly"
                            >
                            <div v-if="errors.weight" class="invalid-feedback">
                                {{ errors.weight }}
                            </div>
                        </div>
                    </div>
                    <div class="card-text">
                        <div class="form-group">
                            <label for="height" class="small-text text-secondary">Altura (cm)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="height"
                                v-bind:class="{ 'is-invalid': errors.height !== null }"
                                v-model="height"
                                :disabled="readonly"
                            >
                            <div v-if="errors.height" class="invalid-feedback">
                                {{ errors.height }}
                            </div>
                        </div>
                    </div>
                    <div class="card-text">
                        <div class="form-group">
                            <label for="imc" class="small-text text-secondary">Índice de Massa Corporal</label>
                            <input
                                type="text"
                                class="form-control"
                                id="imc"
                                placeholder="0"
                                v-model="imc"
                                disabled
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-deck">
                <div class="card p-2 pt-3 mt-2 text-dark">
                    <div class="card-title d-flex">
                        <h5 class="flex-grow-1">Problemas de Saúde</h5>
                        <button class="btn btn-sm btn-outline-primary" @click="() => {this.showAddDiseaseModal = true}"
                                v-show="!readonly">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-text">
                        <div class="form-group">
                            <div v-if="diseases && diseases.length > 0">
                                <div v-for="(disease, index) in diseases" :key="disease.name"
                                     style="display: flex; margin-bottom: 8px">
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="disease.name"
                                        :disabled="readonly"
                                    >
                                    <button class="btn btn-sm btn-outline-danger"
                                            @click="() => {removeDisease(index)}" v-show="!readonly">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div v-else>
                                Nenhum registado
                            </div>
                        </div>
                    </div>
                    <div v-if="errors.newDisease" class="invalid-feedback">
                        {{ errors.newDisease }}
                    </div>
                </div>
                <div class="card p-2 pt-3 mt-2 text-dark">
                    <div class="card-title d-flex">
                        <h5 class="flex-grow-1">Medicação Habitual</h5>
                        <button class="btn btn-sm btn-outline-primary" @click="addMedication"
                                v-show="!readonly">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="form-group">
                        <div v-if="medication && medication.length > 0">
                            <div v-for="(m, index) in medication" :key="m.name"
                                 style="display: flex; margin-bottom: 8px;">
                                <div class="flex-grow-1 medication-container">
                                    <span class="text-secondary mr-3">{{ m.name }}</span>
                                    <span class="text-secondary">{{ m.posology }} mg/ml</span>
                                </div>
                                <button v-show="!readonly" class="btn btn-sm btn-outline-info"
                                        @click="() => {updateMedication(index)}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                    </svg>
                                </button>
                                <button v-show="!readonly" class="btn btn-sm btn-outline-danger"
                                        @click="() => {removeMedication(index)}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="text-secondary" v-else>
                            Nenhum registado
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.onCloseClick"
            :title="confirmationModalTitle"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.saveConfirmationModal"
            :message="confirmationModalMessage"
        />
        <AddMedication
            v-show="showAddMedicationModal"
            @close="this.onCloseClick"
            :title="addMedicationModalTitle"
            @save="this.addMedicationToList"
            :id="this.selectedId"
            :user_id="this.id"
        />
        <AddPatientDisease
            v-show="showAddDiseaseModal"
            @close="this.onCloseClick"
            @save="this.addSelectedDisease"
            :allergiesList="allergiesList"
            :diseasesList="diseasesList"
        />
    </div>
</template>

<script type="text/javascript">
import {getCategoryNameById} from '../../utils/misc';
import Datepicker from 'vuejs-datepicker';
import {ptBR} from 'vuejs-datepicker/dist/locale'
import {ERROR_MESSAGES, isEmptyField} from '../../utils/validations';
import ConfirmationModal from '../modals/ConfirmationModal';
import AddMedication from '../modals/AddMedication';
import AddPatientDisease from '../modals/AddPatientDisease';
import {UserRoles} from '../../constants/misc';
import {ROUTE} from '../../utils/routes';

export default {
    props: ['id'],
    data: function () {
        return {
            readonly: false,
            disabledDates: {
                from: new Date(),
            },
            confirmationModalTitle: '',
            confirmationModalMessage: '',
            showConfirmationModal: false,
            showAddMedicationModal: false,
            showAddDiseaseModal: false,
            addMedicationModalTitle: '',
            selectedId: '',
            selectedIndex: null,
            pt: ptBR,
            user: null,
            ufsName: '',
            name: '',
            avatarUrl: '',
            gender: '',
            email: '',
            birthday: '',
            weight: '',
            height: '',
            diseasesList: [],
            allergiesList: [],
            diseases: [],
            medication: [],
            errors: {
                name: null,
                gender: null,
                birthday: null,
                weight: null,
                height: null,
                newDisease: null,
            },
            isFetching: false,
        };
    },
    computed: {
        // a computed getter
        imc: function () {
            if (this.validateWeightAndHeight()) return '';

            if (this.weight && this.weight !== '' && this.height && this.height !== '') {
                let h = Number(this.height) / 100.0;
                return Number(Number(this.weight) / (h * h)).toFixed(2);
            } else {
                return '';
            }
        }
    },
    methods: {
        getDiseases() {
            const diseasesList = [];
            const allergiesList = [];
            axios.get(`api/diseases`).then(response => {
                if (response.data.data) {
                    response.data.data.forEach(d => {
                        if (d.type === 'A') {
                            allergiesList.push({
                                value: d.name,
                                label: d.name,
                            });
                        } else {
                            diseasesList.push({
                                value: d.name,
                                label: d.name,
                            });
                        }
                    });
                }

                this.allergiesList = allergiesList;
                this.diseasesList = diseasesList;
            }).catch();
        },
        getUser() {
            if (this.isFetching) return;

            this.isFetching = true;
            if (this.id) {
                axios.get(`api/users/${this.id}`).then(response => {
                    if (!response.data.data.avatarUrl) {
                        this.avatarUrl = 'https://nutriclock.herokuapp.com/images/avatar.png';
                    } else {
                        this.avatarUrl = `https://nutriclock.s3-eu-west-1.amazonaws.com/avatars/${response.data.data.avatarUrl}`;
                    }

                    let diseasesArray = [];

                    this.ufsName = getCategoryNameById(response.data.data.ufc_id, this.$store.state.userUfcs);
                    this.name = response.data.data.name;
                    this.email = response.data.data.email;
                    this.gender = response.data.data.gender;

                    if (response.data.data.weight) this.weight = response.data.data.weight;
                    if (response.data.data.height) this.height = response.data.data.height;
                    this.birthday = new Date(response.data.data.birthday);
                    if (response.data.data.diseases) {
                        response.data.data.diseases.replace(/,\s*$/, "").split(',').forEach(d => {
                            diseasesArray.push({name: d});
                        });
                    }

                    this.diseases = diseasesArray;
                    this.isFetching = false;

                    this.getMedication();
                }).catch((error) => {
                    this.isFetching = false;
                    if (error.response && error.response.status === 401) {
                        this.$router.push(ROUTE.Login)
                    }
                });
            }
        },
        getMedication() {
            this.isFetching = true;
            axios.get(`api/medications/${this.id}`).then(medicationResponse => {
                this.isFetching = false;
                this.medication = medicationResponse.data.data;
            }).catch(() => {
                this.isFetching = false;
            });
        },
        validateWeightAndHeight() {
            let hasErrors = false;
            this.errors.weight = null;
            this.errors.height = null;

            if (this.weight && isEmptyField(this.weight)) {
                hasErrors = true;
                this.errors.weight = ERROR_MESSAGES.mandatoryField;
            }

            if (this.height && isEmptyField(this.height)) {
                hasErrors = true;
                this.errors.height = ERROR_MESSAGES.mandatoryField;
            }

            if (this.weight && isNaN(this.weight)) {
                hasErrors = true;
                this.errors.weight = ERROR_MESSAGES.invalidFormat;
            }

            if (this.height && isNaN(this.height)) {
                hasErrors = true;
                this.errors.height = ERROR_MESSAGES.invalidFormat;
            }

            if (this.weight && this.weight <= 0) {
                hasErrors = true;
                this.errors.weight = ERROR_MESSAGES.invalidNegative;
            }

            if (this.height && this.height <= 0) {
                hasErrors = true;
                this.errors.height = ERROR_MESSAGES.invalidNegative;
            }

            return hasErrors;
        },
        removeDisease(index) {
            this.showConfirmationModal = true;
            this.confirmationModalTitle = 'Eliminar Problema de Saúde';
            this.confirmationModalMessage = 'Tem a certeza que deseja remover da lista o problema de saúde selecionado?';
            this.selectedIndex = index;
        },
        addMedication() {
            this.showAddMedicationModal = true;
            this.addMedicationModalTitle = 'Nova Medicação';
        },
        addMedicationToList() {
            this.getMedication();
            this.onCloseClick();
        },
        updateMedication(index) {
            const med = this.medication[index];
            this.selectedId = med.id;
            this.showAddMedicationModal = true;
            this.addMedicationModalTitle = 'Editar Medicação';
        },
        removeMedication(index) {
            this.showConfirmationModal = true;
            this.confirmationModalTitle = 'Eliminar Medicação';
            this.confirmationModalMessage = 'Tem a certeza que deseja remover da lista a medicação selecionada?';
            this.selectedIndex = index;
        },
        saveConfirmationModal() {
            if (this.confirmationModalTitle === 'Eliminar Problema de Saúde') {
                this.diseases.splice(this.selectedIndex, 1);
                this.onCloseClick();
                return;
            }

            if (this.isFetching) return;

            this.isFetching = true;

            const m = this.medication[this.selectedIndex];

            axios.delete(`api/medications/${m.id}`).then(() => {
                this.isFetching = false;
                this.medication.splice(this.selectedIndex, 1);
                this.onCloseClick();
            }).catch(() => {
                this.isFetching = false;
            });
        },
        renderTimesAWeek(timesAWeek) {
            if (!timesAWeek) return;

            let times = timesAWeek.replace(/,\s*$/, "").split(',');
            let str = "";

            if (times.length === 7) return 'Todos os dias';

            times.forEach(t => {
                if (Number(t) === 2) str += "2ª, ";
                if (Number(t) === 3) str += "3ª, ";
                if (Number(t) === 4) str += "4ª, ";
                if (Number(t) === 5) str += "5ª, ";
                if (Number(t) === 6) str += "6ª, ";
                if (Number(t) === 7) str += "Sábado, ";
                if (Number(t) === 1) str += "Domingo, ";
            });

            return str.replace(/,\s*$/, "");
        },
        addSelectedDisease(diseaseSelected, allergySelected, disease) {
            this.onCloseClick();
            if (!isEmptyField(diseaseSelected)) {
                this.addDisease(diseaseSelected);
            }
            if (!isEmptyField(allergySelected)) {
                this.addDisease(allergySelected);
            }
            if (!isEmptyField(disease)) {
                this.addDisease(disease);
            }
        },
        addDisease(disease) {
            let hasErrors = false;
            this.errors.newDisease = null;

            this.diseases.forEach(d => {
                if (d.name.toLowerCase() === disease.toLowerCase()) hasErrors = true;
            });

            if (hasErrors) {
                this.errors.newDisease = ERROR_MESSAGES.alreadyExistingDisease;
                return;
            }

            this.diseases.push({
                name: disease,
            });
        },
        onCloseClick() {
            this.showConfirmationModal = false;
            this.showAddMedicationModal = false;
            this.showAddDiseaseModal = false;
            this.selectedIndex = null;
            this.selectedId = null;
        },
        savePatient() {
            if (this.validateWeightAndHeight()) return;
            this.errors.weight = null;
            this.errors.height = null;

            let diseasesStr = '';

            this.diseases.forEach(d => {
                diseasesStr += `${d.name},`;
            });

            if (this.isFetching) return;
            this.isFetching = true;

            axios.put(`api/users/${this.id}`, {
                name: this.name,
                gender: this.gender,
                weight: this.weight,
                height: this.height,
                birthday: this.birthday,
                diseases: diseasesStr,
            }).then(() => {
                this.isFetching = false;
                this.$toasted.show('A informação foi atualizada com sucesso!', {
                    type: 'success',
                    duration: 3000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
            }).catch(() => {
                this.isFetching = false;
                this.$toasted.show(ERROR_MESSAGES.unknownError, {
                    type: 'error',
                    duration: 3000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
            })
        }
    },
    mounted() {
        if (this.$store.state.user) {
            this.readonly = this.$store.state.user.role === UserRoles.Intern
        }
        this.getUser();
        this.getDiseases();
    },
    components: {
        Datepicker,
        ConfirmationModal,
        AddMedication,
        AddPatientDisease,
    },
};
</script>

<style>
.invalid-feedback {
    display: unset;
}

.small-text {
    font-size: 12px;
    font-weight: 700;
}

</style>
