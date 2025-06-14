# coachtech_ams

## 勤怠管理アプリ

## 機能一覧
・ 会員登録

・ 会員登録時にメールによる本人認証

・ 認証メール再送

・ ログイン

・ ログアウト

・ 勤怠打刻

・ 月毎の勤怠一覧

・ 勤怠詳細

・ 勤怠情報修正後、申請

・ 修正した勤怠一覧(未承認、承認済み)

・ 管理者ログイン

・ スタッフ一覧

・ スタッフの月毎の勤怠一覧

・ 当日の勤怠一覧

・ 修正申請一覧(未承認、承認済み)

・ 修正申請承認

## 環境構築

### Dockerビルド

1. git clone git@github.com:hi-san10/ams.git

2. docker-compose up -d --build

*MYSQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してください。

### Laravel環境構築

1. docker-compose exec php bash

2. composer install

3. env.example ファイルから .env を作成し、環境変数を変更

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

・ 勤怠情報(出勤、退勤)のダミーデータ50件分

・ 勤怠情報(休憩)のダミーデータ50件分

・ 管理者のダミーデータ1件分

・ ユーザーのダミーデータ1件分↓
![Image](https://github.com/user-attachments/assets/fef256f3-d5c0-446a-8505-87e1527a9970)

## 使用技術

・PHP 8.3

・Laravel 8.83

・MYSQL 8.0

## ER図

![Image](https://github.com/user-attachments/assets/cf635c52-c126-42c2-a8cc-f72b315c29b3)

## テーブル仕様書
![Image](https://github.com/user-attachments/assets/f4395c14-6650-43fd-a1a6-efe420b14921)

![Image](https://github.com/user-attachments/assets/4e09a092-369c-44ee-bf44-d4ed715b6259)

![Image](https://github.com/user-attachments/assets/d7d13156-b32d-4eb5-b7dc-93408c3256e7)

![Image](https://github.com/user-attachments/assets/bb529ba1-1b98-44c4-ae50-abc6bae9b15c)

![Image](https://github.com/user-attachments/assets/3178cf4f-0999-4b5e-b2d3-487d01755437)

## URL

・アプリケーション(開発環境):[http//localhost/](http//localhost/)

・phpMyAdmin:[http//localhost:8080](http/localhost:8080)
