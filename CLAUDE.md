# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 專案性質

發佈到 Docker Hub `larvata/laravel-elite-image` 的 PHP/Laravel base image。沒有應用程式碼、沒有測試；每個頂層目錄（`5.6`、`7.2` … `8.4`、`8.5`、`7.4-fpm`）是一個獨立的 Docker build context，彼此不共用檔案。

維護狀態見根目錄 `README.md`：8.5、8.4、8.3 為主要維護；7.4 僅安全更新；其餘已停止支援。改動一般只動 8.4 / 8.5。

## 單一版本目錄結構

- `Dockerfile` — image 本體（8.4 起為 multi-stage：`build` stage 編 extension + 裝 Composer/Envoy，final stage 只 copy 產物）
- `config/` — 被 COPY 進 image 的 nginx / php.ini / php-fpm `www.conf` / `supervisord.conf`
- `supervisord.d/*.conf` — 各 process 定義，全部 `autostart=false`，由 Envoy 腳本決定啟動哪些
- `optional/` — octane 啟動腳本（COPY 到 `/opt/octane`）、opcache preload 範例
- `setup/` — **不進 image**，是給下游專案 wget 下來用的範本（`Dockerfile`、`Envoy.blade.php`、`Makefile`、`docker-compose.yml.example`）

## 執行時架構

Image `CMD` 是 `envoy run production`。下游專案的 `Envoy.blade.php`（範本在 `setup/`）定義 `production` / `local` task：先 `supervisord -c /etc/supervisord.conf`，再 `supervisorctl start <program>:*` 逐一啟動 php-fpm、octane（或 octane-dev）、nginx、schedule、worker，最後 `tail -f /dev/null` 保持前景。新增 process = 在 `supervisord.d/` 加 conf，並在 Envoy task 中 start 它。

## mysql / pgsql 雙變體（8.4 起）

- Dockerfile 用 `ARG DB_TYPE=mysql|pgsql` 切換：build stage 決定編 `pdo_mysql mysqli` 或 `pdo_pgsql pgsql`；final stage pgsql 時補 `libpq`。
- `config/php.ini` 預設寫死 mysql 的 `extension=` 行；pgsql 變體在 Dockerfile 用 `sed` 註解/反註解。**sed 依賴 php.ini 中這幾行的精確文字**（`^extension=pdo_mysql$`、`^;extension=pdo_pgsql$` 等），改 php.ini 時要保持一致，否則 pgsql 版會啟動時載入不存在的 mysql extension。
- 8.5 起 opcache 已靜態編入 PHP，Dockerfile 不再 `docker-php-ext-install opcache`（8.4 仍有）。

## 本地 build / 驗證

```bash
# 在版本目錄下 build（DB_TYPE 預設 mysql）
docker build -t elite-test:8.4 8.4/
docker build --build-arg DB_TYPE=pgsql -t elite-test:8.4-pgsql 8.4/

# 快速檢查 extension 是否載入正常（無 warning）
docker run --rm elite-test:8.4-pgsql php -m
```

多平台推送手動流程見 `7.4-fpm/local_build.md`（`docker buildx build --platform linux/amd64,linux/arm64 --push`）。

## 發版（CI）

`.github/workflows/actions.yml` 只在 push tag `v*.*.*` 時觸發：

- Tag 格式 `v<semver>-php<目錄名>`，例 `v3.9.0-php8.5`、`v3.8.1-php7.4-fpm`。workflow 從 tag 中比對 `php<context>` 決定 build 哪個目錄。
- 新增版本目錄時，**必須**把目錄名加進 workflow 的 `VERSION_LIST`。
- PHP ≥ 8.4 自動 build mysql + pgsql 兩個 image（pgsql tag 加 `-pgsql` 後綴）；產出 tag：`<semver>-php<ver>[-pgsql]` 與 `latest-php<ver>[-pgsql]`。
- 平台 `linux/amd64,linux/arm64/v8`，只推 Docker Hub（ghcr 已註解）。

## 新增 PHP 版本的慣例

參考 commit `a9c64d8`（新增 8.5）：複製前一版目錄 → 改 Dockerfile 兩處 `FROM` 的 base image tag → 處理該版 extension 差異 → 更新該目錄 README（changelog、URL 路徑中的版本號）→ 加進 workflow `VERSION_LIST` → 根 `README.md` 標籤列表。升級 base image patch 版時兩個 `FROM` 都要改。

## 已知不一致

- `8.4/setup/Dockerfile`、`8.5/setup/Dockerfile` 仍 `FROM larvata/laravel-elite-image:latest-php8.3`。
- 8.4 README 仍列 vim、範例用 `bash -c`，但 8.4 image 已移除 bash/vim（只有 `sh`）；8.5 README 已修正。
