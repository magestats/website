require('./bootstrap');
var VueLazyload = require('vue-lazyload');
var VueFullPage = require('vue-fullpage.js');


window.Vue = require('vue');
Vue.component('repository-chart', require('./components/repository-chart.vue'));
Vue.component('contributors', require('./components/contributors.vue'));
Vue.component('top-contributors', require('./components/top-contributors.vue'));
Vue.use(VueLazyload);
Vue.use(VueFullPage);

Chart.defaults.global.animation.duration = 0;
Chart.defaults.global.hover.animationDuration = 0;
Chart.defaults.global.responsiveAnimationDuration = 0;
Chart.defaults.global.defaultFontFamily = 'Heebo, sans-serif';
Chart.defaults.global.defaultFontStyle = 300;
Chart.plugins.register({
    afterDatasetsDraw: function(chart) {
        var ctx = chart.ctx;
        chart.data.datasets.forEach(function(dataset, i) {
            var meta = chart.getDatasetMeta(i);
            if (!meta.hidden) {
                meta.data.forEach(function(element, index) {
                    // Draw the text in black, with the specified font
                    ctx.fillStyle = element._model.borderColor.replace('0.8', '1');

                    var fontSize = 12;
                    var fontStyle = 300;
                    var fontFamily = 'Heebo, sans-serif';
                    ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                    // Just naively convert to string for now
                    var dataString = dataset.data[index].toString();

                    // Make sure alignment settings are correct
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    var padding = 5;
                    var position = element.tooltipPosition();
                    if(dataString == '0') {
                        dataString = '';
                    }
                    ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                });
            }
        });
    }
});
Chart.plugins.register({
    afterDraw: function(chart) {
        var ctx = chart.ctx;
        var fontSize = 12;
        var fontStyle = 300;
        var fontFamily = 'Heebo, sans-serif';
        ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
        ctx.fillStyle = "rgba(50,50,50,0.4)";
        ctx.textAlign = 'right';
        ctx.textBaseline = 'bottom';
        ctx.fillText('© magestats.net', chart.chartArea.right-5, chart.chartArea.bottom);
    }
});
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

var topButton = $('#back-to-top');

$(window).scroll(function() {
    if ($(window).scrollTop() > 300) {
        topButton.addClass('show');
    } else {
        topButton.removeClass('show');
    }
});

topButton.on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({scrollTop:0}, '300');
});

new fullpage('#fullpage', {
    autoScrolling:true,
    scrollHorizontally: true,
    licenseKey: 'OPEN-SOURCE-GPLV3-LICENSE'
});