const { config } = require("vuepress-theme-hope");

module.exports = config({
  title: "ThinkPHP-ApiDoc",
  description: "ThinkPHP-ApiDoc是一个基于ThinkPHP开发的，根据注释自动生成API文档、在线调试、Markdown文档、快速生成Crud、一键生成模块代码的扩展插件",
  base: "/thinkphp-apidoc/",
  dest: "./dist",

  // remove this if you are not using Vue and React in "markdownEnhance: code demo"
  head: [
    [
      "script",
      { src: "https://cdn.jsdelivr.net/npm/react/umd/react.production.min.js" },
    ],
    [
      "script",
      {
        src:
          "https://cdn.jsdelivr.net/npm/react-dom/umd/react-dom.production.min.js",
      },
    ],
    ["script", { src: "https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js" }],
    [
      "script",
      { src: "https://cdn.jsdelivr.net/npm/@babel/standalone/babel.min.js" },
    ],
  ],



  themeConfig: {
    logo: "/logo.png",
    hostname: "https://apidoc.demo.hg-code.com/apidoc/",
    author: "HG",
    repo: "https://github.com/HGthecode/thinkphp-apidoc",
    docsRepo: "https://github.com/HGthecode/thinkphp-apidoc",
    docsBranch:"docs",
    docsDir: "packages/v2/src",
    baseLang:"zh-CN",
    pageInfo:["Author","Category","Tag","ReadTime","Time"],
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
        text: "演示",
        link: "https://apidoc.demo.hg-code.com/apidoc/",
        icon: "demo",
      },
      {
        text: "版本",
        icon: "version",
        items:[
          {
            text:"V2",
            link:"/"
          },
          {
            text:"V1",
            link:"https://hgthecode.github.io/thinkphp-apidoc/v1/"
          }
        ]
      },
    ],
    sidebar: {
      "/guide/": [
        "",
        "install",
        "changelog",
      ],
      "/use/": [
        "",
        "actions",
        "function",
        "help",
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
