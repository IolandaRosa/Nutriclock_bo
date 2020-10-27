<template>
    <div class="tab-wrapper">
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                Diário de Refeições
            </div>
        </div>
        <div class="component-wrapper-body">
            <data-tables :data="meals" :pagination-props="{ pageSizes: [8] }" :action-col="actionCol">
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
    import { COLUMN_NAME } from '../../../utils/table_elements';
    import { parseDateToString, parseMealTypeToString } from '../../../utils/misc';

    export default{
        props: ['id'],
        data() {
            return {
                isFetching: false,
                showMealModal: false,
                selectedRow: null,
                meals: [],
                actionCol: {
                    label: ' ',
                    props: {
                        align: 'center',
                    },
                    buttons: [{
                        props: {
                            type: 'info is-circle',
                            icon: 'el-icon-view'
                        },
                        handler: row => {
                            this.showDetails(row);
                        },
                        label: ''
                    }]
                },
                titles: [{
                    prop: "parsedDate",
                    label: COLUMN_NAME.Date,
                }, {
                    prop: "time",
                    label: COLUMN_NAME.Time,
                }, {
                    prop: "name",
                    label: COLUMN_NAME.Name,
                }, {
                    prop: "parsedQuantity",
                    label: COLUMN_NAME.Quantity
                }, {
                    prop: "parsedType",
                    label: COLUMN_NAME.Type
                }],
            }
        },
        methods: {
            showDetails(row) {
                console.log('emit')
                this.$emit('meal-details', row);
            },
            getUserMeals() {
                axios.get(`api/meals/${this.id}/user`).then(response => {
                    if (this.isFetching) return;

                    this.isFetching = true;

                    response.data.data.map(meal => {
                        meal.parsedDate = parseDateToString(new Date(meal.date));
                        meal.parsedQuantity = `${meal.quantity} ${meal.relativeUnit}`;
                        meal.parsedType = parseMealTypeToString(meal.type);
                        return meal;
                    });

                    this.meals = response.data.data;
                    this.isFetching = false;
                }).catch((e) => {
                    console.log(e);
                    this.isFetching = false;
                });
            },
        },
        mounted() {
            this.getUserMeals();
        }
    };
</script>
