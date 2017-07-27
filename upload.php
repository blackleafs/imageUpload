<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27
 * Time: 18:57
 */

$post_data = $_POST['imgData'];

if ($_POST) {
    if ($_POST['imgData']) {

        $base_img = $_POST['imgData'];
        $base_img = str_replace('data:image/png;base64,', '', $base_img);
        $path = './avatar/';
        $pre = 'img_';
        $output_file = $pre . time() . rand(100, 999) . '.png';
        $path = $path . $output_file;
        $success = file_put_contents($path, base64_decode($base_img));

        if ($success) {
            $ajax_return = array(
                'code' => 1,
                'msg' => '上传成功',
                'img_name' => $path
            );
            exit(json_encode($ajax_return, JSON_UNESCAPED_UNICODE));
        }
        $ajax_return = array(
            'code' => 0,
            'msg' => '上传失败',
        );
        exit(json_encode($ajax_return, JSON_UNESCAPED_UNICODE));
    } else {
        $ajax_return = array(
            'code' => 0,
            'msg' => '图片为空',
        );
        exit(json_encode($ajax_return, JSON_UNESCAPED_UNICODE));
    }
} else {
    exit('发生错误');
}
