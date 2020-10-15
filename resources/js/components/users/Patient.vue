<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header mobile-header-wrapper">
            <div class="component-wrapper-left">
                <div>
                    <img
                        :src="avatarUrl"
                        alt=""
                        height="60"
                        width="60"
                        style="border-radius: 50%; object-fit: cover"
                    />
                </div>
            </div>
            <div>
                <div>
                    <span class="label-small">USF:</span>
                    <span class="text-secondary label-small">{{ufsName}}</span>
                </div>
                <div>
                    <span class="label-small">Email:</span>
                    <span class="text-secondary label-small">{{email}}</span>
                </div>
            </div>
        </div>
        <div class="separator-white"/>
        <div class="component-wrapper-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="name" class="white-label">Nome</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        v-bind:class="{ 'is-invalid': errors.name !== null }"
                        placeholder="Nome"
                        v-model="name"
                    >
                    <div v-if="errors.name" class="invalid-feedback">
                        {{errors.name}}
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="white-label">Data de Nascimento</label>
                    <Datepicker
                        v-model="birthday"
                        input-class="form-control"
                        calendar-class="text-secondary"
                        :disabledDates="disabledDates"
                        :language="pt"
                    />
                    <div v-if="errors.birthday" class="invalid-feedback">
                        {{errors.birthday}}
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="white-label">Género</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="user-profile-input-gender-male"
                                   value="MALE" v-model="gender">
                            <label class="form-check-label text-secondary" for="user-profile-input-gender-male">Masculino</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="user-profile-input-gender-female"
                                   value="FEMALE" v-model="gender">
                            <label class="form-check-label text-secondary" for="user-profile-input-gender-female">Feminino</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator-white"/>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="weight" class="white-label">Peso (kg)</label>
                    <input
                        type="text"
                        class="form-control"
                        id="weight"
                        v-bind:class="{ 'is-invalid': errors.weight !== null }"
                        placeholder="weight"
                        @change="updateIMC"
                        v-model="weight"
                    >
                    <div v-if="errors.weight" class="invalid-feedback">
                        {{errors.weight}}
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="height" class="white-label">Altura (cm)</label>
                    <input
                        type="text"
                        class="form-control"
                        id="height"
                        @change="updateIMC"
                        v-bind:class="{ 'is-invalid': errors.height !== null }"
                        placeholder="height"
                        v-model="height"
                    >
                    <div v-if="errors.height" class="invalid-feedback">
                        {{errors.height}}
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="imc" class="white-label">Índice de Massa Corporal</label>
                    <input
                        type="text"
                        class="form-control"
                        id="imc"
                        placeholder="IMC"
                        v-model="imc"
                        disabled
                    >
                </div>
            </div>
            <div class="separator-white"/>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <div>
                        <label class="white-label">Problemas de Saúde</label>
                    </div>

                    <div v-if="diseases && diseases.length > 0">
                        <div v-for="(disease, index) in diseases" :key="disease.name" style="display: flex; margin-bottom: 8px">
                            <input
                                type="text"
                                class="form-control"
                                v-model="disease.name"
                                style="height: 40px"
                            >
                            <button class="el-button el-button--danger" style="height: 40px" @click="() => {removeDisease(index)}"><em class="el-icon-delete" /></button>
                        </div>
                    </div>
                    <div class="text-secondary" v-else>
                        Nenhum registado
                    </div>
                    <div style="display: flex">
                        <input
                            type="text"
                            class="form-control"
                            v-model="newDisease"
                            style="height: 40px"
                            v-bind:class="{ 'is-invalid': errors.newDisease !== null }"
                        >
                        <button class="el-button el-button--success" style="height: 40px" @click="addDisease"><em class="el-icon-plus" /></button>
                    </div>
                    <div v-if="errors.newDisease" class="invalid-feedback">
                        {{errors.newDisease}}
                    </div>
                </div>
            </div>
            <div class="separator-white"/>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <div style="display: flex" class="mb-2">
                        <label class="white-label flex-grow-1">Medicação Habitual</label>
                        <button class="el-button el-button--success" style="height: 40px" @click="addMedication"><em class="el-icon-plus" /></button>
                    </div>
                    <div v-if="medication && medication.length > 0">
                        <div v-for="(m, index) in medication" :key="m.name" style="display: flex; margin-bottom: 8px;">
                            <div class="flex-grow-1 medication-container">
                                <span class="text-secondary mr-3">{{m.name}}</span>
                                <span class="text-secondary mr-5">{{m.posology}} mg/ml</span>
                                <span class="text-secondary mr-3">{{m.timesADay}}</span>
                                <span class="text-secondary mr-3">{{renderTimesAWeek(m.timesAWeek)}}</span>
                            </div>
                            <button class="el-button el-button--primary" style="height: 40px" @click="() => {updateMedication(index)}"><em class="el-icon-edit" /></button>
                            <button class="el-button el-button--danger" style="height: 40px; margin-left: 0" @click="() => {removeMedication(index)}"><em class="el-icon-delete" /></button>
                        </div>
                    </div>
                    <div class="text-secondary" v-else>
                        Nenhum registado
                    </div>
                </div>
            </div>
        </div>
        <div>
            <button class="btn-bold btn btn-primary" v-on:click="this.savePatient" type="button"
                    data-toggle="tooltip"
                    title="Guardar alterações">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                <span>Guardar alterações</span>
            </button>
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
            :user_id="this.$route.params.id"
        />
    </div>
