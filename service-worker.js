if(!self.define){const e=e=>{"require"!==e&&(e+=".js");let s=Promise.resolve();return a[e]||(s=new Promise((async s=>{if("document"in self){const a=document.createElement("script");a.src=e,document.head.appendChild(a),a.onload=s}else importScripts(e),s()}))),s.then((()=>{if(!a[e])throw new Error(`Module ${e} didn’t register its module`);return a[e]}))},s=(s,a)=>{Promise.all(s.map(e)).then((e=>a(1===e.length?e[0]:e)))},a={require:Promise.resolve(s)};self.define=(s,i,c)=>{a[s]||(a[s]=Promise.resolve().then((()=>{let a={};const r={uri:location.origin+s.slice(1)};return Promise.all(i.map((s=>{switch(s){case"exports":return a;case"module":return r;default:return e(s)}}))).then((e=>{const s=c(...e);return a.default||(a.default=s),a}))})))}}define("./service-worker.js",["./workbox-ed249f6c"],(function(e){"use strict";e.setCacheNameDetails({prefix:"mr-hope"}),self.addEventListener("message",(e=>{e.data&&"SKIP_WAITING"===e.data.type&&self.skipWaiting()})),e.clientsClaim(),e.precacheAndRoute([{url:"assets/css/0.styles.75d1ecf0.css",revision:"a2b600ba0deefae19467d1e94e74e473"},{url:"assets/img/danger-dark.7b1d6aa1.svg",revision:"7b1d6aa1bdcf013d0edfe316ab770f8e"},{url:"assets/img/danger.b143eda2.svg",revision:"b143eda243548a9982491dca4c81eed5"},{url:"assets/img/default-skin.b257fa9c.svg",revision:"b257fa9c5ac8c515ac4d77a667ce2943"},{url:"assets/img/info-dark.f8a43cf6.svg",revision:"f8a43cf67fa96a27a078530a3a43253c"},{url:"assets/img/info.88826912.svg",revision:"88826912d81d91c9e2d03164cd1481a1"},{url:"assets/img/search.83621669.svg",revision:"83621669651b9a3d4bf64d1a670ad856"},{url:"assets/img/tip-dark.075a244c.svg",revision:"075a244c83d1403c167defe81b4d7fe7"},{url:"assets/img/tip.a2b80aa5.svg",revision:"a2b80aa50b769a26da12fe352322a657"},{url:"assets/img/warning-dark.aac7e30c.svg",revision:"aac7e30c5fafc6748e21f7a9ef546698"},{url:"assets/img/warning.ec428b6d.svg",revision:"ec428b6d6d45ac5d0c610f08d757f40f"},{url:"assets/js/41.f2d16078.js",revision:"f8852df6f8e6570d095c52d9f20edb69"},{url:"assets/js/42.04c52807.js",revision:"7c573e1c5b6d36dd87b94c86df8865c5"},{url:"assets/js/43.128bff53.js",revision:"731c94e27e8bc61c9eea3480fcf0b421"},{url:"assets/js/app.9527da73.js",revision:"b61fd4894d31ad0b1c2f8e775df01d38"},{url:"assets/js/layout-Blog.2fa42e14.js",revision:"7f2d96dfa45b66a4a116a7f0892d2b1e"},{url:"assets/js/layout-Layout.85d11602.js",revision:"e5b24bf52e3169e1f19f8d202283945f"},{url:"assets/js/layout-NotFound.41d628ed.js",revision:"ccbbafb84f5cfb9ff7af6cda4cfb4b73"},{url:"assets/js/layout-Slide.a41f58b6.js",revision:"630844b40488b8edc6d88c5f19dcd5d1"},{url:"assets/js/page-Markdown文档.1c6cc5ca.js",revision:"f9c973bdaa8ccac033dcec7d0e123bec"},{url:"assets/js/page-Mock调试数据.e09e7899.js",revision:"54221bbb5d2c9c0de478ef149716c06e"},{url:"assets/js/page-TP6新项目的创建与Apidoc的安装.8af4d176.js",revision:"16314dd8f5718c3d867113ddaf58e390"},{url:"assets/js/page-V2x升级V3x指南.e830f7c4.js",revision:"b157e75b5902ec41182446d1a9ae4636"},{url:"assets/js/page-介绍.83cf1568.js",revision:"42669e721233882aaad62ca3327d65cd"},{url:"assets/js/page-代码生成器.fd126170.js",revision:"e82a34178f223f1ee6d73c41a571b42f"},{url:"assets/js/page-使用GithubActions实现TP6自动化部署.397a80e7.js",revision:"44c85ae5a68a876ee69129067ba3a8f7"},{url:"assets/js/page-使用phpstudy搭建TP6运行环境.51e7e9e1.js",revision:"f7e4c34b1a678a427cffbf19709fa254"},{url:"assets/js/page-前端配置.91965bfc.js",revision:"472dd27c6c25d508a15d9c7ed1140116"},{url:"assets/js/page-各种参数类型的注解教程.aaeca552.js",revision:"8824992debdb499d8bffbedd02e5a366"},{url:"assets/js/page-多应用多版本.f9a930df.js",revision:"5332076e13b6d4ed7737f568ab959643"},{url:"assets/js/page-多语言.50b327b2.js",revision:"6809b1ba35e934c40a1d6eec4d4baf39"},{url:"assets/js/page-安装升级.340408b6.js",revision:"b1c951ca62ab874145104c4b8f5c6211"},{url:"assets/js/page-建议及规范.610da079.js",revision:"0bb4297826f519520c3778845d413de1"},{url:"assets/js/page-引入解释文件.1866e6fd.js",revision:"f46d993ad81c59e52d369dc8ce43b760"},{url:"assets/js/page-指南.5981b293.js",revision:"a5763b920bbe9eb0c9d1af8e39921d0e"},{url:"assets/js/page-接口参数Param、Returned注解技巧.09e52956.js",revision:"4939fb3526b5e5e0e34ab15aa4c5143d"},{url:"assets/js/page-接口注释.3f1ff7f5.js",revision:"02d68a6e0887a99b313df12f6800b3d9"},{url:"assets/js/page-接口调试.92081547.js",revision:"6ede73c2e022e535fcdccc08043f6582"},{url:"assets/js/page-控制器分组.cc829ff1.js",revision:"8f67f882e0ee4e6899e6806f001b84dd"},{url:"assets/js/page-控制器注释.24944a2b.js",revision:"50f816803afe5be0dcf21e9f79c31dfc"},{url:"assets/js/page-教程.60af4ed0.js",revision:"4f0361fda878e5c7457b1b542993b7e0"},{url:"assets/js/page-文档缓存.cd9ee7e9.js",revision:"c1589c2a62f23a1ece0cf12a512df217"},{url:"assets/js/page-更新日志.d339ce4e.js",revision:"20030160f5a2d15af1630e5f2d646308"},{url:"assets/js/page-没有生成apidocphp配置文件.879694ee.js",revision:"625a991c1badedf6be8d5bab75fe91a1"},{url:"assets/js/page-访问密码.a35932a3.js",revision:"58d9aaf04b80960a8c175cc203abcb10"},{url:"assets/js/page-调试时的事件.3c1ce811.js",revision:"39c05daf208765735ec20716f674aa07"},{url:"assets/js/page-配置参数.ea5b5d5a.js",revision:"483ee9d2e090807d477a4c871b1d4020"},{url:"assets/js/page-页面404错误.6b543064.js",revision:"cb3694369320552bc3a87d2fa9239694"},{url:"assets/js/page-页面500错误.d9b49a67.js",revision:"e5475d473f83912148450528817568f3"},{url:"assets/js/page-页面布局.69138b7c.js",revision:"681f5323d44024eb298874686f8c9890"},{url:"assets/js/vendors~layout-Blog~layout-Layout~layout-NotFound.c13b613b.js",revision:"13cf464b28026154409967db43d49a31"},{url:"assets/js/vendors~layout-Blog~layout-Layout~layout-NotFound~layout-Slide.358419a0.js",revision:"7204388c3bbc1471554ad69c7bbf6ed3"},{url:"assets/js/vendors~layout-Layout.92f7a663.js",revision:"44b73316360ca46c51a7fa3606408883"},{url:"assets/js/vendors~photo-swipe.aade9084.js",revision:"2c9b06ad9bc2572137775bc9a71565a1"},{url:"404.html",revision:"6feb084b5bcfd92e1487ecd05a638ba5"},{url:"config/index.html",revision:"e42f2072944e8650f4a830325800a0ed"},{url:"config/page/index.html",revision:"f37387e5e661ee839983e98a355e5e7c"},{url:"course/apiParam/index.html",revision:"20603e29f24a667d0147300616d3312d"},{url:"course/createTpAndInstall/index.html",revision:"1a40653a17fae71ea433dc71a1f09cf6"},{url:"course/githubActionsDeploy/index.html",revision:"062b83a2b799fc645252b591fd239169"},{url:"course/index.html",revision:"f12ed723936874ccbc4fbfd70a9f4da3"},{url:"course/paramSkills/index.html",revision:"88be458bba9d12950bdc664ce4aee75c"},{url:"course/phpStudyInstall/index.html",revision:"b317bff7477f83bdb031b5b544f9abaa"},{url:"guide/changelog/index.html",revision:"782cb2dcca8c1f4de6bc530f73f7b8e5"},{url:"guide/index.html",revision:"49935e36bdc7b2e41a97e1d4797bc44c"},{url:"guide/install/index.html",revision:"bfb438108e27960149ee73e22b22a333"},{url:"index.html",revision:"75c8ddacf942e894675be04fca5d6270"},{url:"use/function/apps/index.html",revision:"37e217382b5d6a236ba0f9cdb3ec98bc"},{url:"use/function/cache/index.html",revision:"02d3f719fe31621b3dd65c71e7cbef0f"},{url:"use/function/controllerGroup/index.html",revision:"5c7c748dada9c39e5ac3f3ea96a5f893"},{url:"use/function/debugEvent/index.html",revision:"cbf0f410fca19042a506c6aef45a5863"},{url:"use/function/docs/index.html",revision:"72ea59b3e6924ced75a396247213aebf"},{url:"use/function/generator/index.html",revision:"7e1435a6f56c89db516407d50ad44a23"},{url:"use/function/lang/index.html",revision:"fcbb06c6c8cc3a37d9879e7d89c326fe"},{url:"use/function/mock/index.html",revision:"4acd5e0b938ec0d1a892bb81b687312e"},{url:"use/function/password/index.html",revision:"ebac9439920714de35c5b5cfc8ee36a5"},{url:"use/help/404/index.html",revision:"c2d734aa97d73cfce7aed57860010047"},{url:"use/help/500/index.html",revision:"28e4c20be5622f7faa5733c7275989f5"},{url:"use/help/notConfig/index.html",revision:"0f1b66512c7d391d08b4db1366a5428e"},{url:"use/help/v2Tov3/index.html",revision:"212bedc4330e3cc0ba73e7dfb956c012"},{url:"use/index.html",revision:"d9b126fd9128271a2075080934e7687e"},{url:"use/notes/api/index.html",revision:"376fc7215675d4c195f1afa3e978c96a"},{url:"use/notes/controller/index.html",revision:"b872b4676f3126e4411a846d3acf964b"},{url:"use/notes/index.html",revision:"6ed1c6c63e5e67b27f06f40a73e84bf2"},{url:"use/notes/useFile/index.html",revision:"30007a540104382cac6efdaf6d27b64b"},{url:"use/operation/apidebug/index.html",revision:"f8cafebdd738a15182e85ca676c759d0"},{url:"use/operation/layout/index.html",revision:"a10449bc76922a90bf78efc6d68ceb1f"},{url:"assets/icon/apple-icon-152.png",revision:"f80145aa55bdcd965ca90e40c7bf852c"},{url:"assets/icon/chrome-192.png",revision:"f1aa7a38848d97a4328f561ce4c25fe1"},{url:"assets/icon/chrome-512.png",revision:"b36b4238ca28da6995301ef2637e06ce"},{url:"assets/icon/chrome-mask-192.png",revision:"71a4c2f46e621bf85b6a1bcd8c21560c"},{url:"assets/icon/chrome-mask-512.png",revision:"bb407756d4070797d4b4e0986c71e632"},{url:"assets/icon/guide-maskable.png",revision:"99cc77cf2bc792acd6b847b5e3e151e9"},{url:"assets/icon/guide-monochrome.png",revision:"699fa9b069f7f09ce3d52be1290ede20"},{url:"assets/icon/ms-icon-144.png",revision:"bccb7c7b07285b900091c7618fe1171a"},{url:"images/apidoc-api-base-demo.png",revision:"bc8627c9a152086a3f84ead363532ffc"},{url:"images/apidoc-api-complete-demo.png",revision:"c5fa189101acc34b46ccb492187787b0"},{url:"images/apidoc-api-dictionary-demo.png",revision:"d6ce3a96f5038e78264c71e0967f18b7"},{url:"images/apidoc-api-model-demo.png",revision:"590cf66c15ea3499b04c6bf388f9ea70"},{url:"images/apidoc-api-page_debug.jpg",revision:"6e4266062877ba804bf70bba3e359865"},{url:"images/apidoc-api-page.jpg",revision:"1de3a949b9cf33f23f3919d1ce7406fd"},{url:"images/apidoc-api-server-demo.png",revision:"b09fcfb9040826083d9713c1ae196527"},{url:"images/apidoc-config_crud.png",revision:"250053f8e1c54e1418c945c373c230ed"},{url:"images/apidoc-controller-demo.png",revision:"647d55b314e74979f84299bd7fa11aac"},{url:"images/apidoc-demo-apps.png",revision:"a31d7ccf34565fc8e80cacad42968469"},{url:"images/apidoc-demo-md.png",revision:"8d8fdd9f507f2b34afb787b1f8b2b328"},{url:"images/apidoc-help-404_error.png",revision:"1f0b312587fba9355ea02e35fc52babe"},{url:"images/apidoc-help-route404.png",revision:"00f5a734b5e0d1567bec18234a1acef2"},{url:"images/apidoc-help-use_error.png",revision:"38e6f12ce9120d87dbf02b5b777ad18a"},{url:"images/apidoc-home-page.jpeg",revision:"c47a3f25ead28d5754ffd73c2504bcd7"},{url:"images/course/apiParam/titlepic.jpg",revision:"24f8a97f9140532f5b42ceba929f5a1e"},{url:"images/course/githubActions/composer.png",revision:"dce4eed3259a7790e10acdafa71ac3d5"},{url:"images/course/githubActions/github-actions.png",revision:"b59f8481260fe60142d6c22e33915594"},{url:"images/course/githubActions/github-settings-key.png",revision:"eaedb35383ab85f12bf906ded5d2d74e"},{url:"images/course/githubActions/github-settings.png",revision:"f3329cdc8654e476164e78cc974302cf"},{url:"images/course/githubActions/titlepic.png",revision:"3149df4a493dc829e1e2f68ad32e765b"},{url:"images/course/install-1.png",revision:"033d2bcb91f1ddecf782b4ab50d946de"},{url:"images/course/install-2.png",revision:"368b137317a4a2e17309e106a4c4fd89"},{url:"images/course/install-3.png",revision:"7e80273c9be9dfa0dc53e3617c94484a"},{url:"images/course/install-4.png",revision:"59a163113aeb9b6c21ac21395a2d7938"},{url:"images/course/install-apidoc-1.png",revision:"5c34c0640652e3670988580215a38e3b"},{url:"images/course/install-apidoc-2.png",revision:"c9a7f968f6be9705e2b994ce57690dd9"},{url:"images/course/install-apidoc-3.png",revision:"557909af93f3a19610f8c0772205678c"},{url:"images/course/install-apidoc-4.png",revision:"0a9dc2601a45853e57ac4e66dc3b84c0"},{url:"images/course/install-apidoc-5.png",revision:"a084d350ab1a4066953fd03cb1e51c4e"},{url:"images/course/install-apidoc-6.png",revision:"ffb6cededbeeab5b588c764ff614b6bb"},{url:"images/course/php-study-install/install-1.png",revision:"356f7a4f3790ce393b60529e2717223f"},{url:"images/course/php-study-install/install-2.png",revision:"83379b0328db209771c29b9ecdf87003"},{url:"images/course/php-study-install/install-3.png",revision:"e98a5c9b2119e6e07eb42b1cc22c38e6"},{url:"images/course/php-study-install/install-4.png",revision:"be98f0da3c4edec2ba876c9de1c436eb"},{url:"images/course/php-study-install/install-5.png",revision:"b9fef03a021f58fc274f671b4c9cb006"},{url:"images/course/php-study-install/install-6.png",revision:"529dfe051c095770d62c13810da75d04"},{url:"images/course/php-study-install/install-7.png",revision:"c819e8538b5cff578447d1f7201065b3"},{url:"images/course/php-study-install/install-8.png",revision:"63771f6319b0d3c8f99c361e0c3614f5"},{url:"images/course/php-study-install/titlepic.png",revision:"22db0a43aa94063f622a3135d5215048"},{url:"images/course/titlepic.png",revision:"4f036983e6aea693343b039f2498aad6"},{url:"images/logo.png",revision:"b000157a61a423c6b357baf49fd19031"},{url:"logo.png",revision:"b000157a61a423c6b357baf49fd19031"}],{}),e.cleanupOutdatedCaches()}));
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
