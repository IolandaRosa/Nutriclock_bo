<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Adicionar utilizador a grupo de recolha</span>
                    </div>
                    <div class="modal-body">
                        <div class="font-weight-bold text-dark">Utilizadores no grupo</div>
                        <div class="with-p-4 bg-light rounded with-shadow mb-3">
                            <div v-if="this.usersFromGroup.length === 0" class="m-2">
                                Ainda não foram adicinados utilizadores na recolha.
                            </div>
                            <div v-else v-for="(user, index) in this.usersFromGroup" :key="user.id" class="d-flex m-2">
                                <div class="mr-2 flex-grow-1">{{ user.name }}</div>
                                <div @click="() => onRemoveUserFromGroup(user.id)" class="mr-2 text-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-x text-danger" viewBox="0 0 16 16">
                                        <path
                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="font-weight-bold text-dark">Utilizadores para adicionar</div>
                        <div class="with-p-4 bg-light rounded with-shadow">
                            <div v-if="this.users.length === 0" class="m-2">
                                Não existem utilizadores para adicionar.
                            </div>
                            <div v-else v-for="(user, index) in this.users" :key="user.id" class="d-flex m-2">
                                <div class="mr-2 flex-grow-1">{{ user.name }}</div>
                                <div @click="() => onAddUserFromGroup(user.id)" class="mr-2 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus text-primary" viewBox="0 0 16 16">
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-bold btn btn-secondary" @click="onCloseClick" id="close-category-modal-btn">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */

export default {
    props: ['groupId'],
    data() {
        return {
            id: null,
            isFetching: false,
            usersFromGroup: [],
            users: [],
        };
    },
    methods: {
        onCloseClick() {
            this.$emit('close');
        },
        getUsers() {
            axios.get(`api/biometric-group/users/${this.id}`).then(response => {
                this.usersFromGroup = response.data.usersFromGroup;
                this.users = response.data.usersWithoutGroup;
            }).catch(e => {
                this.isFetching = false;
                console.log(e);
            });
        },
        onRemoveUserFromGroup(userId) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.post('api/biometric-group-user-remove', { 'userId': userId }).then(response => {
                this.isFetching = false;
                this.usersFromGroup = response.data.usersFromGroup;
                this.users = response.data.usersWithoutGroup;
            }).catch(e => {
                this.isFetching = false;
                console.log(e);
            });
        },
        onAddUserFromGroup(userId) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.post('api/biometric-group-user-add', { 'userId': userId, 'groupId': this.id }).then(response => {
                this.isFetching = false;
                this.usersFromGroup = response.data.usersFromGroup;
                this.users = response.data.usersWithoutGroup;
            }).catch(e => {
                this.isFetching = false;
                console.log(e);
            });
        }
    },
    watch: {
        groupId: function (newVal, oldVal) {
            if (newVal) {
                this.id = newVal;
                this.getUsers();
            }
        }
    },
};
</script>
