<template>
    <div class="container">
        <router-view></router-view>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.');
        },
    };
</script>
