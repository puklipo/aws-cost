# AWSコスト

## Overview

AWSのコストを取得しBlueskyのDMで通知するツール。GitHub Actionsで毎日定期実行される。

## Technology Stack
- PHP 8.4
- Laravel 12.x (コンソールスターターキットでartisanコマンドのみ使うプロジェクト構成。Web関連機能はないことに注意)

## コアファイル
- `app/Console/Commands/AwsCostCommand.php`: AWSコストを取得しBlueskyに通知するコマンド
- `app/Notifications/AwsCostNotification.php`: 通知クラス
- `.github/workflows/cost.yml`: GitHub Actionsのワークフローファイル
