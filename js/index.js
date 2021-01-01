
setTimeout(() => {
    imgView.init()
}, 1000);


var imgView = {
    init:function(){
        var imgList = document.querySelectorAll('.img-view');
        if (imgList && imgList.length) {
            for (let i = 0; i < imgList.length; i++) {
                const element = imgList[i];
                this.bindImgEvent(element)
            }
        }
    },
    bindImgEvent(el){
        console.log(el);
        const that=this
        el.onclick=function(e){
            console.log(e);
            const url = e.target.src;
            that.getImgWH(url).then(({width,height})=>{
                that.createModal({url,width,height})
            });
           
        }
        
    },
    createModal({url,width,height}){
        var winW =document.body.clientWidth;
        var winH =document.body.clientHeight;

        // 弹窗
        var imgViewModal = document.createElement("div");
        imgViewModal.setAttribute("id", "imgViewModal");
        imgViewModal.setAttribute("class","img-view-modal");
        //背景
        var mask = document.createElement("div");//js新建元素
        mask.setAttribute("class","img-view-modal_mask");
        mask.onclick = this.hideModal;//给元素添加点击事件
        //图片容器
        var imgViewWraper = document.createElement("div");
        imgViewWraper.setAttribute("class","img-view-modal_wraper");
        
        if (width<winW) {
            imgViewWraper.style.marginLeft = "-"+(width/2)+"px";
        }else{
            imgViewWraper.style.left='0px';
        }
        if (height<winH) {
            imgViewWraper.style.marginTop = "-"+(height/2)+"px";
        }else{
            imgViewWraper.style.top='0px';
        }

        //图片
        var img = document.createElement("img");
        img.src = url;
        if (width<winW) {
            img.style.width = width+"px";
            img.style.height = height+"px";
        }else{
            img.style.width = "100%";
        }
        imgViewWraper.appendChild(img);

        imgViewModal.appendChild(mask);
        imgViewModal.appendChild(imgViewWraper);

        document.body.appendChild(imgViewModal);
    },
    hideModal(){
        var box=document.getElementById("imgViewModal");
        box.remove();
    },
    getImgWH(url){
        return new Promise((resolve, reject) => {
            var img = new Image();
            img.src = url;
            let overtime=0;
            // 定时执行获取宽高
            var check = function(){
                overtime++;
                if(img.width>0 || img.height>0){
                    resolve({width:img.width,height:img.height})
                    clearInterval(set);
                }
                if (overtime>100) {
                    // 超时
                    reject()
                    clearInterval(set);
                }
            };
            var set = setInterval(check,40);
        })
    }
}