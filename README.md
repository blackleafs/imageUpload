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

