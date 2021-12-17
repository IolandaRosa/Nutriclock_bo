<template>
    <div class="wrapper-center">
        <div id="login-form-container" style="max-height: 100%; max-width: unset;" class="white-container px-5 overflow-auto">
            <div class="d-flex mb-3 align-items-center">
                <img :src="'images/logo_text_horizontal.png'" alt="" height="30"/>
                <h1 class="text-dark font-weight-bold flex-grow-1 text-right mb-0">
                    {{title}}
                </h1>
            </div>
            <h6 class="text-dark text-justify">
                {{description}}
            </h6>
        </div>
    </div>
</template>

<script type="text/javascript">
import {ERROR_MESSAGES, isEmptyField} from '../../utils/validations';
import {ROUTE} from '../../utils/routes';

export default {
    data: function () {
        return {
            title: '',
            version: '',
            description: '',
            errors: {
                title: null,
                description: null,
            },
            isFetching: false,
        };
    },
    methods: {
        getTermsAndConditions() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/terms').then(response => {
                this.isFetching = false;
                this.title = response.data.data.title;
                this.description = response.data.data.description;
                this.version = response.data.data.version;
            }).catch(error => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            });
        }
    },
    mounted() {
        this.getTermsAndConditions();
    },
};
</script>
