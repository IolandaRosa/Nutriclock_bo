<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Configurações
                    </h3>
                </div>
                <div class="component-wrapper-body">
                    <div class="table-responsive">
                        <table class="table table-hover bg-white">
                            <thead>
                            <tr>
                                <th scope="col" colspan="2">Configuração</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="d in data">
                                <td>{{ parseConfigurationKey(d.key) }}</td>
                                <td>
                                    <input
                                        v-if="d.key !== 'SLEEP_TIP_ENABLED'"
                                        type="text"
                                        class="form-control"
                                        id="configurations-sleep-tip-input"
                                        v-model="d.value"
                                        v-on:change="() => updateConfiguration(d.id, d.value)"
                                    >
                                    <div v-else class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            v-model="d.value"
                                            id="configurations-sleep-tip-checkbox""
                                            v-on:change="() => updateConfiguration(d.id, d.value)"
                                        >
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {ROUTE} from '../../utils/routes';

export default {
    data() {
        return {
            isFetching: false,
            data: [],
        };
    },
    methods: {
        updateConfiguration(id, value) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.put(`api/configs/${id}`, {value}).then(() => {
                this.isFetching = false;
                this.showToast('A configuração foi atualizada com sucesso!', 'success');
            }).catch((error) => {
                this.isFetching = false;
                this.showToast('Ocorreu um erro ao atualizar a configuração!', 'error');
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            });
        },
        getConfigurations() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/configs').then((response) => {
                this.isFetching = false;
                this.data = response.data.data;
            }).catch((error) => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        },
        showToast(message, messageType) {
            this.$toasted.show(message, {
                type: messageType,
                duration: 2000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
        parseConfigurationKey(key) {
            switch (key) {
                case 'SLEEP_TIP_ENABLED':
                    return 'Ativar Dicas de Sono';
                case 'GERAL_EMAIL':
                    return 'Contacto de Email';
                case 'GERAL_PHONE':
                    return 'Contacto Telefónico';
            }
        }

    },
    mounted() {
        this.getConfigurations();
    },
};
</script>
