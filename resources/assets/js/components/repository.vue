<script>
    import { Line } from 'vue-chartjs';

    export default {
        extends: Line,
        props: {
            'repo': String,
            'year': String
        },
        created () {
            this.$root.loadFromStorage('/' + this.year + '/' + this.repo + '.json', this.renderLineChart);
        },

        methods: {
            renderLineChart: function ()
            {
                console.log(this.$root.response.datasets);
                this.renderChart({
                    labels: this.$root.response.labels,
                    datasets: this.$root.response.datasets
                }, {responsive: true, maintainAspectRatio: false})
            }
        }
    }
</script>