<template>
    <span>
        <div class="row">
            <div class="col">
                <div class="card mb-3">
                    <div class="navbar navbar-expand-lg navbar-light navbar-laravel pl-0 pr-0">
                        <div class="container pl-0 pr-0">
                            <div class="card-heading ml-2">
                                <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" :data-target="'#'+this.id" aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                            </div>
                            <div class="collapse navbar-collapse" :id="this.id">
                                <div class="card-body">
                                    <h5 class="card-title"><i :class="this.icon"></i> {{this.title}}</h5>
                                    <span class="card-text">
                                        <div class="list-group list-group-flush"> 
                                            <slot></slot>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
    export default {
        props: ['id', 'title', 'items', 'icon']
    }
</script>
