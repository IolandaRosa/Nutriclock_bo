<template>
    <div class="d-flex align-items-center p-2 rounded with-shadow mb-2">
        <div class="flex-grow-1 mr-2 font-weight-bold">{{  this.question }}</div>
        <div>
            <img :src="`images/likert${Math.round(this.value)}.png`" alt="" width="30" height="30" class="mr-2"/>
        </div>
        <div class="font-weight-bold">({{ value }})</div>
    </div>
</template>


<script type="text/javascript">
/*jshint esversion: 6 */

export default {
    props: ['question', 'value'],
};
</script>
