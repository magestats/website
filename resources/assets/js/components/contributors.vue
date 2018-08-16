<script>
    export default {
        data() {
            return {
                contributors: [],
            }
        },
        props: {
            'repo': String,
            'year': String,
            'limit': String
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
                    <a :href="'https://github.com/' + contributor"><img :data-src="values.avatar_url"/></a>
                    <p class="center"><a :href="'https://github.com/' + contributor">{{ contributor }}</a></p>
                    <ul>
                        <li>Merged: <span class="badge merged right">{{ values.merged }}</span></li>
                        <li>Created: <span class="badge created right">{{ values.created }}</span></li>
                        <li>Closed: <span class="badge closed right">{{ values.closed }}</span></li>
                    </ul>
                </div>
            </div>
            </div>
        </div>
</template>

