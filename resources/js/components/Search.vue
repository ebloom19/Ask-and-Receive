<template>
    <div class="container-fluid">
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :on-cancel="onCancel"
                 :is-full-page="fullPage"/>

        <form @submit.prevent="submit" @keydown="form.onKeydown($event)">
            <div class="form-group d-flex flex-row align-items-center">
                <label for="streetNumber">Street Number</label>
                <input type="text" name="streetNumber" v-model="form.streetNumber">
                <div v-if="form.errors.has('streetNumber')" v-html="form.errors.get('streetNumber')" />
                <label for="unitNumber">Unit Number</label>
                <input type="text" name="unitNumber" v-model="form.streetNumber">
                <div v-if="form.errors.has('unitNumber')" v-html="form.errors.get('unitNumber')" />
                <label for="streetName">Street Name</label>
                <input type="text" name="streetName" v-model="form.streetNumber">
                <div v-if="form.errors.has('streetName')" v-html="form.errors.get('streetName')" />
                <label for="streetType">Street Type</label>
                <select name="streetType" v-model="form.streetType">
                    <option v-for="item in streetTypes" :key="item">
                        {{ item }}
                    </option>
                </select>
                <div v-if="form.errors.has('streetType')" v-html="form.errors.get('streetType')" />
            </div>

            <div class="form-group">
                <label for="suburb">Suburb</label>
                <input type="text" name="suburb" v-model="form.streetNumber">
                <div v-if="form.errors.has('suburb')" v-html="form.errors.get('suburb')" />
            </div>

            <div class="form-group">
                <label for="state">State</label>
                <select name="state" v-model="form.state">
                    <option v-for="st in states" :key="st" >{{ st }}</option>
                </select>
                <div v-if="form.errors.has('state')" v-html="form.errors.get('state')" />
            </div>

            <div class="form-group">
                <label for="postCode">Post Code</label>
                <select name="postCode" v-model="form.state">
                    <option v-for="st in states" :key="st" >{{ st }}</option>
                </select>
                <div v-if="form.errors.has('postCode')" v-html="form.errors.get('postCode')" />
            </div>

            <button type="submit" :disabled="form.busy" class="btn btn-primary">Submit</button>
        </form>

    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import '../../css/app.css';
    import Form from 'vform';

    window.$ = window.jQuery = require('jquery')

    export default {
        data() {
            return {
                isLoading: true,
                fullPage: true,
                streetTypes: ['Alley', 'Arcade', 'Avenue', 'Boulevard', 'Bypass', 'Circuit', 'Close', 'Corner', 'Court', 'Crescent', 'Cul-de-sac', 'Drive', 'Esplanade', 'Green', 'Grove', 'Highway', 'Junction', 'Lane', 'Link', 'Mews', 'Parade', 'Place', 'Ridge', 'Road', 'Square', 'Street', 'Terrace'],
                states: ["NSW", "VIC", "QLD", "TAS", "SA", "WA", "NT", "ACT"],
                form: new Form({
                    streetNumber: '',
                    unitNumber: '',
                    streetName: '',
                    streetType: '',
                    suburb: '',
                    state: '',
                    postCode: ''
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
                // $.param() *to query string*

                // console.log('FINDME', $.param({
                //     streetNumber: this.form.streetNumber, 
                //     unitNumber: this.form.unitNumber, 
                //     unitNumber: this.form.unitNumber,
                //     streetName: this.form.streetName,
                //     suburb: this.form.suburb,
                //     state: this.form.state,
                //     postCode: this.form.postCode
                // }), '&&&&', this.form);
            }
        }
    }
    
</script>