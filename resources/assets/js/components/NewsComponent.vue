<template>
    <div class="news">
        <a v-bind:href="url" target="_blank">
            <div class="image" v-bind:style="{ 'background-image':'url(' + (image ? image : '/images/placeholder.jpg')  + ')' }"></div>
        </a>
        <div class="content">
            <div class="title">
                <h5>
                    <a v-bind:href="url" target="_blank">{{ title }}</a>
                </h5>
            </div>
            <div class="text">{{ text }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: String,
            url: String,
            text: String,
            image: String
        },
        data() {
            return {}
        },
        methods: {},
        created() {
        },
        mounted(){
        }
    }
</script>
