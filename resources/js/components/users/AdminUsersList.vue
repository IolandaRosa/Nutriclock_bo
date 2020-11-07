<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Utilizadores Backoffice
            </div>
            <div class="component-wrapper-right">
                <button class="btn-bold btn btn-primary" v-on:click.prevent="add" type="button" data-toggle="tooltip" title="Registar Utilizador">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                    <span class="full-text">Registar Utilizador</span>
                    <span class="min-text">+</span>
                </button>
            </div>
        </div>
        <div class="component-wrapper-body">
            <data-tables :data="data" :pagination-props="{ pageSizes: [8] }" :action-col="actionCol" :filters="filters">
                <el-row slot="tool" style="margin: 10px 0">
                    <el-col :span="10">
                        <el-input v-model="filters[0].value" placeholder="Pesquisar..."></el-input>
                    </el-col>
                    <el-col :span="5" style="padding-left: 8px">
                        <select
                            style="height: 40px"
                            class="form-control"
                            id="users-list-modal-select-role"
                            v-on:change="filterByRole"
                            v-model="selectedRole">
                            <option value="ALL" selected>Todos os Perfis</option>
                            <option v-for="role in roles" :value="role.label">{{role.label}}</option>
                        </select>
                    </el-col>
                </el-row>
                <el-table-column
                    v-for="title in titles"
                    :prop="title.prop"
                    :label="title.label"
                    :key="title.label"
                    :sortable="true">
                </el-table-column>
            </data-tables>
        </div>
        <AddProfessional
            v-show="showModal"
            @save="this.save"
            @close="this.close"
        />
        <Profile
            v-show="showDetailsModal"
            @close="this.close"
            :userId="selectedUserId"
            @save="this.updateUserProfile"
        />
        <ResendEmail
            v-show="showResendEmailModal"
            @close="this.close"
            @save="this.resendEmail"
            :selectedEmail="selectedRowEmail"
        />
        <ConfirmationModal
            v-show="showConfirmationModal"
            @close="this.close"
            :title="confirmationModalTitle"
            cancel-button-text="Cancelar"
            :save-button-class="confirmationModalButtonClass"
            :save-button-text="confirmationModalButtonText"
            @save="this.deleteBlockUser"
            :message="confirmationModalMessage"
        />
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import { COLUMN_NAME } from '../../utils/table_elements';
    import { renderGender, renderRole, getCategoryNameById } from '../../utils/misc';
    import AddProfessional from '../modals/AddProfessional';
    import Profile from '../modals/Profile';
    import ResendEmail from '../modals/ResendEmail';
    import { ERROR_MESSAGES } from '../../utils/validations';
    import ConfirmationModal from '../modals/ConfirmationModal';
    import { ROUTE } from '../../utils/routes';
    import { UserRoles } from '../../constants/misc';

    export default{
        data() {
            return {
                showModal: false,
                showDetailsModal: false,
                showResendEmailModal: false,
                showConfirmationModal: false,
                selectedRowEmail: '',
                selectedRole: 'ALL',
                isFetching: false,
                data: [],
                originalData: [],
                categories: [],
                selectedUserId: null,
                confirmationModalTitle: '',
                confirmationModalButtonClass: '',
                confirmationModalButtonText: '',
                confirmationModalMessage: '',
                roles: [{
                    value: 'ADMIN',
                    label: 'Administrador',
                }, {
                    value: 'INTERN',
                    label: 'Investigador',
                }, {
                    value: 'PROFESSIONAL',
                    label: 'Profissional de Saúde',
                }],
                titles: [{
                    prop: "name",
                    label: COLUMN_NAME.Name,
                }, {
                    prop: "email",
                    label: COLUMN_NAME.Email,
                }, {
                    prop: "role",
                    label: COLUMN_NAME.Role,
                }, {
                    prop: "category",
                    label: COLUMN_NAME.ProfessionalCategory
                }],
                filters: [{
                    value: '',
                    prop: ['name', 'email', 'role', 'category'],
                }],
                actionCol: {
                    label: ' ',
                    props: {
                        align: 'center',
                    },
                    buttons: [{
                        props: {
                            type: 'primary is-circle',
                            icon: 'el-icon-edit'
                        },
                        handler: row => {
                            this.selectedUserId = row.id;
                            this.showDetailsModal = true;
                        },
                        label: ''
                    }, {
                        props: {
                            type: 'success is-circle',
                            icon: 'el-icon-right'
                        },
                        handler: row => {
                            this.onClickResendEmail(row);
                        },
                        label: ''
                    }, {
                        props: {
                            type: 'info is-circle',
                            icon: 'el-icon-goods'
                        },
                        handler: row => { this.onBlockClick(row); },
                        label: ''
                    }, {
                        props: {
                            type: 'danger is-circle',
                            icon: 'el-icon-delete'
                        },
                        handler: row => { this.onDeleteClick(row); },
                        label: ''
                    }]
                }
            };
        },
        methods:{
            updateUserProfile(id, user) {
                if (this.isFetching) return;

                this.isFetching = true;

                axios.put(`api/users/${id}`, { ...user }).then(() => {
                    this.isFetching = false;
                    this.showDetailsModal = false;
                    this.getUsers();
                }).catch(() => {
                    this.isFetching = false;
                })
            },
            filterByRole() {
                const filtered = [];

                if (this.selectedRole === 'ALL') {
                    this.data = this.originalData;
                    return;
                }
                this.originalData.forEach(obj => {
                    if (obj && obj.role === this.selectedRole) {
                        filtered.push(obj);
                    }
                });
                this.selectedRole = null;
                this.data = filtered;
            },
            getCategories(usersArray) {
                axios.get('api/professionalCategories').then(response => {
                    const dataArray = [];
                    this.isFetching = false;
                    this.categories = response.data.data;

                    console.log('get categories')

                    usersArray.forEach(d => {
                        const obj = d;
                        obj.gender = renderGender(d.gender);
                        obj.role = renderRole(d.role);
                        obj.category = getCategoryNameById(d.professionalCategoryId, this.categories);
                        dataArray.push(obj);
                    });

                    this.data = dataArray;
                    this.originalData = dataArray;
                }).catch((error) => {
                    this.isFetching = false;
                    if (error.response && error.response.status === 401) {
                        this.$router.push(ROUTE.Login)
                    }
                });
            },
            add() {
              this.showModal = true;
            },
            getUsers() {
                this.isFetching = true;

                axios.get('api/admin/users').then(response => {
                    const dataArray = [];
                    if (this.categories.length === 0) {
                        this.getCategories(response.data.data);
                        return;
                    }

                    this.isFetching = false;
                    response.data.data.forEach(d => {
                        const obj = d;
                        obj.gender = renderGender(d.gender);
                        obj.role = renderRole(d.role);
                        obj.category = getCategoryNameById(d.professionalCategoryId, this.categories);

                        dataArray.push(obj);
                    });

                    this.data = dataArray;
                    this.originalData = dataArray;
                }).catch((error) => {
                    this.isFetching = false;
                    if (error.response && error.response.status === 401) {
                        this.$router.push(ROUTE.Login)
                    }
                });
            },
            onClickResendEmail(row) {
                this.showResendEmailModal = true;
                this.selectedRowEmail = row.email;
            },
            resendEmail(email){
                if (this.isFetching) return;

                this.isFetching = true;

                axios.post('api/password', {
                    email: email,
                    register: true,
                }).then(() => {
                    this.isFetching = false;
                    this.$toasted.show('O email foi reenviado', {
                        type: 'success',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                    this.showResendEmailModal = false;
                }).catch((error) => {
                    this.isFetching = false;
                    let message = ERROR_MESSAGES.unknownError;
                    const {response} = error;
                    if (response) {
                        const {data} = response;
                        message = data.error;
                    }

                    this.$toasted.show(message, {
                        type: 'error',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                });
            },
            save() {
                this.showModal = false;
                this.getUsers();
            },
            close() {
                this.showModal = false;
                this.showDetailsModal = false;
                this.showResendEmailModal = false;
                this.showConfirmationModal = false;
            },
            onBlockClick(row) {
                this.selectedUserId = row.id;
                this.showConfirmationModal = true;
                if (row.deleted_at === null) {
                    this.confirmationModalTitle = `Bloquear ${row.name}`;
                    this.confirmationModalButtonText = 'Bloquear';
                    this.confirmationModalMessage = 'Tem a certeza que deseja bloquear esta conta?';
                } else {
                    this.confirmationModalTitle = `Desbloquear ${row.name}`;
                    this.confirmationModalButtonText = 'Desbloquear';
                    this.confirmationModalMessage = `Confirme que deseja desbloquear a conta de ${row.name}?`;
                }
                this.confirmationModalButtonClass = 'btn btn-bold btn-primary';
            },
            onDeleteClick(row) {
                this.selectedUserId = row.id;
                this.showConfirmationModal = true;
                this.confirmationModalTitle = `Eliminar ${row.name}`;
                this.confirmationModalButtonClass = 'btn btn-bold btn-danger';
                this.confirmationModalButtonText = 'Eliminar';
                this.confirmationModalMessage = `Tem a certeza que deseja eliminar o utilizador ${row.name} de forma permanente?`;
            },
            deleteBlockUser() {
                if (this.isFetching) return;
                this.isFetching = true;
                let message = ERROR_MESSAGES.unknownError;
                let toastClass = 'error';
                if (this.confirmationModalButtonText === 'Eliminar') {
                    axios.delete(`api/users/${this.selectedUserId}`).then(() => {
                        this.hideConfirmationModal('Utilizador eliminado!', 'success');
                    }).catch(() => {
                        this.hideConfirmationModal(message, toastClass)
                    });
                    return;
                }

                axios.delete(`api/users/${this.selectedUserId}/status`).then((response) => {
                    this.hideConfirmationModal('Utilizador bloqueado!', 'success');
                }).catch(() => {
                    this.hideConfirmationModal(message, toastClass);
                });
            },
            hideConfirmationModal(message, toastClass) {
                this.isFetching = false;
                this.showConfirmationModal = false;
                this.$toasted.show(message, {
                    type: toastClass,
                    duration: 3000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
                this.getUsers();
            }
        },
        mounted() {
            if (this.$store.state.user && this.$store.state.user.role !== UserRoles.Admin) {
                this.$router.push(ROUTE.Patients);
                return;
            }
            this.getUsers();
        },
        components: {
            AddProfessional,
            Profile,
            ResendEmail,
            ConfirmationModal,
        },
    };
</script>

<style>
    a {
        color: #cceee1;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 16px;
        font-size: 14px;
        width: 100%;
        text-decoration: none;
    }

    a:hover {
        color: white;
        background: #cceee1;
        text-decoration: none;
    }
</style>
