<?php
session_start();

//↓ランダムなidが作成される
// $sid = session_id();
// $_SESSION['name'] = 'John Doe'
// echo $sid;

$old_session_id = session_id();

//何か処理,,,,,,,,,,,,,,,,,,,,,,

SESSION_regenerate_id(true);  //新しいIDが発行=>これでsession_id()には新しいidが代入される

$new_session_id = session_id();

echo $old_session_id;
echo '<br>';
echo $new_session_id;

?>