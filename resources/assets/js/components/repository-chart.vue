<script>
    import { Bar, Line } from 'vue-chartjs';

    export default {
        extends: Bar,
        props: {
            'repo': String,
            'year': String
        },
        created () {
            this.loadData(this.year, this.repo);
        },
        methods: {
            renderLineChart: function ()
            {
                this.renderChart({
                    labels: this.$root.response.labels,
                    datasets: this.$root.response.datasets
                }, {responsive: true, maintainAspectRatio: false, elements: {line: {tension: 0}}})
            },

            loadData: function (year, repo) {
                this.$root.loadFromStorage('/' + year + '/' + repo + '.json', this.renderLineChart);
            }
        }
    }
</script>

