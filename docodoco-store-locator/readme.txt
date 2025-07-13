=== DocoDoco Store Locator ===
Contributors: geolocationtechnology
Tags: store locator, geolocation, map, 店舗検索, 店舗管理
Requires at least: 6.0
Tested up to: 6.5.3
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

ウェブサイトに検索可能な店舗一覧と地図表示を簡単に追加することができるプラグインです。どこどこJPを用いてサイト訪問者に最寄りの店舗を表示することも可能です。

== Description ==

* このプラグインを利用すると、Google マップを用いた店舗一覧ページの作成が簡単に設定できます。
* 店舗情報の追加・編集は、管理画面からの入力またはCSVファイルを用いた一括登録に対応しています。
* どこどこJPの設定を追加することで、IPアドレスと位置情報に基づいてサイト訪問者に最寄りの店舗を表示することが可能です。

= Language =
* Japanese  – default

= どこどこJP =

* このプラグインでは、IPアドレスに基づきサイト訪問者の最寄り店舗を表示するために、[どこどこJP](https://www.docodoco.jp/) のAPIを使用します。
* どこどこJPとは、IPアドレスとそれに紐づいた地域・組織・気象・回線情報など100種類以上の情報を利用できるAPIサービスです。
* 本プラグインを使用することにより、サイトの訪問者のIPアドレス等の情報が、どこどこJPのAPI（api.docodoco.jp）を通じてサービス提供者に送信されることがあります。
* プライバシーポリシーは[サービス提供者のWebサイト](https://www.geolocation.co.jp/privacy/)を確認してください。

= Google Maps API =

* 地図を表示するために Google Maps API を使用します。
* サービス提供者のプライバシーポリシーは「[ポリシーと規約 – Google](https://policies.google.com/)」以下を確認してください。

== Installation ==

1. プラグインをインストールすると、管理画面に「店舗管理」メニューが追加されます。
2. 「表示設定」で、Google Maps Platform APIキーを入力します。
3. 「店舗一覧」または「店舗登録」から店舗情報を登録します。
5. 「導入方法」に表示されるショートコードを公開用のページに入力します。
4. 「表示設定」でテンプレートを選択します。

== Frequently Asked Questions ==

= どこどこJP APIキーの設定とは？ =

* サイト訪問者に最寄り店舗を表示する機能を使用するために、どこどこJPのAPIキーを設定します。
* APIキーをお持ちでない場合は [どこどこJP公式サイト](https://www.docodoco.jp/) からAPIキーを発行できます。
* どこどこJPのAPIキーを設定せずに利用することは可能ですが、位置情報に基づく表示制御が行われなくなります。

= Google Maps Platform APIキーの発行方法は？ =

* Googleアカウントを作成し、Google Cloud Platform のサイトでAPIの有効化を行う必要があります。
* 詳しくは [Google Maps Platform のドキュメント](https://developers.google.com/maps/documentation/javascript/get-api-key) を参照ください。

== Documentation ==

* [プラグイン紹介ページ](https://www.docodoco.jp/plugin/docodoco-store-locator/)

== Screenshots ==
1. 店舗一覧の表示サンプル
2. 管理画面: 店舗管理 -> 店舗一覧
3. ZIPインポートの結果画面

== Changelog ==

= 1.0.1 =

* Windows環境でZIPインポートができない問題を修正

= 1.0.0 =
* 最初のリリース
