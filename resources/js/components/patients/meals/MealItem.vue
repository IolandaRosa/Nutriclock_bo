<template>
    <div class="list-item-container" v-if="this.data.length > 0">
        <div class="list-item-type">
            {{ title }}
        </div>
        <div class="list-item-images">
            <div v-for="m in this.data" class="list-item-img">
                <div v-if="m.meal.foodPhotoUrl">
                    <img
                        :src="`https://nutriclock.s3-eu-west-1.amazonaws.com/food/thumb_${m.meal.foodPhotoUrl}`"
                        @error="setAltImage"
                        alt=""
                        class="list-item-image"
                    />
                </div>

                <div v-else class="list-item-image">
                    <img
                        src="https://nutriclock.herokuapp.com/images/placeholder.jpg"
                        alt=""
                        class="list-item-image"
                    />
                </div>
                <div class="list-item-time">{{m.meal.time}}h</div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    export default{
        props: ['title', 'data'],
        methods: {
            setAltImage(event) {
                event.target.src = 'https://nutriclock.herokuapp.com/images/placeholder.jpg'
            }
        },
    };
</script>
