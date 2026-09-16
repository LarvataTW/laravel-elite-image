# Opcache Preload 功能
> 啟動時將腳本預加載到 opcache 中
###### tags: `Opcache` `Preload`

### 爲 Wordpress 設定 Preload

1. 請確保你的PHP版本為7.4或者更高版本

2. 將檔案 `wp_opcache_preload.php` 複製到專案資料夾下

3. 建立PHP設定檔 `opcache_preload.ini`

```ini
[opcache]
; 指定 Preload 設定檔,
opcache.preload=/var/www/wp_opcache_preload.php
; 指定 Preload 執行 User
opcache.preload_user=www-data
```

4. 設定 Dockerfile

```dockerfile
COPY wp_opcache_preload.php /var/www/wp_opcache_preload.php
COPY opcache_preload.ini /usr/local/etc/php/conf.d/opcache_preload.ini
```

P.S. 在生產環境，啟動時最好關閉PHP檔案修改檢查，提升效能

```bash
# 關閉PHP檔案修改檢查
sed -i 's/opcache.validate_timestamps=1/opcache.validate_timestamps=0/g' /usr/local/etc/php/php.ini
```