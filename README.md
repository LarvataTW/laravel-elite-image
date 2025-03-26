# laravel-elite-image
> 精簡且可用於開發與生產環境的 PHP/Laravel Docker Image
###### tags: `Docker` `Laravel`

### Laravel Elite Image 是什麼？

這是一個針對開發與生產特別設計的 PHP/Laravel Docker 環境，使用 [Envoy 任務執行器](https://github.com/laravel/envoy) 設計啟動腳本，
可以很輕鬆的根據開發或生產需求來客製化啟動腳本，另外使用 [Supervisor 進程管理工具](http://supervisord.org/index.html) 管理進程，
提供 Nginx、PHP-FPM、PHP Server、PHP swoole、Laravel 排程、Laravel 佇列處理等常見的進程管理，可使用高效能的 Swoole 網絡通信引擎。

### 標籤對應的Dockerfile
> 以下提供版本詳細使用說明
* [latest-php8.4](https://github.com/LarvataTW/laravel-elite-image/blob/master/8.4/Dockerfile) (下一代維護版本)
* [latest-php8.3](https://github.com/LarvataTW/laravel-elite-image/blob/master/8.0/Dockerfile) (主要維護版本)
* [latest-php8.0](https://github.com/LarvataTW/laravel-elite-image/blob/master/8.0/Dockerfile) (已停止支援)
* [latest-php7.4](https://github.com/LarvataTW/laravel-elite-image/blob/master/7.4/Dockerfile) (僅提供PHP版本更新與安全問題修復)
* [latest-php7.3](https://github.com/LarvataTW/laravel-elite-image/blob/master/7.3/Dockerfile) (已停止支援)
* [latest-php7.2](https://github.com/LarvataTW/laravel-elite-image/blob/master/7.2/Dockerfile) (已停止支援)
* [latest-php5.6](https://github.com/LarvataTW/laravel-elite-image/blob/master/5.6/Dockerfile) (已停止支援)

### 相關支援

* 維護者: [果子云數位科技](https://github.com/LarvataTW)

### 常見問題

Ｑ：環境變數未更改
Ａ：請重新起動

Ｑ：無法連上這個網站
Ａ：請確定已設定好**env**與 `composer install` 並 重新起動

Ｑ：No application encryption key has been specified.
Ａ：請設定APP Key `php artisan key:generate`