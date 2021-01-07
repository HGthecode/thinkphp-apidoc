module.exports = {
    title: 'ThinkPHP-ApiDoc',
    description: 'ThinkPHP-ApiDoc是一个基于ThinkPHP6开发的，根据注释自动生成API文档的插件',
    base:"/thinkphp-apidoc/",
    plugins: {
        '@vuepress/medium-zoom': {
          selector: 'img.img-view',
          options: {
            margin: 16
          }
        }
      },
    themeConfig: {
        logo: '/images/logo.png',
        displayAllHeaders:true,
        nav: [
            { text: '指南', link: '/' },
            { 
              text: '安装', 
              items: [
                { text: '安装', link: '/install/' },
                { text: '更新日志', link: '/changelog/' }
              ]
            },
            { text: '配置', link: '/config/' },
            { text: '使用', link: '/use/' },
            { text: '支持', link: '/sponsor/' },
            { text: '演示', link: 'https://apidoc.demo.hg-code.com/apidoc/', target:'_blank'},
        ],
        sidebar: {
            '/config/': [
                '',  
                'function', 
            ],
            '/use/': [
                '', 
                'actions',
                'help',
            ],
        }
      }
  }