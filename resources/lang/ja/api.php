<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'error_message' => [
        'no_object' => '検索結果がなし',
        'login_failed' => 'メールまたはパスワードが間違っています',
        'create_token_error' => 'トークンが作成できません',
        'token_expired' => 'トークンは期限切れます',
        'token_invalid' => 'トークンが間違っています',
        'token_required' => 'トークンはトークンが必要があります',
        'token_black_list' => 'トークンはブラックリスト',
        'logout_error' => 'ログアウトエラー',
        'logout_success' => 'ログアウト成功',
        'mail_error' => 'メール送信できません',
        'udp_error' => '他のユーザによってデータが更新されたため、更新が行えませんでした。入力内容を確認の上、更新を行う場合は再度送信してください',
        'un_authorization' => '権限がありません',
        'process_success' => '成功します',
        'time_error' => '契約期間が重複しています。',
        'mail_not_exits' => '指定したメールアドレスが存在しません。',
        'old_pass_not_match' => '現在のパスワードが一致しません。',
        'disable_link' => 'パスワード再設定リンクが無効です。',
        'invalid_format' => ':valid_format形式で指定してください。',
        'deleted_user_login' => 'ユーザーが既に削除されていました。管理者にご連絡ください',
        'mail' => 'メールのみロクインしてください',
        'no_input_collateral_error' => '担保有りの場合、担保企業を選択してください。',
        'no_input_finance_error' => 'ファイナンス有りの場合、ファイナンス会社を選択してください。',
        'no_input_document' => 'ファイルを選択してください。',
        'exits_document' => '同じファイルが存在しています。',
        'error_length' => 'ファイル名は80文字以下で指定してください。',
        'not_work_day_error' => '支払い日には営業日を入力してください。',
        'not_import_file' => 'CSVファイル入力してください。',
        'length_import_over_10mb' => 'ファイルサイズが大きすぎます。10MB以下にしてください。',
        'import_format_failed' => 'ファイルのフォーマットが正しくありません。',
    ],
    'time_status' => [
        'expried' => '契約満了',
        'avaiable' => '契約中',
        'future'  => '将来の契約'
    ]
];
