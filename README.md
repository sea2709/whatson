# What's On, Watson?

## Demo URL
https://dang.mybluemix.net/

## Prerequisite
This app is build by [Laravel 5.6](https://laravel.com/docs/5.6)
> PHP >= 7.1.3
> OpenSSL PHP Extension
> Mbstring PHP Extension
> Tokenizer PHP Extension
> XML PHP Extension
> Ctype PHP Extension
> JSON PHP Extension

> Google Map API key which allow to use Google Maps Geocoding API
> [DarkSky](https://darksky.net/dev) API key to get weather information 
> Credentials of [IBM Watson Discovery Service](https://www.ibm.com/watson/services/discovery/) (username, password, URL and workspace ID)
> Credentials of [IBM Watson Conversation Service](https://www.ibm.com/watson/services/conversation/) (username, password, URL, environment and collection)

## Quck Installation    
Run `composer install` for installing all needed dependencies

Run `php artisan serve`. Navigate to `http://locahost:8000`

## Deploy to IBM Cloud
This source code is build to deploy to [IBM Cloud](https://www.ibm.com/cloud/) with PHP Buildpack.

To build this source code to the IBM Cloud, enable service Continuous delivery and have a toolchain which are from IBM Cloud solution.
Make sure that this source code is pushed in a Git repository, because we will config the toolchain to get the sourcecode to deploy from a Git repository

Create Delivery Pipeline and create a deploy Stage with these configuration:

Config the input with this setting
>Input type: `Git repository`
>Input Git your source code repository settings : Git repository, Git URL, Branch  

Create a new job with this configuration
>Deploy type: `Cloud Foundry` 
>Application Name: `dang`
>Deploy script: `cf push "${CF_APP}"`

Then save the stage, trigger the stage to run. 
After this staged is finished, your app is deployed to your IBM cloud. 
Then you can launch your app by visiting the APP URL.
You can setup to automatically deploy your source code whenever there is a new push on the repository.

## Further help
To get more help about deploying a Laravel App to IBM cloud, please checkout 
>[Bluemix Laravel Demo](https://github.com/ibmjstart/Bluemix-Laravel-Demo)
>[Laravel on Bluemix](https://github.com/tarikgan/laravel-on-bluemix)
>[Watson Discovery News documentation](https://console.bluemix.net/docs/services/discovery/watson-discovery-news.html#watson-discovery-news)
>[Watson Conversation documentation](https://console.bluemix.net/docs/services/conversation/index.html#about)

### Environment and configuration files
Please find the env configuration files 
> `.env` config file for development environment
> `manifest.yml` config file for IBM cloud configuration and application configuration (which is inside the env configuration)

## 3rd party libraries used
> [Guzzle, PHP HTTP client](http://docs.guzzlephp.org/en/stable/)
> [Laravel Dark Sky](https://packagist.org/packages/naughtonium/laravel-dark-sky)
> [Skycons](https://darkskyapp.github.io/skycons/)
> [FontAwesome](https://fontawesome.com/)
> [Google Font Nunito](https://fonts.google.com/specimen/Nunito)