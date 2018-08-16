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
            <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2"  v-for="(values, contributor) of contributors">
            <div class="panel panel-default">
                <div class="panel-body">
                    <clazy-load :src="values.avatar_url">
                        <div slot="placeholder">
                            <img src="/images/contributor-placeholder.png" alt="placeholder">
                        </div>
                    <a :href="'https://github.com/' + contributor"><img :src="values.avatar_url"/></a>
                    <p class="center"><a :href="'https://github.com/' + contributor">{{ contributor }}</a></p>
                    <ul>
                        <li>Merged: <span class="badge merged right" v-if="values.total.merged">{{ values.total.merged }}</span><span class="badge merged right" v-else>0</span></li>
                        <li>Created: <span class="badge created right" v-if="values.total.created">{{ values.total.created }}</span><span class="badge right" v-else>0</span></li>
                        <li>Closed: <span class="badge closed right" v-if="values.total.closed">{{ values.total.closed }}</span><span class="badge right" v-else>0</span></li>
                    </ul>
                    </clazy-load>
                </div>
            </div>
            </div>
        </div>
</template>

