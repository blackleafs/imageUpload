最近公司要做一个h5，其中包含了图片裁剪、上传等功能，当时也没太在意就用了cropper插件，因为之前在PC端用过也熟悉，大概看了一下文档，说是支持移动端，就毫不犹豫的选择了它，从此踏上了填坑之路......

######第一个问题：ios竖屏拍照裁剪图片后上传到服务器的图片是顺时针旋转90度
原因：由于目前的手机拍照基本都在2M以上，而ios中只要超过2M图片就会自动旋转。拍照后直接取出来的UIimage（用UIImagePickerControllerOriginalImage取出），它本身的imageOrientation属性是3，即UIImageOrientationRight。如果这个图片直接使用则没事，但是如果对它进行裁剪、缩放等操作后，它的这个imageOrientation属性会变成0。此时这张图片用在别的地方就会发生旋转。imageOrientation是只读的，不能直接修改其值。

解决方法（js）: 当拍照后，获取input中的图片数据，利用exif.js（http://code.ciaoca.com/[JavaScript](http://lib.csdn.net/base/javascript)/exif-js/）获取到Orientation属性，此属性有四个值
1 表示旋转0度，也就是没有旋转。
6 表示顺时针旋转90度
8 表示逆时针旋转90度
3 旋转180度
我们要做的就是在拍照后，从input中获取到图片，然后得到它的Orientation值，在裁剪后给它逆着旋转90度，然后上传就好了，代码在这就不写了。

######第二个问题：ios竖屏拍照后裁剪图片后所得的图片与裁剪区域不相符。
暂时没找到原因，由于之前直接在百度上搜索cropper，然后下载，也没有看是哪个版本，在百度中搜索也完全搜不到相关的问题，最后我在github上cropper项目中issue中找到答案，应该是cropper之前的bug，新版本已经修复，我就试着用了新版本，完美解决，而且新版本中已经解决了第一个问题。

######总结：
这一路下来完全是自己坑了自己，如果一开始直接在GitHub上下载最新版就不会出现这些问题，以后吸取教训。

最后贴上图片裁剪、上传完整代码：
```
<!--html-->
<!--文件上传-->
<input type="file" class="js-uploadfile">
<!--确定裁剪-->
<input type="button" class="js-ok" value="裁剪">
<!--cropper基本结构-->
<div class="container js-container" style="width: 100%;height: auto">
    <img class="js-image" src="" style="width: 100%;height: auto">
</div>
<!--裁剪结果显示-->
<div style="width: 60%;height: auto;margin: 1rem 20%">
    <img id="showImg" src="" alt="" style="width: 100%;height: auto">
</div>
```
```
//js代码
var url = './index.php';
    $(".js-uploadfile").on("change", function () {
        var fr = new FileReader();
        var file = this.files[0];

        fr.readAsDataURL(file);
        fr.onload = function () {
            //这里初始化cropper
            $('.js-image').attr('src', fr.result);
            iniCropper()
        };
    });
    var croppable = false;
    function iniCropper() {
        var $image = $('.js-image'),
            image = $image[0];
        $image.cropper({
            dragMode: 'move',
            aspectRatio: 1,
            autoCropArea: 0.65,
            restore: false,
            viewMode: 1,
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
            rotatable: true,
            checkOrientation: true,
            ready: function () {
                croppable = true;
            }
        });
    }


    $('.js-ok').on('click', function () {
        var dataURL = $('.js-image').cropper("getCroppedCanvas", {
            width: 200,
            height: 200
        });
        var data = {
            imgData: dataURL.toDataURL('image/png')
        };
        submit(url, data);
    });

    function submit(url, data) {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function (res) {
                $('#showImg').attr('src', res.img_name);
            },
            dataType: 'JSON'
        })
    }
```
```
//php代码
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
```


