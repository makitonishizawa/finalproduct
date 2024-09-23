<?php
//必ずsession_startは最初に記述


//現在のセッションIDを取得


// 以下追加
//　新しいセッションIDを発行（前のSESSION IDは無効）


//新しいセッションIDを取得


//旧セッションIDと新セッションIDを表示
echo '古いセッション:' . $old_session_id . '<br />';
echo '新しいセッション:' . $new_session_id . '<br />';
