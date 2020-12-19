<template>
    <div class="list-item" v-on:click="this.showDetails">
        <div class="list-item-date">{{this.date}}</div>
            <MealItem :data="this.meal['P']" title="Pequeno-almoço" />
            <MealItem :data="this.meal['M']" title="Meio da manhã" />
            <MealItem :data="this.meal['A']" title="Almoço" />
            <MealItem :data="this.meal['L']" title="Lanche" />
            <MealItem :data="this.meal['J']" title="Jantar" />
            <MealItem :data="this.meal['O']" title="Ceia" />
            <MealItem :data="this.meal['S']" title="Snacks" />
    </div>
</template>

<script type="text/javascript">
    import MealItem from './MealItem';
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
        components: {
            MealItem
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

    .list-item-image {
        height: 100px;
        width: 100px;
        object-fit: cover;
        opacity: 0.7;
        background: white;
        color: black;
        font-weight: 900;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
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
