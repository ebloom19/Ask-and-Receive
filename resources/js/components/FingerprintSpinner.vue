<template>
  <div id="app">
    <fingerprint-spinner
        :animation-duration="1500"
        :size="64"
        color="#ff1d5e"
    />
  </div>
</template>

<script>
  // To use minified css and js files instead of .vue single file components:
  // import 'epic-spinners/dist/lib/epic-spinners.min.css'
  // import {AtomSpinner} from 'epic-spinners/dist/lib/epic-spinners.min.js'

  // To get tree shaking from webpack (won't import all spinners when you only need one)
  // import AtomSpinner from 'epic-spinners/src/components/lib/AtomSpinner'

  import { FingerprintSpinner } from 'epic-spinners'
  export default {
    components: {
      FingerprintSpinner
    }
  }
</script>
