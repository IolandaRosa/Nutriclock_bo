<template>
    <div class="list-item" v-on:click="this.showDetails">
        <div class="list-item-date">{{this.date}}</div>
        <div class="list-item-container">
            <div class="list-item-type">
                Pequeno-almoço
            </div>
            <div class="list-item-images">
                <div v-for="m in this.meal['P']" class="list-item-img">
                    <img :src="`http://nutriclock.test:81/storage/food/${m.foodPhotoUrl}`" alt="" class="list-item-image" />
                    <div class="list-item-time">{{m.time}}h</div>
                </div>
            </div>
        </div>
        <div class="list-item-container">
            <div class="list-item-type">
                Almoço
            </div>
            <div class="list-item-images">
                <div v-for="m in this.meal['A']" class="list-item-img">
                    <img :src="`http://nutriclock.test:81/storage/food/${m.foodPhotoUrl}`" alt="" class="list-item-image" />
                    <div class="list-item-time">{{m.time}}h</div>
                </div>
            </div>
        </div>
        <div class="list-item-container">
            <div class="list-item-type">
                Lanche
            </div>
            <div class="list-item-images">
                <div v-for="m in this.meal['L']" class="list-item-img">
                    <img :src="`http://nutriclock.test:81/storage/food/${m.foodPhotoUrl}`" alt="" class="list-item-image" />
                    <div class="list-item-time">{{m.time}}h</div>
                </div>
            </div>
        </div>
        <div class="list-item-container">
            <div class="list-item-type">
                Jantar
            </div>
            <div class="list-item-images">
                <div v-for="m in this.meal['J']" class="list-item-img">
                    <img :src="`http://nutriclock.test:81/storage/food/${m.foodPhotoUrl}`" alt="" class="list-item-image" />
                    <div class="list-item-time">{{m.time}}h</div>
                </div>
            </div>
        </div>
        <div class="list-item-container">
            <div class="list-item-type">
                Snacks
            </div>
            <div class="list-item-images">
                <div v-for="m in this.meal['S']" class="list-item-img">
                    <img :src="`http://nutriclock.test:81/storage/food/${m.foodPhotoUrl}`" alt="" class="list-item-image" />
                    <div class="list-item-time">{{m.time}}h</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    export default{
        props: ['meal', 'date'],
        data() {
            return {
                breakfast: [],
                lunch: [],
                dinner: [],
                snack: [],
                brunch: [],
            }
        },
        methods: {
            showDetails() {
                this.$emit('show-details', this.meal, this.date);
            }
        },
        mounted() {
            console.log('mount', this.meal);
            console.log('date', this.date);
        }
    };
</script>

<style>
    .list-item {
        box-shadow: 2px 0 10px -1px #888;
        margin-bottom: 16px;
    }

    .list-item-date {
        background: white;
        color: black;
        padding: 8px 20px;
        font-weight: 900;
        font-size: 12px;
        border-radius: 2px;
    }

    .list-item-container {
        display: flex;
        height: 100px;
        margin-bottom: 2px;
        overflow: hidden;
        cursor:pointer;
    }

    .list-item-type {
        background: lightgrey;
        color: dimgray;
        font-weight: 900;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0 20px;
        min-width: 100px;
        max-width: 100px;
    }

    img.list-item-image {
        height: 100px;
        width: 100px;
        object-fit: cover;
        opacity: 0.7
    }

    .list-item-images {
        display: flex;
        overflow: hidden;
        overflow-x: scroll;
        height: 140px;
        background: white;
    }

    .list-item-img {
        position: relative;
    }

    .list-item-time {
        position: absolute;
        top: 0;
        padding-left: 8px;
        padding-top: 8px;
        color: black;
        font-weight: 900;
        font-size: 12px;
    }
</style>
