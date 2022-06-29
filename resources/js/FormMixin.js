export default {
    data() {
      return {
        fields: {},
        errors: {},
        success: false,
        loaded: true,
        action: '',
      }
    },
  
    methods: {
  
      submit() {
        if (this.loaded) {
          this.loaded = false;
          this.success = false;
          this.errors = {};
          fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: this.fields,
          }).catch(error => {
            this.loaded = true;
            if (error.response.status === 422) {
              this.errors = error.response.data.errors || {};
            }
          });
        }
      },
    },
  }


//   axios.post(this.action, this.fields).then(response => {
//     this.fields = {}; //Clear input fields.
//     this.loaded = true;
//     this.success = true;
//   }).catch(error => {
//     this.loaded = true;
//     if (error.response.status === 422) {
//       this.errors = error.response.data.errors || {};
//     }
//   });