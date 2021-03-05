---
sidebar: auto
---

# 使用Github Actions 实现TP6自动化部署

通常我们部署网站，都是手动 FTP 推包，手动连上服务器操作。这种方式一是操作烦琐，二是不推崇总是在生产环境人工操作，因为人工操作越多，越容易出错。实现自动化部署后，我们只需要将代码提交到仓库等几分钟，就自动部署好了。

实现代码提交的自动化工作流，要依靠持续集成（或者加上持续交付）服务。现在主流的公用免费的持续集成服务有：

- GitHub Actions
- Travis CI
- Jenkins
- Circle CI
- Azure Pipeline
...

其中 GitHub Actions 是 GitHub 自家的持续集成及自动化工作流服务，简单易用，开源/私有项目都可免费试用（私有项目有限额）。
它使用起来非常简单，只要在你的仓库根目录建立.github/workflows文件夹，将你的工作流配置(YAML 文件)放到这个目录下，就能启用 GitHub Actions 服务。


## 实现逻辑

- 使用GitHub Action监听到代码仓库的提交`push`操作,触发工作流
- 拉取最新代码推送到服务器
- 安装依赖`composer update`


## 服务器端配置

为了能让github将仓库代码部署到服务器，需要使用git的密钥对解决登录校验问题。

### 安装git

链接服务器终端，执行安装命令
```sh
yum install git 

# 安装完成后，查看git版本
git --version

```



### 生成SSH密钥对

#### 1、创建`.ssh`文件夹并进入该文件夹
```sh
mkdir -p ~/.ssh && cd ~/.ssh
```

#### 2、生成ssh密钥对
```sh
ssh-keygen -t rsa -f mysite
```
这里一路回车就行，执行完成后，会在~/.ssh下生成两个文件：mysite（私钥）和mysite.pub（公钥）。其中私钥是你的个人登录凭证，不可以分享给他人，如果别人得到了你的私钥，就能登录到你的服务器。公钥则需要放配置在服务器上。

#### 3、配置公钥

如果上一步你直接是在服务器中执行，则只要：
```sh
cat mysite.pub >> authorized_keys
```
否则将公钥mysite.pub的内容贴到服务器的~/.ssh/authorized_keys中，若文件或目录不存在，可以自己创建。

#### 4、配置权限
确保服务器~/.ssh文件夹的权限低于 711，这里直接用 600（仅本用户可读写）：
```sh
chmod 600 -R ~/.ssh
```
#### 5、查看私钥
进入到`~/.ssh`目录，查看私钥文件`mysite`,将内容复制下来以备后续使用

```sh
# 查看mysite内容
cat mysite
```
私钥的内容大致如下：
```sh
-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----
```

### 安装composer
通常`vendor`目录是不提交到git仓库的，所以需要在自动部署文件后执行 `composer update`来更新依赖

1、下载composer
```sh
curl -sS https://getcomposer.org/installer | php
```
2、将composer.phar文件移动到bin目录以便全局使用composer命令
```sh
mv composer.phar /usr/local/bin/composer
```
3、切换国内镜像源
```sh
composer config -g repo.packagist composer https://packagist.phpcomposer.com
```
4、验证安装成功
```sh
composer
```

![composer安装](/thinkphp-apidoc/images/course/githubActions/composer.png "composer-install")

::: warning 提示
检查php是否禁用`putenv`,`proc_open`函数，如果禁用了请删除函数的禁用，因为执行`composer update`更新依赖时需要这些函数的支持。
:::



## 配置GitHub仓库

打开你的网站代码仓库，点击 Settings 标签，找到 Secrets 设定

![github-settings](/thinkphp-apidoc/images/course/githubActions/github-settings.png "github-settings")

添加如下Secret
|Name|Value|说明|
|-|-|-|
|SERVER_KEY|-----BEGIN RSA PRIVATE KEY-----...|ssh的私钥，就是[服务器建立ssh的私钥内容](#_5、查看私钥)|
|SERVER_HOST|如：47.115.160.199| 服务器连接地址 |
|SERVER_PORT|如：22（默认）|服务器连接端口|
|SERVER_USERNAME|如：root|服务器登录用户名|


## 编写工作流

在仓库根目录中创建.github/workflows文件夹，再创建一个 YAML 文件，文件名自定，我这里起名叫deploy.yml，所以文件的完整路径为.github/workflows/deploy.yml，我将配置的意义写在注释中，文件内容如下：

```yml
name: Deploy Demo #工作流名称，可自定义
# 触发条件
on:
  push:
    branches:
      - master  # 只在master上push触发部署
    paths-ignore: # 下列文件的变更不触发部署
      - README.md
      - LICENSE
jobs:
  deploy:
    runs-on: ubuntu-latest # 使用ubuntu系统镜像运行自动化脚本
    steps: # 自动化步骤
      - uses: actions/checkout@v2 # 第一步，下载代码仓库
      - name: Deploy to Server    # 第二步，推文件到服务器
        uses: AEnterprise/rsync-deploy@v1.0
        env:
          DEPLOY_KEY: ${{ secrets.SERVER_KEY }}    # 引用Github配置，SSH私钥
          ARGS: -avz --delete --exclude='*.env' --exclude='runtime/' # rsync参数，删除服务器目录的文件，排除.env，runtime文件
          SERVER_PORT: ${{ secrets.SERVER_PORT }}  # 引用Github配置，SSH端口,默认22
          FOLDER: ./ # 要推送的文件夹，路径相对于代码仓库的根目录
          SERVER_IP: ${{ secrets.SERVER_HOST }}    # 引用Github配置，服务器的host名（IP或者域名domain.com
          USERNAME: ${{ secrets.SERVER_USERNAME }} # 引用Github配置，服务器登录名
          SERVER_DESTINATION: /www/wwwroot/apidoc.demo/ # 部署到服务器目标文件夹
      - name: Execute Script # 第三步，执行安装依赖
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }} # 下面三个配置与上面一样
          username: ${{ secrets.SERVER_USERNAME }}
          key: ${{ secrets.SERVER_KEY }}
          # 进入到项目目录，执行依赖更新
          script: |
            cd /www/wwwroot/apidoc.demo/
            composer update
```

::: warning 重要提示
- 以上`yml`文件使用时去除注释
- 本部署方式为删除服务器原目录的文件，并将github仓库内的文件部署进去，如服务器项目目录中有不需要删除的文件，必须在`ARGS`的`--exclude=''`排除
:::

## 测试自动部署
把工作流文件写好，提交到仓库。就可以发现 GitHub Actions 已经启动了！可以在提交历史后面的状态，或者 Actions 标签中看到运行的状态。

![github-actions](/thinkphp-apidoc/images/course/githubActions/github-actions.png "github-actions")

