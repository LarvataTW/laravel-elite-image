# laravel-elite-image
> 精簡且可用於開發與生產環境的 PHP/Laravel Docker Image
###### tags: `Docker` `Laravel`

### Laravel Elite Image 是什麼？

這是一個針對開發與生產特別設計的 PHP/Laravel Docker 環境，使用 [Envoy 任務執行器](https://github.com/laravel/envoy) 設計啟動腳本，
可以很輕鬆的根據開發或生產需求來客製化啟動腳本，另外使用 [Supervisor 進程管理工具](http://supervisord.org/index.html) 管理進程，
提供 Nginx、PHP-FPM、PHP Server、PHP swoole、Laravel 排程、Laravel 佇列處理等常見的進程管理，可搭配
[laravel-swoole](https://github.com/swooletw/laravel-swoole) 使用高效能的 Swoole 網絡通信引擎。

### 快速啟動

這個 Image 適用於所有 php 環境，以 Laravel Framework 為例，通過以下三個步驟，你將建立好一個 Laravel 網站

1. 安裝 Laravel

通過以下指令，你可以安裝最新版的 Laravel

```bash
docker run --rm -v $(pwd):/opt -w /opt larvata/laravel-elite-image:latest-php7.4 \
bash -c "composer create-project --prefer-dist laravel/laravel example-app"
```

你可以通過修改指令中的 `example-app` 來改變專案資料夾名稱，例如：

```bash
docker run --rm -v $(pwd):/opt -w /opt larvata/laravel-elite-image:latest-php7.4 \
bash -c "composer create-project --prefer-dist laravel/laravel blog"
```

你還可以使用 = 或 : 作為分隔符指定版本，例如：

```bash
docker run --rm -v $(pwd):/opt -w /opt larvata/laravel-elite-image:latest-php7.4 \
bash -c "composer create-project --prefer-dist laravel/laravel=7.30.1 example-app"
```

2. 安裝環境啟動設定檔

```bash
cd example-app
wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/setup/Dockerfile
wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/setup/Envoy.blade.php
wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/setup/docker-compose.yml.example
cp docker-compose.yml.example docker-compose.yml
```

3. 啟動環境(請先設定好.env)

```bash
docker-compose up -d
```

按照上述步驟，你應該已經建立好一個 Laravel 網站， 現在你可以打開你的瀏覽器，輸入 `http://localhost/` ， 確認你的網站是否已經成功建立完成

### 標籤對應的Dockerfile

* [latest-php8.0](https://github.com/LarvataTW/laravel-elite-image/blob/master/8.0/Dockerfile) (未來版本)
* [latest-php7.4](https://github.com/LarvataTW/laravel-elite-image/blob/master/7.4/Dockerfile) (主要維護版本)
* [latest-php7.3](https://github.com/LarvataTW/laravel-elite-image/blob/master/7.3/Dockerfile) (僅提供安全問題修復)
* [latest-php7.2](https://github.com/LarvataTW/laravel-elite-image/blob/master/7.2/Dockerfile) (已停止支援)
* [latest-php5.6](https://github.com/LarvataTW/laravel-elite-image/blob/master/5.6/Dockerfile) (已停止支援)

### 環境設定檔

#### 設定 Nginx

1. 下載Nginx設定檔
```bash
# Nginx + PHP-FPM (default)
wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/config/nginx.conf
# Nginx + Swoole
wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/config/nginx-swoole.conf
```

2. 設定dockerfile

```dockerfile
# Nginx + PHP-FPM (default)
COPY nginx.conf /etc/nginx/nginx.conf
# Nginx + Swoole
COPY nginx-swoole.conf /etc/nginx/nginx-swoole.conf
```

#### 設定 PHP

1. 下載PHP設定檔 `wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/config/php.ini`

2. 下載PHP-FPM設定檔 `wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/config/www.conf`

3. 設定dockerfile

```dockerfile
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
```

#### 設定 Supervisord

1. 下載Supervisord設定檔 `wget https://github.com/LarvataTW/laravel-elite-image/raw/{version}/7.4/config/supervisord.conf`

2. 設定dockerfile

```dockerfile
COPY supervisord.conf /etc/supervisord.conf
```

按照以下流程可以自行建立新的 Supervisord 腳本

1. 建立 Supervisord 腳本(Ex:example.conf)

2. 設定dockerfile

```dockerfile
COPY example.conf /etc/supervisord.d/example.conf
```
### 啟動腳本

通過修改 `Envoy.blade.php` 你可以設定程式啟動的流程

- 建議在開發環境使用Nginx (Port 80) + PHP-FPM (Port 9000)
- 建議在正式環境使用Nginx (Port 80) + Swoole (Port 7780)

#### 啟動 Nginx (Port 80) + PHP-FPM (Port 9000)

你可以通過[Nginx設定檔](#設定-Nginx)，修改啟動Port

```bash
supervisord -c /etc/supervisord.conf
supervisorctl start laravel-php-fpm:*
supervisorctl start laravel-nginx:*
```

#### 啟動 Nginx (Port 80) + Swoole (Port 7780)

使用你可以通過[Nginx設定檔](#設定-Nginx)，修改啟動Port

```bash
supervisord -c /etc/supervisord.conf
supervisorctl start laravel-php-fpm:*
supervisorctl start laravel-swoole:*
supervisorctl start laravel-nginx-swoole:*
```

#### 啟動排程

```bash
supervisord -c /etc/supervisord.conf
supervisorctl start laravel-schedule:*
```

#### 啟動佇列處理

```bash
supervisord -c /etc/supervisord.conf
supervisorctl start laravel-worker:*
```

### Supervisor 腳本

|腳本|說明|
|---|---|
|laravel-nginx|Nginx 服務(PHP-FPM)|
|laravel-nginx-swoole|Nginx 服務(Swoole)|
|laravel-php-fpm|PHP-FPM 服務|
|laravel-server|PHP Server 服務|
|laravel-swoole|PHP swoole 服務|
|laravel-schedule|Laravel 排程|
|laravel-worker|Laravel 佇列處理|
|laravel-webpack|Webpack 熱更新(開發用)|

### 已安裝工具

|套件|說明|
|---|---|
|supervisor|進程管理工具|
|curl|Http命令列工具|
|vim|文件編輯工具|
|tar|檔案壓縮工具|
|nodejs|JavaScript 執行環境|
|yarn|JavaScript 管理工具|

### 已安裝 PHP 擴展

|擴展|用途|
|---|---|
|swoole|Swoole服務|
|redis|Redis連線|
|mcrypt|加密|
|iconv|中文字處理|
|mbstring|中文字處理|
|pdo|資料庫連線|
|pdo_mysql|Mysql連線|
|mysqli|Mysql連線|
|tokenizer|laravel tinker|
|xml|RSS、Excel...等功能|
|zip|檔案壓縮(gzip)|
|exif|讀取副檔名|
|opcache|php優化加速組件|

### 相關支援

* 維護者: [果子云數位科技](https://github.com/LarvataTW)

### 常見問題

Ｑ：環境變數未更改
Ａ：請重新起動

Ｑ：無法連上這個網站
Ａ：請確定已設定好**env**與 `composer install` 並 重新起動

Ｑ：No application encryption key has been specified.
Ａ：請設定APP Key `php artisan key:generate`