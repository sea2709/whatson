<template>
    <div class="weather-info clearfix">
        <div class="float-left content">
            <div class="clearfix">
                <div class="temperature float-left">
                    <div class="temp h1">
                        <template v-if="temperatureLow && temperatureHigh">
                            {{ Math.round(temperatureLow) }} - {{ Math.round(temperatureHigh) }}
                        </template>
                        <template v-else>{{ Math.round(temperature) }}</template>
                        <sup>o</sup>F
                    </div>
                    <div class="apparent-temp h5">
                        Feel like
                        <template v-if="apparentTemperatureLow && apparentTemperatureHigh">
                        {{ Math.round(apparentTemperatureLow) }} - {{ Math.round(apparentTemperatureHigh) }}
                        </template>
                        <template v-else>
                            {{ Math.round(apparentTemperature) }}
                        </template>
                        <sup>o</sup>F
                    </div>
                </div>
                <div class="info">
                    <div class="summary">{{ summary }}</div>
                    <div class="wind-speed">Wind Speed : {{ windSpeed.toFixed(2) }}</div>
                    <div class="humidity">Humidity : {{ Math.round(humidity * 100) }}%</div>
                    <div class="visibility">Visibility : {{ visibility }}</div>
                </div>
            </div>
        </div>
        <div class="icon float-right">
            <canvas class="weather-icon" width="100" height="100"></canvas>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            summary: String,
            temperature: Number,
            temperatureLow: Number,
            temperatureHigh: Number,
            apparentTemperature: Number,
            apparentTemperatureLow: Number,
            apparentTemperatureHigh: Number,
            icon: String,
            windSpeed: Number,
            humidity: Number,
            visibility: Number
        },
        data() {
            return {
            }
        },
        methods: {

        },
        created() {
        },
        mounted(){
            let skycons = new Skycons({"color": "#5aaafa"});
            let fn;
            switch (this.icon) {
                case 'clear-day': {
                    fn = Skycons.CLEAR_DAY;
                }
                case 'clear-night': {
                    fn = Skycons.CLEAR_NIGHT;
                }
                case 'rain': {
                    fn = Skycons.RAIN;
                }
                case 'snow': {
                    fn = Skycons.SNOW;
                }
                case 'sleet': {
                    fn = Skycons.SLEET;
                }
                case 'wind': {
                    fn = Skycons.WIND;
                }
                case 'fog': {
                    fn = Skycons.FOG;
                }
                case 'cloudy': {
                    fn = Skycons.CLOUDY;
                }
                case 'partly-cloudy-day': {
                    fn = Skycons.PARTLY_CLOUDY_DAY;
                }
                case 'partly-cloudy-night': {
                    fn = Skycons.PARTLY_CLOUDY_NIGHT;
                }
            }
            skycons.add(this.$el.querySelector('.weather-icon'), fn);
            skycons.play();
        }
    }
</script>
