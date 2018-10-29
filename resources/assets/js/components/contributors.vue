<script>
    export default {
        data() {
            return {
                contributors: [],
            }
        },
        props: {
            'repo': String,
            'year': String
        },
        created () {
            this.loadData(this.year, this.repo);
        },
        methods: {
            loadData: function (year, repo) {
                this.$root.loadFromStorage('/' + year + '/' + repo + '.json', this.update);
            },
            update() {
                this.contributors = this.$root.response.contributors;

            }
        }
    }
</script>
<template>
        <div class="row contributors">
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2"  v-for="(values, contributor) of contributors">
            <div class="panel panel-default">
                <div class="panel-body" v-lazy-container="{ selector: 'img', error: '/images/magestats-icon-colord.png', loading: '/images/magestats-icon-colord.png' }">
                    <a :href="'/contributor/' + contributor"><img :data-src="values.avatar_url"/></a>
                    <p class="center"><a :href="'/contributor/' + contributor">{{ contributor }}</a></p>
                    <ul>
                        <li title="Merged Pull Requests">Merged: <span class="badge merged right">{{ values.merged }}</span></li>
                        <li title="Created Pull Requests">Created: <span class="badge created right">{{ values.created }}</span></li>
                        <li title="Closed Pull Requests">Closed: <span class="badge closed right">{{ values.closed }}</span></li>
                        <li v-if="values.rejected" title="Rejected Pull Requests">Rejected: <span class="badge rejected-dark-30 right">{{ values.rejected }}</span></li>
                        <li v-if="!values.rejected" title="Rejected Pull Requests">Rejected: <span class="badge rejected-dark-30 right">0</span></li>
                        <li v-if="values.acceptance_rate" title="Acceptance Rate">Acc. Rate: <span class="badge acceptance-rate right">{{ values.acceptance_rate }}</span></li>
                    </ul>
                </div>
            </div>
            </div>
        </div>
</template>

