# coachtech_ams

## 勤怠管理アプリ

## 環境構築

### Dockerビルド

1. git clone git@github.com:hi-san10/ams.git

2. docker-compose up -d --build

*MYSQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してください。

### Laravel環境構築

1. docker-compose exec php bash

2. composer install

3. env.example ファイルから .env を作成し、docker-compose.ymlに応じて環境変数を変更

    ・開発環境ではMailtrapサービスを使ってメール機能を開発しています

    ・Mailtrap url:[https://mailtrap.io](https://mailtrap.io)

    ・アカウント作成後、ログインする

    ・左メニューにある Email Testing リンク、もしくは画面中央あたりの Email Testing の「Start Testing」ボタンをクリック

    ・SMTP Settings タブをクリック

    ・Integrations セレクトボックスで、Laravel 7.x,8.x を選択

    ・copy ボタンをクリックして、クリップボードに .env の情報を保存

    ・.envにコピーした情報を貼り付ける
        ![75F1C55F-FC14-46BE-898D-9C25817259E9](https://github.com/user-attachments/assets/571e1894-4346-4b98-883d-af7e577a743e)

4. php artisan key:generate

5. php artisan migrate

6. php artisan db:seed

・ 一般ユーザー(スタッフ)のダミーデータ10件分

・ ログイン用ユーザーのダミーデータ1件分↓
![Image](https://github.com/user-attachments/assets/a84c91c2-2a64-4604-b656-69e99cfe4551)
・ 勤怠情報(出勤、退勤)のダミーデータ50件分

・ 勤怠情報(休憩)のダミーデータ50件分

・ 管理者のダミーデータ1件分
![Image](https://github.com/user-attachments/assets/d7ed3551-3713-45a7-8c3f-25faa9eda3c6)

## 使用技術

・PHP 8.3

・Laravel 8.83

・MYSQL 8.0

## ER図

![Image](https://github.com/user-attachments/assets/a64d9dba-e1d8-4cc6-b618-aa4c5e3c5e5e)

## テーブル仕様書

![Image](https://github.com/user-attachments/assets/849b7365-c72a-43b7-a5e9-3dbedc692991)

![Image](https://github.com/user-attachments/assets/700867ca-dbd5-444d-b186-6b6d93e4649d)

![Image](https://github.com/user-attachments/assets/37162213-ddd8-42f4-b1ab-79050b4ec7cb)

## URL

・アプリケーション(開発環境):[http//localhost/](http//localhost/)

・phpMyAdmin:[http//localhost:8080](http/localhost:8080)


## 採点担当の方へ

・テストケース"メール認証機能"に関しては、メール認証を完了した後の画面遷移が期待挙動と相違があります(認証後ログイン画面へ遷移)。

　こちら模擬案件製作中に担当部門へ確認し了承を得ており、READMEに記載するよう指示がありましたのでご確認いただきたく思います。
