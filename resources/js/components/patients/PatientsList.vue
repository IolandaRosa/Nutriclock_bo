<template>
    <div class="component-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Utentes
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
                            v-on:change="filterByUsf"
                            v-model="selectedUsf">
                            <option value="ALL" selected>Todas as USF</option>
                            <option v-for="usf in ufcs" :value="usf.id">{{usf.name}}</option>
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
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */

    import { getCategoryNameById, parseDateToString, renderGender } from '../../utils/misc';
    import { COLUMN_NAME } from '../../utils/table_elements';
    import { ROUTE } from '../../utils/routes';

    export default {
        data() {
            return {
                isFetching: false,
                data: [],
                originalData: [],
                selectedUserId: null,
                ufcs: [],
                selectedUsf: 'ALL',
                titles: [{
                    prop: "name",
                    label: COLUMN_NAME.Name,
                }, {
                    prop: "gender",
                    label: COLUMN_NAME.Gender,
                }, {
                    prop: "parsedDate",
                    label: COLUMN_NAME.Birthday,
                }, {
                    prop: "ufc",
                    label: COLUMN_NAME.Usf
                }, {
                    prop: "email",
                    label: COLUMN_NAME.Email
                }],
                filters: [{
                    value: '',
                    prop: ['name', 'email', 'gender'],
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
                            this.$router.push({
                                name: 'PatientTabs',
                                params: {
                                    id: row.id,
                                }
                            });
                        },
                        label: ''
                    }]
                }
            };
        },
        methods: {
            filterByUsf() {
                const filtered = [];

                if (this.selectedUsf === 'ALL') {
                    this.data = this.originalData;
                    return;
                }

                this.originalData.forEach(obj => {
                    if (obj.ufc_id === this.selectedUsf) {
                        filtered.push(obj);
                    }
                });
                this.selectedUsf = null;
                this.data = filtered;
            },
            getUsers() {
                if (!this.$store.state.user) this.$router.push(ROUTE.Login);
                this.isFetching = true;
                axios.get(`api/users/${this.$store.state.user.id}/ufcs`).then(response => {
                    const ufcsIds = [];
                    const dataArray = [];

                    this.ufcs = response.data.data;

                    this.$store.commit('setUserUfcs', response.data.data);

                    response.data.data.forEach(ufc => {
                        ufcsIds.push(ufc.id);
                    });

                    axios.post('api/patients', {ufcsIds}).then(res => {
                        this.isFetching = false;

                        res.data.data.forEach(d => {
                            const obj = d;
                            obj.gender = renderGender(d.gender);
                            obj.ufc = getCategoryNameById(d.ufc_id, response.data.data);
                            obj.parsedDate = parseDateToString(new Date(d.birthday));

                            dataArray.push(obj);
                        });

                        this.data = dataArray;
                        this.originalData = dataArray;
                    }).catch(() => {
                        this.isFetching = false;
                    });
                }).catch(() => {
                    this.isFetching = false;
                    if (error.response && error.response.status === 401) {
                        this.$router.push(ROUTE.Login)
                    }
                });
            },
        },
        mounted() {
            this.getUsers();
        },
    };
</script>
