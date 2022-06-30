<template>
    <div class="vld-parent">
        <!-- <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :on-cancel="onCancel"
                 :is-full-page="fullPage"/> -->

        <form @submit.prevent="submit" @keydown="form.onKeydown($event)">
            <div class="formMain">
                <input type="text" name="streetNumber" v-model="form.streetNumber" placeholder="Street Number">
                <div v-if="form.errors.has('streetNumber')" v-html="form.errors.get('streetNumber')" />
                <input type="number" name="unitNumber" v-model="form.unitNumber" placeholder="Unit Number">
                <div v-if="form.errors.has('unitNumber')" v-html="form.errors.get('unitNumber')" />
                <input type="text" name="streetName" v-model="form.streetName" placeholder="Street Name">
                <div v-if="form.errors.has('streetName')" v-html="form.errors.get('streetName')" />
                <select name="streetType" v-model="form.streetType">
                    <option v-for="item in streetTypes" :key="item">
                        {{ item }}
                    </option>
                </select>
                <div v-if="form.errors.has('streetType')" v-html="form.errors.get('streetType')" />
            </div>

            <div class="formMain">
                <input type="text" name="suburb" v-model="form.suburb" placeholder="Suburb">
                <div v-if="form.errors.has('suburb')" v-html="form.errors.get('suburb')" />

                <select name="state" v-model="form.state">
                    <option v-for="st in states" :key="st" >{{ st }}</option>
                </select>
                <div v-if="form.errors.has('state')" v-html="form.errors.get('state')" />

                <input type="number" name="postCode" v-model="form.postCode" placeholder="Post Code">
                <div v-if="form.errors.has('postCode')" v-html="form.errors.get('postCode')" />

                <button type="submit" :disabled="form.busy" class="btn btn-primary">Submit</button>
            </div>
        </form>

    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import '../../css/app.css';
    import Form from 'vform';

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
                console.log('THIS', this.form);
                window.location.href = '/results' + this.form;
                // const response = await this.form.post('/results');
                console.log('FINDME', response);
            }
        }
    }
    
</script>