<template>
    <div class="container-fluid">
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :on-cancel="onCancel"
                 :is-full-page="fullPage"/>

        <form @submit.prevent="submit" @keydown="form.onKeydown($event)">
            <!-- <div v-if="$isMobile()" class="form-group">
                <label for="streetNumber">Street Number</label>
                <input type="text" name="streetNumber" v-model="form.streetNumber">
                <div v-if="form.errors.has('streetNumber')" v-html="form.errors.get('streetNumber')" />
                <label for="unitNumber">Unit Number</label>
                <input type="text" name="unitNumber" v-model="form.unitNumber">
                <div v-if="form.errors.has('unitNumber')" v-html="form.errors.get('unitNumber')" />
                <label for="streetName">Street Name</label>
                <input type="text" name="streetName" v-model="form.streetName">
                <div v-if="form.errors.has('streetName')" v-html="form.errors.get('streetName')" />
                <label for="streetType">Street Type</label>
                <select name="streetType" v-model="form.streetType">
                    <option v-for="item in streetTypes" :key="item">
                        {{ item }}
                    </option>
                </select>
                <div v-if="form.errors.has('streetType')" v-html="form.errors.get('streetType')" />
            </div> -->
            <div class="form-group d-flex flex-row align-items-center flex-wrap">
                <div class="d-flex flex-row m-1">
                    <label for="streetNumber" class="m-2">Street Number</label>
                    <input type="text" name="streetNumber" class="numberInput" v-model="form.streetNumber">
                    <div v-if="form.errors.has('streetNumber')" v-html="form.errors.get('streetNumber')" />
                </div>
                <div class="d-flex flex-row m-1">
                    <label for="unitNumber" class="m-2">Unit Number</label>
                    <input type="text" name="unitNumber" class="numberInput" v-model="form.unitNumber">
                    <div v-if="form.errors.has('unitNumber')" v-html="form.errors.get('unitNumber')" />
                </div>
                <div class="d-flex flex-row m-1">
                    <label for="streetName" class="m-2">Street Name</label>
                    <input type="text" name="streetName" v-model="form.streetName">
                    <div v-if="form.errors.has('streetName')" v-html="form.errors.get('streetName')" />
                </div>
                <div class="d-flex flex-row m-1">
                    <label for="streetType" class="m-2">Street Type</label>
                    <select name="streetType" v-model="form.streetType">
                        <option v-for="item in streetTypes" :key="item">
                            {{ item }}
                        </option>
                    </select>
                    <div v-if="form.errors.has('streetType')" v-html="form.errors.get('streetType')" />
                </div>
            </div>

            <div class="form-group d-flex flex-wrap">
                <div class="d-flex flex-row m-1">
                    <label for="suburb" class="m-2">Suburb</label>
                    <input type="text" name="suburb" v-model="form.suburb">
                    <div v-if="form.errors.has('suburb')" v-html="form.errors.get('suburb')" />
                </div>

                <div class="d-flex flex-row m-1">
                    <label for="state" class="m-2">State</label>
                    <select name="state" v-model="form.state">
                        <option v-for="st in states" :key="st" >{{ st }}</option>
                    </select>
                    <div v-if="form.errors.has('state')" v-html="form.errors.get('state')" />
                </div>

                <div class="d-flex flex-row m-1">
                    <label for="postCode" class="m-2">Post Code</label>
                    <input type="number" name="postCode" class="numberInput" v-model="form.postCode">
                    <div v-if="form.errors.has('postCode')" v-html="form.errors.get('postCode')" />
                </div>
                <button type="submit" :disabled="form.busy" class="btn btn-custom ml-4">Submit</button>

                <vue-google-autocomplete id="map" classname="form-control" placeholder="Suburb" v-on:placechanged="getAddressData">
                </vue-google-autocomplete>

            </div>

        </form>

    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import '../../css/app.css';
    import Form from 'vform';
    import VueToastify from "vue-toastify";
    import VueGoogleAutocomplete from "vue-google-autocomplete";

    Vue.use(VueToastify, {
        position: "top-right",
        successDuration: 7000,
        canTimeout: false
    });

    window.$ = window.jQuery = require('jquery')

    export default {
        data() {
            return {
                isLoading: true,
                fullPage: true,
                streetTypes: ['Alley', 'Arcade', 'Avenue', 'Boulevard', 'Bypass', 'Circuit', 'Close', 'Corner', 'Court', 'Crescent', 'Cul-de-sac', 'Drive', 'Esplanade', 'Green', 'Grove', 'Highway', 'Junction', 'Lane', 'Link', 'Mews', 'Parade', 'Place', 'Ridge', 'Road', 'Square', 'Street', 'Terrace'],
                states: ["NSW", "VIC", "QLD", "TAS", "SA", "WA", "NT", "ACT"],
                address: '',
                form: new Form({
                    streetNumber: '',
                    unitNumber: '',
                    streetName: '',
                    streetType: '',
                    suburb: '',
                    state: '',
                    postCode: '',
                }),
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
            async submit () {
                // this.methods.doAjax();
                this.$vToastify.success("Loading Results...");
                window.location.href = '/results?' + $.param({
                    streetNumber: this.form.streetNumber, 
                    unitNumber: this.form.unitNumber, 
                    unitNumber: this.form.unitNumber,
                    streetName: this.form.streetName,
                    suburb: this.form.suburb,
                    state: this.form.state,
                    postCode: this.form.postCode
                });
                // const response = await this.form.post('/results');
            },
            getAddressData: function (addressData, placeResultData, id) {
                this.address = addressData;
            },
        },
    }
    
</script>