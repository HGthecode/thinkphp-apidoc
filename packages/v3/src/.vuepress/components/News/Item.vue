<template>
    <a class="news-item" :href="url">
        <div v-if="image" class="image">
            <img :src="image" alt="">
        </div>
        <div class="info-wraper">
            <div class="title"><slot></slot></div>
            <div class="desc">{{desc}}<slot name="desc"></slot></div>
        </div>
    </a>
</template>

<script>
export default {
    props:{
        url:{
            type:String,
            default:""
        },
        desc:{
            type:String,
            default:""
        },
        image:{
            type:String,
            default:""
        }
    },
    methods:{
        toUrl(){

        }
    }
}
</script>

<style lang="css" scoped>
    
    .news-item{
        padding:10px;
        display: flex;
        border-bottom:1px solid #fafafa;
    }
    .theme-dark .news-item{
        border-bottom:1px solid #555;
    }
    .image{
        border-radius: 5px;
        overflow: hidden;
        width: 100px;
        height: 70px;
    }
    .image img{
        width: 100%;
        height: 100%;
    }
    .info-wraper{
        flex:1;
        padding:0 10px;
    }
    .title{
        font-size: 20px;
        margin-bottom: 10px;
    }
    .desc{
        color:#999;
        font-size: 16px;
    }
</style>