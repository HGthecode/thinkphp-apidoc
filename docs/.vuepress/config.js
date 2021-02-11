module.exports = {
    title: 'ThinkPHP-ApiDoc',
    description: 'ThinkPHP-ApiDoc是一个基于ThinkPHP开发的，根据注释自动生成API文档的插件',
    base:"/thinkphp-apidoc/",
    plugins: {
        '@vuepress/medium-zoom': {
          selector: 'img.img-view',
          options: {
            margin: 16
          }
        }
      },
      locales: {
        // 键名是该语言所属的子路径
        // 作为特例，默认语言可以使用 '/' 作为其路径。
        '/': {
          lang: 'V2', // 将会被设置为 <html> 的 lang 属性
          title: 'ThinkPHP-ApiDoc',
          description: '基于ThinkPHP根据注释自动生成API文档的插件'
        },
        '/v1/': {
          lang: 'V1',
          title: 'ThinkPHP-ApiDoc',
          description: '基于ThinkPHP根据注释自动生成API文档的插件'
        }
      },
    themeConfig: {
        logo: '/images/logo.png',
        displayAllHeaders:true,
        locales: {
          '/': {
            selectText: '版本',
            label: 'V2',
            nav: [
              { text: '指南', link: '/' },
              { 
                text: '安装', 
                items: [
                  { text: '安装', link: '/v2/install/' },
                  { text: '快速上手', link: '/v2/course/' },
                  { text: '更新日志', link: '/v2/changelog/' }
                ]
              },
              { text: '功能', link: '/v2/function/' },
              { text: '配置', link: '/v2/config/' },
              { text: '使用', link: '/v2/use/' },
              { text: '支持', link: '/v2/sponsor/' },
              { text: '演示', link: 'https://apidoc.demo.hg-code.com/apidoc/', target:'_blank'},
            ],
            sidebar: {
              '/v2/function/': [
                '',  
              ],
              '/v2/config/': [
                  '',  
                  // 'function', 
              ],
              '/v2/use/': [
                  '', 
                  'actions',
                  'help',
              ],
            }
          },
          '/v1/': {
            selectText: '版本',
            label: 'V1',
            nav: [
              { text: '指南', link: '/v1/' },
              { 
                text: '安装', 
                items: [
                  { text: '安装', link: '/v1/install/' },
                  { text: '更新日志', link: '/v1/changelog/' }
                ]
              },
              { text: '配置', link: '/v1/config/' },
              { text: '使用', link: '/v1/use/' },
              { text: '支持', link: '/v1/sponsor/' },
              { text: '演示', link: 'https://apidoc.demo.hg-code.com/apidoc/', target:'_blank'},
            ],
            sidebar: {
              '/v1/config/': [
                  '',  
                  'function', 
              ],
              '/v1/use/': [
                  '', 
                  'actions',
                  'help',
              ],
            }
            
          }
        },
        
      }
  }