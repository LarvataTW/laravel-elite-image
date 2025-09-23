

# 說明
此特別版是針對php-7.4(not alpine) + python環境，編譯成base image 後讓共用php 與 python專案可以使用

# image 網址
> https://hub.docker.com/repository/docker/larvata/laravel-elite-python/general

# 本地編譯 
## 1.登入 docker hub
> docker login
## 2.設定多環境編譯
> docker buildx create --use --name larvata-multi --driver docker-container
## 3.執行build與push到dockerhub
> docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -t larvata/laravel-elite-python:7.4-fpm \
  --push .