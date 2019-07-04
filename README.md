# Swoft 定时任务组件

swoft定时任务组件，核心大部分基于1.0，在此基础上适配了2.0，实测可用。

## 安装

- composer command

```bash
composer require banyancheung/cron-task
```

- composer.json

```bash
"require": {
    ...
    "banyancheung/cron-task": "dev-master",
    ...
}
```

然后 `composer update` 即可。

## 使用

1.在`app/beans.php` 文件里的`server`一项，监听 `onPipeMessage` 事件。

![](https://img.yunjes.com/doc/cron-task/1.png)

2.在定义好的task注解里，增加 Scheduled 一项。

![](https://img.yunjes.com/doc/cron-task/2.png)

注意：Scheduled里的task属性是必须的，用于定位是哪个Task，也许会有更好的方法。:)

3.应用配置里增加 cron 配置项

![](https://img.yunjes.com/doc/cron-task/3.png)

## LICENSE

The Component is open-sourced software licensed under the [Apache license](LICENSE).
