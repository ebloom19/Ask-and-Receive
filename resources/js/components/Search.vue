<template>
    <div class="vld-parent">
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :on-cancel="onCancel"
                 :is-full-page="fullPage"/>

        <form @submit.prevent="submit">
            <div class="formMain">
                <input type="text" name="streetNumber" placeholder="Street Number">
                <div v-if="errors && errors.streetNumber" class="text-danger">{{ errors.streetNumber[0] }}</div>
                <input type="number" name="unitNumber" placeholder="Unit Number">
                <div v-if="errors && errors.unitNumber" class="text-danger">{{ errors.unitNumber[0] }}</div>
                <input type="text" name="streetName" placeholder="Street Name">
                <div v-if="errors && errors.streetName" class="text-danger">{{ errors.streetName[0] }}</div>
                <select name="streetType">
                    <option v-for="item in streetTypes" :key="item">
                        {{ item }}
                    </option>
                </select>
                <div v-if="errors && errors.streetType" class="text-danger">{{ errors.streetType[0] }}</div>
            </div>

            <div class="formMain">
                <input type="text" name="suburb" placeholder="Suburb">
                <div v-if="errors && errors.suburb" class="text-danger">{{ errors.suburb[0] }}</div>

                <select name="state">
                    <option v-for="st in states" :key="st" >{{ st }}</option>
                </select>
                <div v-if="errors && errors.state" class="text-danger">{{ errors.state[0] }}</div>

                <input type="number" name="postCode" placeholder="Post Code">
                <div v-if="errors && errors.postCode" class="text-danger">{{ errors.postCode[0] }}</div>

                <label><input type="checkbox" v-model="fullPage">Full page?</label>
                <button type="submit" @click.prevent="doAjax" class="btn btn-primary">Submit</button>

                <div v-if="success" class="alert alert-success mt-3">
                    Message sent!
                </div>
            </div>
        </form>

    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import '../../css/app.css';
    import axios from 'axios';

    export default {
        data() {
            return {
                isLoading: false,
                fullPage: true,
                streetTypes: ['Alley', 'Arcade', 'Avenue', 'Boulevard', 'Bypass', 'Circuit', 'Close', 'Corner', 'Court', 'Crescent', 'Cul-de-sac', 'Drive', 'Esplanade', 'Green', 'Grove', 'Highway', 'Junction', 'Lane', 'Link', 'Mews', 'Parade', 'Place', 'Ridge', 'Road', 'Square', 'Street', 'Terrace'],
                states: ["NSW", "VIC", "QLD", "TAS", "SA", "WA", "NT", "ACT"],
                fields: {},
                errors: {},
                success: false,
                loaded: true,
            }
        },
        components: {
            Loading
        },
        methods: {
            doAjax() {
                this.isLoading = true;
                // simulate AJAX
                setTimeout(() => {
                    this.isLoading = false
                }, 5000)
            },
            onCancel() {
                console.log('User cancelled the loader.')
            },
            submit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    axios.post(`/results${this.fields}`).then(response => {
                        this.fields = {}; //Clear input fields.
                        this.loaded = true;
                        this.success = true;
                    }).catch(error => {
                        this.loaded = true;
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
                }
            },
        }
    }
    
</script>