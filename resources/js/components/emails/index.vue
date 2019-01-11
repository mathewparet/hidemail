<template>
    <div>
        <div class="card card-default">
            <div class="card-header"><i class="fas fa-at"></i> My Hidden Email IDs</div>
            <div class="card-body">
                <div class="card-text">

                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data()
        {
            return {
                loading: null,
                emails: []
            }
        },
        methods: {
            loadEmails()
            {
                axios.get('/api/emails')
            }
        }
    }
</script>
<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>