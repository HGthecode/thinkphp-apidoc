if(!self.define){const e=e=>{"require"!==e&&(e+=".js");let s=Promise.resolve();return a[e]||(s=new Promise((async s=>{if("document"in self){const a=document.createElement("script");a.src=e,document.head.appendChild(a),a.onload=s}else importScripts(e),s()}))),s.then((()=>{if(!a[e])throw new Error(`Module ${e} didn’t register its module`);return a[e]}))},s=(s,a)=>{Promise.all(s.map(e)).then((e=>a(1===e.length?e[0]:e)))},a={require:Promise.resolve(s)};self.define=(s,i,c)=>{a[s]||(a[s]=Promise.resolve().then((()=>{let a={};const d={uri:location.origin+s.slice(1)};return Promise.all(i.map((s=>{switch(s){case"exports":return a;case"module":return d;default:return e(s)}}))).then((e=>{const s=c(...e);return a.default||(a.default=s),a}))})))}}define("./service-worker.js",["./workbox-7dafdff6"],(function(e){"use strict";e.setCacheNameDetails({prefix:"mr-hope"}),self.addEventListener("message",(e=>{e.data&&"SKIP_WAITING"===e.data.type&&self.skipWaiting()})),e.clientsClaim(),e.precacheAndRoute([{url:"assets/css/0.styles.3ced7cf1.css",revision:"c7ebe2a037612ca49eb7fd0110530988"},{url:"assets/img/danger-dark.7b1d6aa1.svg",revision:"7b1d6aa1bdcf013d0edfe316ab770f8e"},{url:"assets/img/danger.b143eda2.svg",revision:"b143eda243548a9982491dca4c81eed5"},{url:"assets/img/default-skin.b257fa9c.svg",revision:"b257fa9c5ac8c515ac4d77a667ce2943"},{url:"assets/img/info-dark.f8a43cf6.svg",revision:"f8a43cf67fa96a27a078530a3a43253c"},{url:"assets/img/info.88826912.svg",revision:"88826912d81d91c9e2d03164cd1481a1"},{url:"assets/img/search.83621669.svg",revision:"83621669651b9a3d4bf64d1a670ad856"},{url:"assets/img/tip-dark.075a244c.svg",revision:"075a244c83d1403c167defe81b4d7fe7"},{url:"assets/img/tip.a2b80aa5.svg",revision:"a2b80aa50b769a26da12fe352322a657"},{url:"assets/img/warning-dark.aac7e30c.svg",revision:"aac7e30c5fafc6748e21f7a9ef546698"},{url:"assets/img/warning.ec428b6d.svg",revision:"ec428b6d6d45ac5d0c610f08d757f40f"},{url:"assets/js/38.822c433d.js",revision:"63d3cc464f695ea4f717b9f03d38e630"},{url:"assets/js/39.a1dd2445.js",revision:"9054648cc77ee7b4ea1d76f20907d3c1"},{url:"assets/js/40.cd1d3711.js",revision:"7249aad8c30ade53a7edbfc0d95df8cf"},{url:"assets/js/app.882556b8.js",revision:"4b05bcd5555f0bcb36a8a27e6d548e35"},{url:"assets/js/layout-Blog.26c3dad4.js",revision:"7f2d96dfa45b66a4a116a7f0892d2b1e"},{url:"assets/js/layout-Layout.85d11602.js",revision:"e5b24bf52e3169e1f19f8d202283945f"},{url:"assets/js/layout-NotFound.2ddaa33d.js",revision:"ccbbafb84f5cfb9ff7af6cda4cfb4b73"},{url:"assets/js/layout-Slide.8a31f320.js",revision:"630844b40488b8edc6d88c5f19dcd5d1"},{url:"assets/js/page-Markdown文档.84d1d6db.js",revision:"4b29c4dbbd779ae22e75a66f5b12df8d"},{url:"assets/js/page-TP6新项目的创建与Apidoc的安装.facdbed9.js",revision:"d6c07013a63763d01830687de12866de"},{url:"assets/js/page-V10升级V20报错.fb37f2ee.js",revision:"73dbe74afe4fe7f0ab7edf3eadb771e5"},{url:"assets/js/page-介绍.f45d14a5.js",revision:"ffd6a863e5f1e7c202e8086891a8366d"},{url:"assets/js/page-使用GithubActions实现TP6自动化部署.1b55033a.js",revision:"f3715a84863faaf63d15447b5701f033"},{url:"assets/js/page-使用phpstudy搭建TP6运行环境.dd46860b.js",revision:"f24d5c40368c5c84d0a62a63e3c96e0f"},{url:"assets/js/page-各种参数类型的注解教程.7ea8d978.js",revision:"e91dd74daf9fc6fcc69bf06da995563b"},{url:"assets/js/page-多应用多版本.984d8888.js",revision:"5a62d827eabdfbd0819599eb14d885fc"},{url:"assets/js/page-安装升级.9968a524.js",revision:"357212d2b382b5ea8d415bed89d0597b"},{url:"assets/js/page-密码验证.d92a84eb.js",revision:"c02af960a15ea58ceea088378127e7b0"},{url:"assets/js/page-建议及规范.6b69a888.js",revision:"8ef883b67bc5a9e4f3184cbef9eda43e"},{url:"assets/js/page-引入解释文件.1839f9b3.js",revision:"639022e72bc9ece3cabbd86aa35eca49"},{url:"assets/js/page-快速生成CRUD.4e0e121d.js",revision:"bfd4d22662b969e078dffef0d96e116e"},{url:"assets/js/page-指南.22c059fa.js",revision:"12d9e63bd3f0cfdb4d4bc929c9b7e159"},{url:"assets/js/page-接口参数Param、Returned注解技巧.79e96dbe.js",revision:"b7ab5d33ec87cbdbdce56ab6fcd67c11"},{url:"assets/js/page-接口注释.a9a63069.js",revision:"b0eaebbc7b62037c43e7fc466a2c6fa3"},{url:"assets/js/page-接口调试.1cf0ee7b.js",revision:"864ff96528dc09aa0c5cba6dc4a6c438"},{url:"assets/js/page-控制器分组.65a7f25b.js",revision:"f24573928a3dfd81b9df69ea32a319a7"},{url:"assets/js/page-控制器注释.7a837202.js",revision:"deec30fd3622291331535f97a3b8d6f1"},{url:"assets/js/page-教程.69f6ec2b.js",revision:"436230afbb986ff8c6e25d835f53abd4"},{url:"assets/js/page-文档缓存.5284efe7.js",revision:"5a576e654c65377e72f98cdcb642e239"},{url:"assets/js/page-更新日志.e143406a.js",revision:"ca6d2939ab5444e3fe9ecd186e5c4d6a"},{url:"assets/js/page-没有生成apidocphp配置文件.f80cafb0.js",revision:"56c729fcd4a75a3c37638360dc474c98"},{url:"assets/js/page-配置参数.dd3e817c.js",revision:"5a861f58c2a4e667fd4cc4cda8f1f887"},{url:"assets/js/page-页面404错误.2b3446ee.js",revision:"c4b6eee75210c622985354f3a206a892"},{url:"assets/js/page-页面500错误.bf43cda8.js",revision:"b272722cde060920770b23d01310287d"},{url:"assets/js/page-页面布局.a18ac79f.js",revision:"f27d8cb3f618ca242cec69b9e15a8436"},{url:"assets/js/page-页面配置.bf6db25a.js",revision:"502ef43ccc6f4ecc7012c81dc3cfbb69"},{url:"assets/js/vendors~layout-Blog~layout-Layout~layout-NotFound.a0178148.js",revision:"13cf464b28026154409967db43d49a31"},{url:"assets/js/vendors~layout-Blog~layout-Layout~layout-NotFound~layout-Slide.03ba6a36.js",revision:"7204388c3bbc1471554ad69c7bbf6ed3"},{url:"assets/js/vendors~layout-Layout.322e0a3a.js",revision:"e75601a48b03f23f0a4a0541afabe916"},{url:"assets/js/vendors~photo-swipe.00de1f37.js",revision:"a5f249ecfe640a031ec3b7ff95a8f9e0"},{url:"404.html",revision:"e47c6c6ddf572015781c04f0b79a5bbc"},{url:"config/index.html",revision:"6dbfc1624ad03dac92af575ce9e1c916"},{url:"config/page/index.html",revision:"b52b3e08b9af9fbf5bf71df974c55294"},{url:"course/apiParam/index.html",revision:"1a869290f5cf2c107929bf64ec4896f2"},{url:"course/createTpAndInstall/index.html",revision:"c7eda1e11ed99f896fad057266a19262"},{url:"course/githubActionsDeploy/index.html",revision:"e7127e87b5cbcabb68428c550c597482"},{url:"course/index.html",revision:"512d965bd4de3b4681e32dfb6ec5f689"},{url:"course/paramSkills/index.html",revision:"a6bb2336789176cc5e36dc2378dd242c"},{url:"course/phpStudyInstall/index.html",revision:"fc060d101ccdabb00b6d77b8e3bf2ea8"},{url:"guide/changelog/index.html",revision:"c08fb77956270b468cc7236293f7f7ac"},{url:"guide/index.html",revision:"762b12fef6d0d34cf59f3e5da9eca56f"},{url:"guide/install/index.html",revision:"32d16a428fe80e8804d802170a4f2ff4"},{url:"index.html",revision:"7046d43bd22e1a6d98a01807c21f421f"},{url:"use/function/apps/index.html",revision:"ca768194a10e85c1ccda02a49086f973"},{url:"use/function/cache/index.html",revision:"a7586103b742ee08c30128426c3c2276"},{url:"use/function/controllerGroup/index.html",revision:"740b1694e2b8c5cb3333ac58fe5b9100"},{url:"use/function/crud/index.html",revision:"b53f1d30fcde593047e3f33af9bf8173"},{url:"use/function/docs/index.html",revision:"c2b3c2fa6266b70625919b5a2e4b9aa5"},{url:"use/function/password/index.html",revision:"90dce841ca421412803bdd8070d4ece1"},{url:"use/help/404/index.html",revision:"cb26545318eb1ff4371a15f0d60e75f0"},{url:"use/help/500/index.html",revision:"db4cc05e9484d8b6781173206a13d1ca"},{url:"use/help/notConfig/index.html",revision:"4f497f3b4e1191537f118412aa2004fc"},{url:"use/help/v1Tov2/index.html",revision:"ee15838cd3ae0c0c13631bcfa4f30347"},{url:"use/index.html",revision:"dbc198d80d89f1d51cebaaf85d20e6da"},{url:"use/notes/api/index.html",revision:"31db39492a99cfab2a20975fa4a99dc0"},{url:"use/notes/controller/index.html",revision:"eea0e793922982cdca390ebe2fa160ba"},{url:"use/notes/index.html",revision:"a3284517ca74c916e346bd48295c2245"},{url:"use/notes/useFile/index.html",revision:"f55e5aba901bd600378eec7c2f57c3e8"},{url:"use/operation/apidebug/index.html",revision:"1ef76d5f49d52db8ccf51ca3b88797ba"},{url:"use/operation/layout/index.html",revision:"da2ba25b8c01ecc39c8a2ea31975c018"},{url:"assets/icon/apple-icon-152.png",revision:"f80145aa55bdcd965ca90e40c7bf852c"},{url:"assets/icon/chrome-192.png",revision:"f1aa7a38848d97a4328f561ce4c25fe1"},{url:"assets/icon/chrome-512.png",revision:"b36b4238ca28da6995301ef2637e06ce"},{url:"assets/icon/chrome-mask-192.png",revision:"71a4c2f46e621bf85b6a1bcd8c21560c"},{url:"assets/icon/chrome-mask-512.png",revision:"bb407756d4070797d4b4e0986c71e632"},{url:"assets/icon/guide-maskable.png",revision:"99cc77cf2bc792acd6b847b5e3e151e9"},{url:"assets/icon/guide-monochrome.png",revision:"699fa9b069f7f09ce3d52be1290ede20"},{url:"assets/icon/ms-icon-144.png",revision:"bccb7c7b07285b900091c7618fe1171a"},{url:"images/apidoc-api-base-demo.png",revision:"dc6db87c76980646dd89d801e7d2fad3"},{url:"images/apidoc-api-complete-demo.png",revision:"0f1b3e12ca365f2c1dec82342cb96510"},{url:"images/apidoc-api-dictionary-demo.png",revision:"e2c83946e7669a4b2943d7e505bd449c"},{url:"images/apidoc-api-model-demo.png",revision:"4920f55448d8f0ff7dd34cc7b1a234cc"},{url:"images/apidoc-api-page_debug.jpg",revision:"2d80868ba39955285baf3c22f2c172b5"},{url:"images/apidoc-api-page.jpg",revision:"10f8c091787dd3e142e51d4631fca29b"},{url:"images/apidoc-api-server-demo.png",revision:"0d04ef74eab3027a652f91c2b8781284"},{url:"images/apidoc-config_crud.png",revision:"dddcb3ecd0a963673725df4bd08e7437"},{url:"images/apidoc-controller-demo.png",revision:"647d55b314e74979f84299bd7fa11aac"},{url:"images/apidoc-demo-apps.png",revision:"db279004724dc47565acee78b9edf013"},{url:"images/apidoc-demo-md.png",revision:"b7f978a7285fcc3173dcb8b5faff6b08"},{url:"images/apidoc-help-404_error.png",revision:"1f0b312587fba9355ea02e35fc52babe"},{url:"images/apidoc-help-route404.png",revision:"00f5a734b5e0d1567bec18234a1acef2"},{url:"images/apidoc-help-use_error.png",revision:"38e6f12ce9120d87dbf02b5b777ad18a"},{url:"images/apidoc-home-page.jpeg",revision:"c47a3f25ead28d5754ffd73c2504bcd7"},{url:"images/course/apiParam/titlepic.jpg",revision:"24f8a97f9140532f5b42ceba929f5a1e"},{url:"images/course/githubActions/composer.png",revision:"dce4eed3259a7790e10acdafa71ac3d5"},{url:"images/course/githubActions/github-actions.png",revision:"b59f8481260fe60142d6c22e33915594"},{url:"images/course/githubActions/github-settings-key.png",revision:"eaedb35383ab85f12bf906ded5d2d74e"},{url:"images/course/githubActions/github-settings.png",revision:"f3329cdc8654e476164e78cc974302cf"},{url:"images/course/githubActions/titlepic.png",revision:"3149df4a493dc829e1e2f68ad32e765b"},{url:"images/course/install-1.png",revision:"033d2bcb91f1ddecf782b4ab50d946de"},{url:"images/course/install-2.png",revision:"368b137317a4a2e17309e106a4c4fd89"},{url:"images/course/install-3.png",revision:"7e80273c9be9dfa0dc53e3617c94484a"},{url:"images/course/install-4.png",revision:"59a163113aeb9b6c21ac21395a2d7938"},{url:"images/course/install-apidoc-1.png",revision:"5c34c0640652e3670988580215a38e3b"},{url:"images/course/install-apidoc-2.png",revision:"c9a7f968f6be9705e2b994ce57690dd9"},{url:"images/course/install-apidoc-3.png",revision:"557909af93f3a19610f8c0772205678c"},{url:"images/course/install-apidoc-4.png",revision:"0a9dc2601a45853e57ac4e66dc3b84c0"},{url:"images/course/install-apidoc-5.png",revision:"a084d350ab1a4066953fd03cb1e51c4e"},{url:"images/course/install-apidoc-6.png",revision:"ffb6cededbeeab5b588c764ff614b6bb"},{url:"images/course/php-study-install/install-1.png",revision:"356f7a4f3790ce393b60529e2717223f"},{url:"images/course/php-study-install/install-2.png",revision:"83379b0328db209771c29b9ecdf87003"},{url:"images/course/php-study-install/install-3.png",revision:"e98a5c9b2119e6e07eb42b1cc22c38e6"},{url:"images/course/php-study-install/install-4.png",revision:"be98f0da3c4edec2ba876c9de1c436eb"},{url:"images/course/php-study-install/install-5.png",revision:"b9fef03a021f58fc274f671b4c9cb006"},{url:"images/course/php-study-install/install-6.png",revision:"529dfe051c095770d62c13810da75d04"},{url:"images/course/php-study-install/install-7.png",revision:"c819e8538b5cff578447d1f7201065b3"},{url:"images/course/php-study-install/install-8.png",revision:"63771f6319b0d3c8f99c361e0c3614f5"},{url:"images/course/php-study-install/titlepic.png",revision:"22db0a43aa94063f622a3135d5215048"},{url:"images/course/titlepic.png",revision:"4f036983e6aea693343b039f2498aad6"},{url:"images/logo.png",revision:"b000157a61a423c6b357baf49fd19031"},{url:"logo.png",revision:"b000157a61a423c6b357baf49fd19031"}],{}),e.cleanupOutdatedCaches()}));
//# sourceMappingURL=service-worker.js.map
addEventListener("message", (event) => {
  const replyPort = event.ports[0];
  const message = event.data;
  if (replyPort && message && message.type === "skip-waiting")
    event.waitUntil(
      self.skipWaiting().then(
        () => replyPort.postMessage({ error: null }),
        (error) => replyPort.postMessage({ error })
      )
    );
});