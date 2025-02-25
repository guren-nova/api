# FortifyBot API

```markdown
# FortifyBot API

FortifyBot APIへようこそ！このAPIは、メッセージ内に不適切なワードが含まれているかどうかをチェックするシンプルな方法を提供します。レートリミットとメッセージのサニタイズ機能を備えており、安全に利用できます。

## API エンドポイント

### メッセージ処理

このAPIの主なエンドポイントは次の通りです：

```
https://www.fortifybot.xyz/api/v1/words.php/?message={message}
```

### パラメーター
- message: チェックしたいメッセージ。このパラメーターは必須で、特殊文字やスペースを含む場合はURLエンコードしてください。

### 使用例

```
https://www.fortifybot.xyz/api/v1/words.php/?message=Hello%20World
```

### レスポンス

APIは、メッセージに不適切なワードが含まれているかどうかを示すJSONオブジェクトを返します。

#### 成功した場合のレスポンス

```json
{
  "contains_bad_words": false
}
```

#### エラーが発生した場合のレスポンス

メッセージが提供されていない場合や不正なリクエストが送信された場合、エラーメッセージが返されます。

- **メッセージがない場合**（400 Bad Request）

```json
{
  "error": "メッセージが必要です"
}
```

- **レートリミットを超えた場合**（429 Too Many Requests）

```json
{
  "error": "レートリミットを超えました。しばらくしてから再試行してください。"
}
```

- **不正なリクエストメソッドの場合**（405 Method Not Allowed）

```json
{
  "error": "不正なリクエストメソッドです"
}
```

### レートリミット

このAPIにはレートリミットが設定されており、過度なリクエストを防ぎます。レートリミットは以下の通りです：

- 1秒あたり最大2リクエストまで。

レートリミットを超えた場合は、`429 Too Many Requests` のレスポンスが返され、しばらく後に再試行するように指示されます。

### メッセージのサニタイズ

APIは、入力メッセージを自動的にサニタイズします。これにより、次の処理が行われます：

- HTMLタグの削除
- 特殊文字のHTMLエンティティへの変換

これにより、クロスサイトスクリプティング（XSS）攻撃を防ぎます。

## 使用方法

FortifyBot APIを使用するには、上記のエンドポイントにGETリクエストを送信し、メッセージをクエリパラメーターとして渡します。

### Pythonでの使用例：

```python
import requests

message = "Hello World"
url = f"https://www.fortifybot.xyz/api/v1/words.php/?message={message}"
response = requests.get(url)

if response.status_code == 200:
    data = response.json()
    if data['contains_bad_words']:
        print("メッセージに不適切なワードが含まれています。")
    else:
        print("メッセージは問題ありません。")
else:
    print("APIリクエストにエラーが発生しました。")
```

### Node.js（JavaScript）での使用例：

```javascript
const fetch = require('node-fetch');

const message = 'Hello World';
const url = `https://www.fortifybot.xyz/api/v1/words.php/?message=${encodeURIComponent(message)}`;

fetch(url)
  .then(response => response.json())
  .then(data => {
    if (data.contains_bad_words) {
      console.log("メッセージに不適切なワードが含まれています。");
    } else {
      console.log("メッセージは問題ありません。");
    }
  })
  .catch(error => {
    console.error('エラー:', error);
  });
```

## ライセンス

このプロジェクトは、カスタムライセンスのもとで提供されています。詳細については、[LICENSE](./LICENSE) ファイルをご覧ください。

## お問い合わせ

質問やサポートが必要な場合は、[guren-nova@fortifybot.net] までご連絡ください。
