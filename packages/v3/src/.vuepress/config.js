const { config } = require("vuepress-theme-hope");

module.exports = config({
  title: "ThinkPHP-ApiDoc",
  description: "ThinkPHP-ApiDoc是一个基于ThinkPHP开发的，根据注释自动生成API文档、在线调试、Markdown文档、快速生成Crud、一键生成模块代码的扩展插件",
  base: "/thinkphp-apidoc/",
  dest: "./dist",

  locales: {
    "/": {
      // 设置需要的语言
      lang: "zh-CN",
    },
  },


  themeConfig: {
    logo: "/logo.png",
    hostname: "https://apidoc.demo.hg-code.com/apidoc/",
    author: "HG",
    repo: "https://github.com/HGthecode/thinkphp-apidoc",
    docsRepo: "https://github.com/HGthecode/thinkphp-apidoc",
    docsBranch:"docs",
    docsDir: "packages/v3/src",
    baseLang:"zh-CN",
    pageInfo:['author', 'visitor', 'time', 'category', 'tag', 'reading-time'],
    displayAllHeaders:true,
    

    nav: [
      { text: "指南", link: "/guide/", icon: "home" },
      {
        text: "配置",
        icon: "config",
        link: "/config/",
      },
      {
        text: "使用",
        icon: "note",
        link: "/use/",
      },
      {
        text: "教程",
        icon: "book",
        link: "/course/",
      },
      
      {
        text: "版本",
        icon: "version",
        items:[
          {
            text:"V3",
            link:"/"
          },
          {
            text:"V2",
            link:"https://hgthecode.github.io/thinkphp-apidoc/v2/"
          }
        ]
      },
      // {
      //   text: "演示",
      //   link: "https://apidoc.demo.hg-code.com/apidoc/",
      //   icon: "demo",
      // },
      {
        text: "更多",
        icon: "link",
        items:[
          // {
          //   text: "教程",
          //   icon: "book",
          //   link: "/course/",
          // },
          {
            text: "TP6演示",
            link: "https://apidoc.demo.hg-code.com/apidoc/",
          },
          {
            text: "TP5演示",
            link: "http://apidoc.tp5.hg-code.com/apidoc/",
          },
          
        ]
      },

      
    ],
    sidebar: {
      "/guide/": [
        "",
        "install",
        "changelog",
      ],
      "/config/": [
        "",
        "page",
      ],
      "/use/": [
        {
          title: "编写注释",
          prefix: "notes/",
          collapsable:false,
          icon:"edit",
          children: [
            "",
            "useFile",
            "controller",
            "api",
          ],
        },
        {
          title: "页面操作",
          prefix: "operation/",
          collapsable:false,
          icon:"page",
          children: [
            "layout",
            "apidebug"
          ],
        },
        {
          title: "功能使用",
          prefix: "function/",
          collapsable:false,
          icon:"extend",
          children: [
            "apps",
            "controllerGroup",
            "password",
            "cache",
            "docs",
            "lang",
            "debugEvent",
            "mock"
          ],
        },
        {
          title: "常见问题",
          prefix: "help/",
          collapsable:false,
          icon:"help",
          children: [
            "notConfig",
            "404",
            "500",
            "v2Tov3"
          ],
        },
      ],
    },

   
    blog:false,
    footer: {
      display: true,
      content: `<div>感谢每一位支持的朋友 | 点个Star呗 <a href="https://github.com/HGthecode/thinkphp-apidoc" target="_blank"><i class="iconfont icon-github" style="margin-right:5px;"></i>GitHub</a></div>`,
    },

    copyright: true,
    lastUpdate: {
      timezone: "Asia/Shanghai",
    },
    mdEnhance: {
      // please only enable the features you need
      enableAll: false,
    },
    feed:false,
    pwa: {
      favicon: "/favicon.ico",
      cachePic: true,
      apple: {
        icon: "/thinkphp-apidoc/assets/icon/apple-icon-152.png",
        statusBarColor: "black",
      },
      msTile: {
        image: "/thinkphp-apidoc/assets/icon/ms-icon-144.png",
        color: "#ffffff",
      },
      manifest: {
        icons: [
          {
            src: "/thinkphp-apidoc/assets/icon/chrome-mask-512.png",
            sizes: "512x512",
            purpose: "maskable",
            type: "image/png",
          },
          {
            src: "/thinkphp-apidoc/assets/icon/chrome-mask-192.png",
            sizes: "192x192",
            purpose: "maskable",
            type: "image/png",
          },
          {
            src: "/thinkphp-apidoc/assets/icon/chrome-512.png",
            sizes: "512x512",
            type: "image/png",
          },
          {
            src: "/thinkphp-apidoc/assets/icon/chrome-192.png",
            sizes: "192x192",
            type: "image/png",
          },
        ],
        shortcuts: [
          {
            name: "指南",
            short_name: "指南",
            url: "/guide/",
            icons: [
              {
                src: "/thinkphp-apidoc/assets/icon/guide-maskable.png",
                sizes: "192x192",
                purpose: "maskable",
                type: "image/png",
              },
              {
                src: "/thinkphp-apidoc/assets/icon/guide-monochrome.png",
                sizes: "192x192",
                purpose: "monochrome",
                type: "image/png",
              },
            ],
          },
        ],
      },
    },
  },
});
