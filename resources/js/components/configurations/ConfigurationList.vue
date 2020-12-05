<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Configurações
            </div>
        </div>
        <div class="component-wrapper-body">

        </div>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import { ROUTE } from '../../utils/routes';

export default {
    data() {
        return {
            isFetching: false,
        };
    },
    methods: {
        getSleepTips() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/diseases').then((response) => {
                this.isFetching = false;
                this.data = response.data.data;
            }).catch((error) => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            });
        }
    },
    mounted() {
        this.getSleepTips();
    },
};
</script>
