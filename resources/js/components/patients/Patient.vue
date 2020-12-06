<template>
    <div class="tab-wrapper">
        <div class="profile-wrapper">
            <div class="profile-wrapper-header">
                <img
                    :src="avatarUrl"
                    alt=""
                    height="60"
                    width="60"
                    style="border-radius: 50%; object-fit: cover"
                />
                <div class="mt-2 mb-4 d-flex flex-column align-items-center justify-content-center">
                    <div class="text-secondary small-text">{{ email }}</div>
                    <div class="text-secondary small-text">{{ ufsName }}</div>
                </div>
            </div>
            <div class="component-wrapper-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="name" class="small-text text-secondary">Nome</label>
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
                    <div class="form-group col-md-4">
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
                    <div class="form-group col-md-4">
                        <label class="small-text text-secondary">Género</label>
                        <div>
                            <select
                                class="form-control mb-2"
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
                <div class="form-row">
                    <div class="form-group col-md-4">
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
                    <div class="form-group col-md-4">
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
                    <div class="form-group col-md-4">
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
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <div>
                            <label class="small-text text-secondary">Problemas de Saúde</label>
                        </div>

                        <div v-if="diseases && diseases.length > 0">
                            <div v-for="(disease, index) in diseases" :key="disease.name"
                                 style="display: flex; margin-bottom: 8px">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="disease.name"
                                    style="height: 40px"
                                    :disabled="readonly"
                                >
                                <button class="el-button el-button--danger" style="height: 40px"
                                        @click="() => {removeDisease(index)}" v-show="!readonly"><em
                                    class="el-icon-delete"/></button>
                            </div>
                        </div>
                        <div class="text-secondary" v-else>
                            Nenhum registado
                        </div>
                        <div style="display: flex" v-show="!readonly">
                            <select
                                class="form-control mb-2"
                                style="height: 40px"
                                v-bind:class="{ 'is-invalid': errors.newDisease !== null }"
                                v-model="newDiseaseSelected">
                                <option v-for="d in diseasesList" :value="d.value">{{ d.label }}</option>
                            </select>
                            <button class="el-button el-button--success" style="height: 40px"
                                    @click="addSelectedDisease">
                                <em class="el-icon-plus"/></button>
                        </div>
                        <div style="display: flex" v-show="!readonly">
                            <select
                                class="form-control mb-2"
                                style="height: 40px"
                                v-bind:class="{ 'is-invalid': errors.newDisease !== null }"
                                v-model="newAllergySelected">
                                <option v-for="d in allergiesList" :value="d.value">{{ d.label }}</option>
                            </select>
                            <button class="el-button el-button--success" style="height: 40px" @click="addAllergy"><em
                                class="el-icon-plus"/></button>
                        </div>
                        <div style="display: flex" v-show="!readonly">
                            <input
                                type="text"
                                class="form-control"
                                v-model="newDisease"
                                style="height: 40px"
                                v-bind:class="{ 'is-invalid': errors.newDisease !== null }"
                            >
                            <button class="el-button el-button--success" style="height: 40px" @click="addNewDisease"><em
                                class="el-icon-plus"/></button>
                        </div>
                        <div v-if="errors.newDisease" class="invalid-feedback">
                            {{ errors.newDisease }}
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <div style="display: flex" class="mb-2">
                            <label class="small-text text-secondary flex-grow-1 d-flex align-items-end">Medicação Habitual</label>
                            <button class="el-button el-button--success" style="height: 40px" @click="addMedication"
                                    v-show="!readonly"><em class="el-icon-plus"/></button>
                        </div>
                        <div v-if="medication && medication.length > 0">
                            <div v-for="(m, index) in medication" :key="m.name"
                                 style="display: flex; margin-bottom: 8px;">
                                <div class="flex-grow-1 medication-container">
                                    <span class="text-secondary mr-3">{{ m.name }}</span>
                                    <span class="text-secondary mr-5">{{ m.posology }} mg/ml</span>
                                    <span class="text-secondary mr-3">{{ m.timesADay }}</span>
                                    <span class="text-secondary mr-3">{{ renderTimesAWeek(m.timesAWeek) }}</span>
                                </div>
                                <button v-show="!readonly" class="el-button el-button--primary" style="height: 40px"
                                        @click="() => {updateMedication(index)}"><em class="el-icon-edit"/></button>
                                <button v-show="!readonly" class="el-button el-button--danger"
                                        style="height: 40px; margin-left: 0" @click="() => {removeMedication(index)}">
                                    <em
                                        class="el-icon-delete"/></button>
                            </div>
                        </div>
                        <div class="text-secondary" v-else>
                            Nenhum registado
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button v-show="!readonly" class="btn-bold btn btn-primary" v-on:click="this.savePatient" type="button"
                        data-toggle="tooltip"
                        title="Guardar alterações">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                    <span>Guardar alterações</span>
                </button>
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
    </div>
</template>

<script type="text/javascript">
import {getCategoryNameById} from '../../utils/misc';
import Datepicker from 'vuejs-datepicker';
import {ptBR} from 'vuejs-datepicker/dist/locale'
import {ERROR_MESSAGES, isEmptyField} from '../../utils/validations';
import ConfirmationModal from '../modals/ConfirmationModal';
import AddMedication from '../modals/AddMedication';
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
            addMedicationModalTitle: '',
            selectedId: '',
            selectedIndex: null,
            newDisease: '',
            newDiseaseSelected: null,
            newAllergySelected: null,
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
            this.addMedicationModalTitle = 'Registar Medicação';
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
        addNewDisease() {
            this.errors.newDisease = null;

            if (isEmptyField(this.newDisease)) {
                this.errors.newDisease = ERROR_MESSAGES.mandatoryField;
                return;
            }

            this.addDisease(this.newDisease);
            this.newDisease = '';
        },
        addAllergy() {
            this.errors.newDisease = null;

            if (isEmptyField(this.newAllergySelected)) {
                this.errors.newDisease = ERROR_MESSAGES.mandatoryField;
                return;
            }

            this.addDisease(this.newAllergySelected);
            this.newAllergySelected = null;
        },
        addSelectedDisease() {
            this.errors.newDisease = null;

            if (isEmptyField(this.newDiseaseSelected)) {
                this.errors.newDisease = ERROR_MESSAGES.mandatoryField;
                return;
            }

            this.addDisease(this.newDiseaseSelected);
            this.newDiseaseSelected = null;
        },
        addDisease(disease) {
            let hasErrors = false;

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
    },
};
</script>

<style>
.invalid-feedback {
    display: unset;
}

.medication-container {
    display: flex;
    height: 40px;
    align-items: center;
    background: white;
    padding: 8px;
    border: lightgrey solid 1px;
    border-radius: 4px;
}

.profile-wrapper {
    background: #fdfdfd;
    margin-right: 12px;
    padding: 16px;
    border-radius: 4px;
    box-shadow: 0 3px 6px #0f0f0f28;
}

.profile-wrapper-header {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.small-text {
    font-size: 12px;
    font-weight: 700;
}
</style>
