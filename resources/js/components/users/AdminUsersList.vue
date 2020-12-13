<template>
    <div class="component-wrapper">
        <div class="container pt-5 pb-2">
            <div class="p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Utilizadores Backoffice
                    </h3>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" data-toggle="tooltip" title="Registar Utilizador"
                                type="button" v-on:click.prevent="add">
                    <span v-if="isFetching" aria-hidden="true" class="spinner-border spinner-border-sm"
                          role="status"></span>
                            <span class="full-text">Novo Utilizador</span>
                            <span class="min-text">+</span>
                        </button>
                    </div>
                </div>

                <div class="component-wrapper-body text-dark mt-2">
                    <table id="professionalsTable" class="table-wrapper responsive table table-hover w-100">
                        <thead>
                        <tr>
                            <th v-for="title in titles" :class="title.className">
                                {{ title.label }}
                            </th>
                        </tr>
                        </thead>
                        <tbody/>
                    </table>
                </div>
            </div>
        </div>
        <AddProfessional
            v-show="showModal"
            @close="this.close"
            @save="this.save"
        />
        <Profile
            v-show="showDetailsModal"
            :userId="selectedUserId"
            @close="this.close"
            @save="this.updateUserProfile"
        />
        <ResendEmail
            v-show="showResendEmailModal"
            :selectedEmail="selectedRowEmail"
            @close="this.close"
            @save="this.resendEmail"
        />
        <ConfirmationModal
            v-show="showConfirmationModal"
            :message="confirmationModalMessage"
            :save-button-class="confirmationModalButtonClass"
            :save-button-text="confirmationModalButtonText"
            :title="confirmationModalTitle"
            cancel-button-text="Cancelar"
            @close="this.close"
            @save="this.deleteBlockUser"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {COLUMN_NAME} from '../../utils/table_elements';
import {renderGender, renderRole, getCategoryNameById} from '../../utils/misc';
import AddProfessional from '../modals/AddProfessional';
import Profile from '../modals/Profile';
import ResendEmail from '../modals/ResendEmail';
import {ERROR_MESSAGES} from '../../utils/validations';
import ConfirmationModal from '../modals/ConfirmationModal';
import {ROUTE} from '../../utils/routes';
import {UserRoles} from '../../constants/misc';
import {
    EmptyObject,
    initDataTable,
    onClickHandler,
    redrawTable,
    TableActionClasses,
    TableActionColumns
} from '../../utils/dataTables';

