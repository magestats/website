require('./bootstrap');
window.Vue = require('vue');
Vue.component('repository-pullrequests', require('./components/repository-pullrequests.vue'));
Chart.defaults.global.animation.duration = 0;
Chart.defaults.global.hover.animationDuration = 0;
Chart.defaults.global.responsiveAnimationDuration = 0;
const app = new Vue({
    el: '#app',
    data: {
        response: '',
        errors: []
    },
    methods: {
        loadFromStorage: function (url, callback)
        {
            var self = this;
            axios.get('/storage' + url).then(function (response) {
                self.response = response.data;
                callback(callback);
            }).catch(function (e) {
                //
            });
        }
    }
});
