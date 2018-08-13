require('./bootstrap');
window.Vue = require('vue');

Vue.component('repository', require('./components/repository.vue'));
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
