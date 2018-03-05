<template>
    <div class="row no-gutters">
        <div class="col">
            <div class="chat-box">
                <div class="chat-content" ref="chatContent">
                    <ul>
                        <li v-for="msg in messages" class="clearfix">
                            <template v-if="msg.isWeatherInfo">
                                <weather-component
                                    :summary="msg.data.summary"
                                    :temperature="msg.data.temperature"
                                    :temperatureLow="msg.data.temperatureLow"
                                    :temperatureHigh="msg.data.temperatureHigh"
                                    :apparentTemperature="msg.data.apparentTemperature"
                                    :apparentTemperatureLow="msg.data.apparentTemperatureLow"
                                    :apparentTemperatureHigh="msg.data.apparentTemperatureHigh"
                                    :icon="msg.data.icon"
                                    :windSpeed="msg.data.windSpeed"
                                    :humidity="msg.data.humidity"
                                    :visibility="msg.data.visibility"
                                >
                                </weather-component>
                            </template>
                            <template v-else-if="msg.isNewsInfo">
                                <div class="articles">
                                    <div v-for="article in msg.data">
                                        <news-component
                                            :title="article.title"
                                            :url="article.url"
                                            :image="article.main_image_url"
                                            :text="article.text">
                                        </news-component>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div :class="{'user-msg': msg.isUser, 'watson-msg': !msg.isUser}" class="text">
                                    {{ msg.text }}
                                </div>
                            </template>
                        </li>
                    </ul>
                </div>
                <div class="chat-input">
                    <form class="chat-form" @submit="enterMessage($event)">
                        <input type="text" v-model="userMessage" ref="input" />
                        <button class="btn btn-primary" @click="enterMessage">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-container-fluid">
                <div class="clearfix position-relative">
                    <div class="upper-right-box">
                        <div class="current-location h3">
                            <i class="fas fa-map-marker"></i>
                            {{ address }}
                        </div>
                        <clock-component></clock-component>
                    </div>
                    <div class="float-left">
                        <div class="day h4">
                            {{ moment(day).format("dddd, MMMM Do YYYY") }}
                        </div>
                    </div>
                </div>

                <div class="weather clearfix">
                    <div class="temperature float-left">
                        <div class="temp h1">{{ Math.round(weather.temp) }}<sup>o</sup>F</div>
                        <div class="apparent-temp h5">Feel like {{ Math.round(weather.apparentTemp) }}<sup>o</sup>F</div>
                        <div class="summary">{{ weather.summary }}</div>
                    </div>
                    <canvas class="weather-icon" width="128" height="128"></canvas>
                </div>


                <div class="articles-wrapper" v-if="articles">
                    <h2>Recent News in Area</h2>
                    <div class="articles">
                        <div class="row">
                            <div v-for="article in articles" class="col-6">
                                <news-component
                                        :title="article.title"
                                        :url="article.url"
                                        :image="article.image"
                                        :text="article.text"></news-component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /**
     * The main component of the application
     */
    export default {
        props: {
            // google map api key
            googleMapAPIKey: String
        },

        data() {
            return {
                userMessage: '',
                // default position is TopCoder's building
                position: {
                    lat: 39.763789,
                    lng: -86.161428
                },
                address: '',
                day: new Date(),
                weather: {
                    temp: null,
                    apparentTemp: null,
                    summary: null,
                    icon: null
                },
                messages: [],
                context: null,
                // articles on the right panel, display articles relating to celebrations and activities in the city
                articles: []
            };
        },
        methods: {
            /**
             * get current time
             */
            moment: function () {
                return moment();
            },

            /**
             * get user current location
             */
            getCurrentPosition() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        this.position.lat = position.coords.latitude;
                        this.position.lng = position.coords.longitude;
                        this.updateCurrentCityAndWeather();
                    }, () => {
                        this.updateCurrentCityAndWeather();
                    });
                }
            },

            /**
             * get current city info and current weather in the city
             */
            updateCurrentCityAndWeather() {
                this.getCurrentCity().then(() => {
                    /**
                     * get articles about celebrations and activities in the city
                     */
                    this.getCelebrationsAndActivities().then(data => {
                        for (let article of data.results) {
                            this.articles.push({
                                'title': article.title,
                                'url': article.url,
                                'text': article.text,
                                'image': article.main_image_url
                            });
                        }

                        if (this.articles.length % 2 === 1) {
                            this.articles.splice(-1, 1);
                        }
                    });
                });

                /**
                 * get weather information
                 */
                this.getWeather();
            },

            /**
             * get the city name based on the current user lattitude and longitude
             * @returns {PromiseLike<TResult2|TResult1>|PromiseLike<TResult|T>|PromiseLike<T>|PromiseLike<TResult>}
             */
            getCurrentCity() {
                return this.$http.get('https://maps.googleapis.com/maps/api/geocode/json',
                    {
                        params: {
                            'latlng': this.position.lat + ',' + this.position.lng,
                            'key': this.googleMapAPIKey
                        }
                    })
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        for (let result of data.results) {
                            if (result.types.indexOf('locality') > -1) {
                                this.address = result.formatted_address;
                                break;
                            }
                        }
                    });
            },

            /**
             * get weather information of a date
             * @param date
             * @returns {PromiseLike<TResult2|TResult1>|PromiseLike<TResult|T>|PromiseLike<T>|PromiseLike<TResult>}
             */
            getWeatherSummaryOfDate(date) {
                let params = this.position;
                params.day = date;

                return this.$http.get('/api/weather', {params: this.position})
                    .then(response => {
                        return response.json();
                    });
            },

            /**
             * get weather information and display weather animation
             */
            getWeather() {
                this.$http.get('/api/weather', {params: this.position})
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        this.weather.temp = data.temperature;
                        this.weather.apparentTemp = data.apparentTemperature;
                        this.weather.summary = data.summary;
                        this.weather.icon = data.icon;
                        let skycons = new Skycons({"color": "#5aaafa"});
                        let fn;
                        switch (this.weather.icon) {
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
                    });
            },

            /**
             * send message to the server and get the response message
             * @param event
             */
            enterMessage(event) {
                event.preventDefault();
                event.stopPropagation();

                this.messages.push({
                    text: this.userMessage,
                    isUser: true,
                    isWeatherInfo: false,
                    isNewsInfo: false
                });

                this.sendMessage(this.userMessage).then(() => {
                    // scroll to the end of the chat box
                    this.$refs.chatContent.scrollTop = this.$refs.chatContent.scrollHeight;
                });

                this.userMessage = '';
                this.$refs.input.focus();
            },

            /**
             * call API send message
             * @param msg
             * @returns {PromiseLike<TResult2|TResult1>|PromiseLike<TResult|T>|PromiseLike<T>|PromiseLike<TResult>}
             */
            sendMessage(msg) {
                let data = {
                    msg: msg
                };
                if (this.context) {
                    data.context = this.context;
                }
                return this.$http.post('/api/send-message', data)
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        let text = '';
                        for (let txt of data.output.text) {
                            if (txt !== '') {
                                text = txt;
                            }
                        }
                        this.messages.push({
                            text: text,
                            isUser: false,
                            isWeatherInfo: false,
                            isNewsInfo: false
                        });
                        this.context = data.context;

                        if (data.intents && data.intents.length === 1) {
                            // get further information about weather, food news, music news or sport news depending
                            // on the intent responded from the API
                            let intent = data.intents[0];
                            if (intent.intent === 'weather') {
                                let date = null;
                                for (let entity of data.entities) {
                                    if (entity.entity === 'sys-date') {
                                        date = entity.value;
                                        break;
                                    }
                                }
                                this.getWeatherSummaryOfDate(date).then(data => {
                                    this.messages.push({
                                        text: this.userMessage,
                                        isUser: true,
                                        isWeatherInfo: true,
                                        isNewsInfo: false,
                                        data: data
                                    });

                                    this.$refs.chatContent.scrollTop = this.$refs.chatContent.scrollHeight;
                                });
                            } else {
                                if (intent.intent === 'select_music_genre'
                                    || intent.intent === 'select_food_cuisine'
                                    || intent.intent === 'select_sport_game') {
                                    let cat = '';
                                    let keyword = '';
                                    for (let entity of data.entities) {
                                        if (entity.entity === 'music_genre'
                                            || entity.entity === 'food_cuisine'
                                            || entity.entity === 'sport_activities') {
                                            keyword = entity.value;
                                        } else {
                                            if (entity.entity === 'type') {
                                                cat = entity.value;
                                            }
                                        }
                                    }

                                    // request news
                                    this.getCelebrationsAndActivities(cat, keyword).then(data => {
                                        this.messages.push({
                                            text: this.userMessage,
                                            isUser: true,
                                            isWeatherInfo: false,
                                            isNewsInfo: true,
                                            data: data.results
                                        });

                                        this.$refs.chatContent.scrollTop = this.$refs.chatContent.scrollHeight;
                                    });
                                }
                            }
                        }
                    });
            },

            /**
             * call request news API
             * @param cat
             * @param keyword
             * @returns {PromiseLike<TResult2|TResult1>|PromiseLike<TResult|T>|PromiseLike<T>|PromiseLike<TResult>}
             */
            getCelebrationsAndActivities(cat = '', keyword = '') {
                let params = {
                    'city': this.address
                };
                if (cat !== '') {
                    params.cat = cat;
                }
                if (keyword !== '') {
                    params.keyword = keyword;
                }

                return this.$http.get('/api/activities-celebrations',{params: params})
                    .then(response => {
                        return response.json();
                    }
                );
            }
        },

        created() {
            this.articles = [];
            this.getCurrentPosition();

            // get the welcome message
            this.sendMessage();
        },

        mounted(){
            // focus on the input chat box
            this.$refs.input.focus();
        }
    }
</script>
