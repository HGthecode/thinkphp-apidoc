module.exports = {
    title: 'ThinkPHP-ApiDoc',
    description: 'ThinkPHP-ApiDoc是一个基于ThinkPHP6开发的，根据注释自动生成API文档的插件',
    base:"/thinkphp-apidoc/",
    head: [
        ['script', { src: '/js/index.js' }],
        ['link', { rel: 'stylesheet', type: 'text/css', href: '/css/index.css' }]
      ],
    themeConfig: {
        logo: '/images/logo.png',
        displayAllHeaders:true,
        nav: [
            { text: '指南', link: '/' },
            { text: '安装', link: '/install/' },
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
            ],
        }
      }
  }