<template>
    <span>{{this.user.name}}</span>
</template>
<script>
import { mapState } from 'vuex';
export default {
    computed: {
        ...mapState(['user']),
    }
}
</script>