export default {
    data() {
        return {
            dataTable: null,
            showModal: false,
            blockUsersMessage: '',
            showDetailsModal: false,
            showResendEmailModal: false,
            showConfirmationModal: false,
            selectedRowEmail: '',
            selectedRole: 'ALL',
            isFetching: false,
            data: [],
            categories: [],
            selectedUserId: null,
            confirmationModalTitle: '',
            confirmationModalButtonClass: '',
            confirmationModalButtonText: '',
            confirmationModalMessage: '',
            titles: [{
                label: COLUMN_NAME.Name,
                className: '',
            }, {
                label: COLUMN_NAME.Email,
                className: '',
            }, {
                label: COLUMN_NAME.Role,
                className: '',
            }, {
                label: COLUMN_NAME.ProfessionalCategory,
                className: '',
            }, EmptyObject, EmptyObject, EmptyObject, EmptyObject],
            columns: [
                {data: 'name', responsivePriority: 5},
                {data: 'email', responsivePriority: 6},
                {data: 'role', responsivePriority: 7},
                {data: 'category', responsivePriority: 8},
                {...TableActionColumns.Edit, responsivePriority: 1},
                {...TableActionColumns.Resend, responsivePriority: 4},
                {...TableActionColumns.Block, responsivePriority: 3},
                {...TableActionColumns.Delete, responsivePriority: 2},
            ],
        };
    },
    methods: {
        updateUserProfile(id, user) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.put(`api/users/${id}`, {...user}).then(() => {
                this.handleSuccess('Os dados do utilizador foram atualizados com sucesso');
            }).catch(() => {
                this.handleError('Não foi possível atualizar os dados do utilizador');
            })
        },
        onUpdateUserClick(row) {
            this.selectedUserId = row.id;
            this.showDetailsModal = true;
        },
        async handleSuccess(message) {
            this.isFetching = false;
            if (message) this.showMessage(message, 'success');
            this.close();
            await this.getUsers();
            redrawTable(this.dataTable, this.data);
        },
        handleError(message) {
            this.isFetching = false;
            this.close();
            this.showMessage(message, 'error');
        },
        async getCategories(usersArray) {
            try {
                const response = await axios.get('api/professionalCategories');
                this.isFetching = false;
                this.categories = response.data.data;
                this.data = this.parseUsersInformation(usersArray);
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            }
        },
        add() {
            this.showModal = true;
        },
        parseUsersInformation(data) {
            const dataArray = [];
            data.forEach(d => {
                const obj = d;
                obj.gender = renderGender(d.gender);
                obj.role = renderRole(d.role);
                obj.category = getCategoryNameById(d.professionalCategoryId, this.categories);
                dataArray.push(obj);
            });
            return dataArray;
        },
        async getUsers() {
            this.isFetching = true;

            try {
                const response = await axios.get('api/admin/users');
                if (this.categories.length === 0) {
                    await this.getCategories(response.data.data);
                    return;
                }
                this.isFetching = false;
                this.data = this.parseUsersInformation(response.data.data);
            } catch (error) {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            }
        },
        onClickResendEmail(row) {
            this.showResendEmailModal = true;
            this.selectedRowEmail = row.email;
        },
        resendEmail(email) {
            if (this.isFetching) return;
            this.isFetching = true;
            axios.post('api/password', {
                email: email,
                register: true,
            }).then(() => {
                this.handleSuccess('O email foi reenviado');
            }).catch((error) => {
                let message = ERROR_MESSAGES.unknownError;
                const {response} = error;
                if (response) {
                    const {data} = response;
                    message = data.error;
                }
                this.handleError(message);
            });
        },
        save() {
            this.handleSuccess(null);
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
                this.blockUserMessage = 'Utilizador bloqueado com sucesso!';
            } else {
                this.confirmationModalTitle = `Desbloquear ${row.name}`;
                this.confirmationModalButtonText = 'Desbloquear';
                this.confirmationModalMessage = `Confirme que deseja desbloquear a conta de ${row.name}?`;
                this.blockUserMessage = 'Utilizador desbloqueado com sucesso!';
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
            if (this.confirmationModalButtonText === 'Eliminar') {
                axios.delete(`api/users/${this.selectedUserId}`).then(() => {
                    this.handleSuccess('Utilizador eliminado!');
                }).catch(() => {
                    this.handleError(message);
                });
                return;
            }

            axios.delete(`api/users/${this.selectedUserId}/status`).then(() => {
                this.handleSuccess(this.blockUserMessage);
            }).catch(() => {
                this.handleError(message);
            });
        },
        showMessage(message, toastClass) {
            this.$toasted.show(message, {
                type: toastClass,
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
    },
    async mounted() {
        if (this.$store.state.user && this.$store.state.user.role !== UserRoles.Admin) {
            this.$router.push(ROUTE.Patients);
            return;
        }
        await this.getUsers();
        this.dataTable = await initDataTable('#professionalsTable', this.data, this.columns);
        onClickHandler(this.dataTable, this.onClickResendEmail, '#professionalsTable', TableActionClasses.Resend);
        onClickHandler(this.dataTable, this.onDeleteClick, '#professionalsTable', TableActionClasses.Delete);
        onClickHandler(this.dataTable, this.onBlockClick, '#professionalsTable', TableActionClasses.Block);
        onClickHandler(this.dataTable, this.onUpdateUserClick, '#professionalsTable', TableActionClasses.Edit);
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
