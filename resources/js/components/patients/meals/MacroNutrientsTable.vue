<template>
    <div class="table-responsive">
        <table class="table table-sm table-hover bg-white m-0">
            <thead>
            <tr>
                <th/>
                <th/>
                <th scope="col" class="align-top">Nome</th>
                <th scope="col" class="align-top">Quant.<br>(g)</th>
                <th scope="col" class="align-top">Energia<br>(kcal)</th>
                <th scope="col" class="align-top">Àgua<br>(ml)</th>
                <th scope="col" class="align-top">Proteína<br>(g)</th>
                <th scope="col" class="align-top">Gordura<br>(g)</th>
                <th scope="col" class="align-top">Hid. Carb.<br>(g)</th>
                <th scope="col" class="align-top">Fibra<br>(g)</th>
                <th scope="col" class="align-top">Colesterol<br>(mg)</th>
                <th/>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in this.data" :key="item.meal.id">
                <td v-if="item.meal.foodPhotoUrl !== null" class="p-0">
                    <img
                        height="50px"
                        width="50px"
                        class="pointer"
                        style="object-fit: cover"
                        :src="`https://nutriclock.s3-eu-west-1.amazonaws.com/food/thumb_${item.meal.foodPhotoUrl}`"
                        @click="() => showZoomModal(`/food/${item.meal.foodPhotoUrl}`)"
                        alt=""
                        @error="setAltImage"
                    />
                </td>
                <td v-else class="p-0">
                    <img
                        height="50px"
                        width="50px"
                        :src="'images/placeholder.jpg'"
                        alt=""
                    />
                </td>
                <td v-if="item.meal.nutritionalInfoPhotoUrl !== null" class="p-0">
                    <img
                        height="50px"
                        width="50px"
                        class="pointer"
                        style="object-fit: cover"
                        :src="`https://nutriclock.s3-eu-west-1.amazonaws.com/nutritionalInfo/thumb_${item.meal.nutritionalInfoPhotoUrl}`"
                        @click="() => showZoomModal(`/nutritionalInfo/${item.meal.nutritionalInfoPhotoUrl}`)"
                        alt=""
                        @error="setAltImage"
                    />
                </td>
                <td v-else class="p-0">
                    <img
                        height="50px"
                        width="50px"
                        :src="'images/placeholder.jpg'"
                        alt=""
                    />
                </td>
                <td class="p-0 px-1">{{ item.meal.name }}</td>
                <td>
                    <input
                        type="text"
                        class="form-control"
                        v-model="item.meal.numericUnit"
                        v-on:change="() => updateQuantityValue(item.meal.id, item.meal.numericUnit)"
                        :disabled="readonly"
                    >
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control"
                        :id="item.nutritionalInfo[0].id"
                        :name="item.nutritionalInfo[0].id"
                        v-model="item.nutritionalInfo[0].value"
                        v-on:change="() => updateNutritionalValue(item.nutritionalInfo[0].id, item.nutritionalInfo[0].value)"
                        :disabled="readonly"
                    >
                </td>
                <td v-for="n in 7" v-if="n >= 2">
                    <input
                        type="text"
                        class="form-control"
                        :id="item.nutritionalInfo[n].id"
                        :name="item.nutritionalInfo[n].id"
                        v-model="item.nutritionalInfo[n].value"
                        v-on:change="() => updateNutritionalValue(item.nutritionalInfo[n].id, item.nutritionalInfo[n].value)"
                        :disabled="readonly"
                    >
                </td>
                <td v-if="!readonly">
                    <button type="button" class="btn btn btn-outline-success btn-sm"
                            v-on:click="() => updateNutritionalInfo(item.nutritionalInfo, index)">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-clockwise"
                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                            <path
                                d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                        </svg>
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="3"><strong>SUBTOTAL</strong></td>
                <td>{{this.subtotals.sumQuant}}</td>
                <td>{{this.subtotals.sumEnergy}}</td>
                <td>{{this.subtotals.sumWater}}</td>
                <td>{{this.subtotals.sumProtein}}</td>
                <td>{{this.subtotals.sumFat}}</td>
                <td>{{this.subtotals.sumCarbs}}</td>
                <td>{{this.subtotals.sumFiber}}</td>
                <td>{{this.subtotals.sumColetrol}}</td>
                <td/>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import { UserRoles } from '../../../constants/misc';

    export default{
        props: ['data', 'subtotals'],
        data(){
            return {
                readonly: false,
            };
        },
        methods: {
            setAltImage(event) {
                event.target.src = 'https://nutriclock.herokuapp.com/images/placeholder.jpg'
            },
            showZoomModal(image){
                this.$emit('show-zoom-modal', image);
            },
            updateQuantityValue(id, value) {
                this.$emit('update-quantity-value', id, value);
            },
            updateNutritionalValue(id, value) {
                this.$emit('update-nutritional-value', id, value);
            },
            updateNutritionalInfo(nutritionalInfo, index) {
                this.$emit('update-nutritional-info', nutritionalInfo, index);
            },
        },
        mounted() {
            if (this.$store.state.user) {
                this.readonly = this.$store.state.user.role === UserRoles.Intern
            }
        }
    };
</script>