</template>

<script type="text/javascript">
    import {getCategoryNameById} from '../../utils/misc';
    import Datepicker from 'vuejs-datepicker';
    import {ptBR} from 'vuejs-datepicker/dist/locale'
    import { ERROR_MESSAGES, isEmptyField } from '../../utils/validations';
    import ConfirmationModal from '../modals/ConfirmationModal';
    import AddMedication from '../modals/AddMedication';

    export default {
        data: function () {
            return {
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
                diseases: [],
                medication: [],
                imc: '',
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
        methods: {
            getUser() {
                if (this.isFetching) return;

                this.isFetching = true;
                if (this.$route.params.id) {
                    axios.get(`api/users/${this.$route.params.id}`).then(response => {
                        if (!response.data.data.avatarUrl) {
                            this.avatarUrl = 'http://nutriclock.test:81/images/avatar.png';
                        } else {
                            this.avatarUrl = `http://nutriclock.test:81/storage/avatars/${response.data.data.avatarUrl}`;
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
                        this.updateIMC();

                        this.isFetching = false;

                        this.getMedication();
                    }).catch((e) => {
                        this.isFetching = false;
                    });
                }
            },
            getMedication() {
                this.isFetching = true;
                axios.get(`api/medications/${this.$route.params.id}`).then(medicationResponse => {
                    this.isFetching = false;
                    this.medication = medicationResponse.data.data;
                }).catch(() => {
                    this.isFetching = false;
                });
            },
            updateIMC() {
                if (this.weight && this.weight !== '' && this.height && this.height !== '') {
                    let h = Number(this.height)/100.0;
                    this.imc =  Number(Number(this.weight) / (h*h)).toFixed(2);
                } else {
                    this.imc = '';
                }
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

                if(this.isFetching) return;

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
            addDisease() {
                this.errors.newDisease = null;
                let hasErrors = false;

                if (isEmptyField(this.newDisease)) {
                    this.errors.newDisease = ERROR_MESSAGES.mandatoryField;
                    return;
                }

                this.diseases.forEach(d => {
                    if (d.name.toLowerCase() === this.newDisease.toLowerCase()) hasErrors = true;
                });

                if (hasErrors) {
                    this.errors.newDisease = ERROR_MESSAGES.alreadyExistingDisease;
                    return;
                }

                this.diseases.push({
                    name: this.newDisease,
                });

                this.newDisease = '';
            },
            onCloseClick() {
                this.showConfirmationModal = false;
                this.showAddMedicationModal = false;
                this.selectedIndex = null;
                this.selectedId = null;
            },
            savePatient() {
                let diseasesStr = '';

                this.diseases.forEach(d => {
                    diseasesStr += `${d.name},`;
                });

                if (this.isFetching) return;
                this.isFetching = true;

                axios.put(`api/users/${this.$route.params.id}`, {
                    name: this.name,
                    gender: this.gender,
                    weight: this.weight,
                    height: this.height,
                    birthday: this.birthday,
                    diseases: diseasesStr,
                }).then((response) => {
                    this.isFetching = false;
                    this.$toasted.show('A informação foi atualizada com sucesso!', {
                        type: 'success',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                }).catch((error) => {
                    this.isFetching = false;
                    this.$toasted.show(ERROR_MESSAGES.unknownError, {
                        type: 'error',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                    console.log(error);
                })
            }
        },
        mounted() {
            this.getUser();
        },
        components: {
            Datepicker,
            ConfirmationModal,
            AddMedication,
        }
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
</style>
