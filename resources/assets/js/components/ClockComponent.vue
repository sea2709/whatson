<template>
    <div class="clock">
        <div class="clock-hours">
            <span v-text="hours"></span>
        </div>
        <div class="clock-minutes" v-text="minutes"></div>
        <div class="clock-seconds" v-text="seconds"></div>
    </div>
</template>

<script>
    /**
     * Clock component to display current time
     */
    import {getHourTime, getZeroPad} from '../Filters'

    export default {
        data () {
            return {
                hours: '',
                minutes: '',
                seconds: '',
                hourtime: ''
            }
        },
        mounted () {
            setInterval(this.updateDateTime, 1000)
        },
        methods: {
            updateDateTime () {
                let now = new Date()

                this.hours = now.getHours()
                this.minutes = getZeroPad(now.getMinutes())
                this.seconds = getZeroPad(now.getSeconds())
            }
        }
    }
</script>