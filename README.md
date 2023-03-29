# オンデマンド授業システム (作成日: 2022/8/23)

授業動画の視聴や投稿・課題管理などを行えるWebアプリケーションです。

# 特徴

生徒画面と教員画面で分かれており、それぞれ下記特徴があります。

* 生徒画面
  * 登録した情報でログインすることで受講中の動画一覧が表示されます。
  * 動画を最後まで視聴することで受講済みになり、アンケートに回答できるようになります。
  * 動画視聴中はAIが常時監視しており、一定時間画面に映らなくなったり、スマホを操作していた場合は不正とみなし強制終了する機能なども備えています。
* 教員画面
  * 登録された情報でログインすることで投稿した動画一覧が表示されます。
  * 先生方は授業動画の投稿や受講状況の確認・質問への回答など様々な機能を備えています。

# インストール
1. このリポジトリをクローンします。
```git
$ git clone https://github.com/furaidopoteto/LessonApp.git
```
2. dockerでコンテナを立ち上げます。
```
$ cd LessonApp
$ docker-compose -f .on-demand_docker/docker-compose.yml up -d
```
3. コンテナが立ち上がったら「[http://127.0.0.1:8080](http://127.0.0.1:8080)」にアクセスすることで使用できます。※phpMyAdminの起動に時間がかかる場合がございます。

# 使用しているライブラリ

このプロジェクトでは、以下のライブラリを使用しています。

- [jQuery](https://jquery.com/) - JavaScriptのライブラリ (MIT License)
- [Font Awesome](https://fontawesome.com/) - アイコンのフォント (MIT License)
- [Icons8](https://icons8.jp/) - アイコンのフォント (CC BY-ND 3.0)
- [Chart.js](https://www.chartjs.org/) - グラフ描画 (MIT License)
- [Raty](https://github.com/wbotelhos/raty) - 星評価プラグイン (MIT License)

それぞれのライブラリについては、各ライブラリのウェブサイトをご参照ください。

# 使用した学習モデル
- [tfjs-models](https://github.com/tensorflow/tfjs-models/tree/master/coco-ssd) - オブジェクト検出の学習モデル (Apache License 2.0)
