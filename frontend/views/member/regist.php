
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>用户注册</title>
    <link rel="stylesheet" href="/style/base.css" type="text/css">
    <link rel="stylesheet" href="/style/global.css" type="text/css">
    <link rel="stylesheet" href="/style/header.css" type="text/css">
    <link rel="stylesheet" href="/style/login.css" type="text/css">
    <link rel="stylesheet" href="/style/footer.css" type="text/css">
</head>
<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li>您好，欢迎来到京西！[<a href="login.html">登录</a>] [<a href="register.html">免费注册</a>] </li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="index.html"><img src="/images/logo.png" alt="京西商城"></a></h2>
    </div>
</div>
<!-- 页面头部 end -->

<!-- 登录主体部分start -->
<div class="login w990 bc mt10 regist">
    <div class="login_hd">
        <h2>用户注册</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <form action="<?=\yii\helpers\Url::to(['member/regist'])?>" method="post" id="signupForm">
                <ul>
                    <li>
                        <label for="">用户名：</label>
                        <input type="text" class="txt" name="username" id="username"/>
                        <p>3-20位字符，可由中文、字母、数字和下划线组成</p>
                    </li>
                    <li>
                        <label for="">密码：</label>
                        <input type="password" class="txt" name="password" />
                        <p>6-20位字符，可使用字母、数字和符号的组合，不建议使用纯数字、纯字母、纯符号</p>
                    </li>
                    <li>
                        <label for="">确认密码：</label>
                        <input type="password" class="txt" name="repassword" />
                        <p> <span>请再次输入密码</p>
                    </li>
                    <li>
                        <label for="">邮箱：</label>
                        <input type="text" class="txt" name="email" />
                        <p>邮箱必须合法</p>
                    </li>
                    <li>
                        <label for="">手机号码：</label>
                        <input type="text" class="txt" value="" name="tel" id="tel" placeholder=""/>
                    </li>
                    <li>
                        <label for="">验证码：</label>
                        <input type="text" class="txt" value="" placeholder="请输入短信验证码" name="checkcode1" disabled="disabled" id="captcha"/> <input type="button" onclick="bindPhoneNum(this)" id="get_captcha" value="获取验证码" style="height: 25px;padding:3px 8px"/>

                    </li>
                    <li class="checkcode">
                        <label for="">验证码：</label>
                        <input type="text"  name="checkcode" />
                        <img id="captcha_ima" alt="" />
                        <span>看不清？<a href="javascript:;" id="change_captcha">换一张</a></span>
                    </li>

                    <li>
                        <label for="">&nbsp;</label>
                        <input type="checkbox" class="chb" checked="checked" /> 我已阅读并同意《用户注册协议》
                    </li>
                    <li>
                        <label for="">&nbsp;</label>
                        <input type="submit" value="" class="login_btn" />
                    </li>
                </ul>
            </form>


        </div>

        <div class="mobile fl">
            <h3>手机快速注册</h3>
            <p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
            <p><strong>1069099988</strong></p>
        </div>

    </div>
</div>
<!-- 登录主体部分end -->

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        ? 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><img src="/images/xin.png" alt="" /></a>
        <a href=""><img src="/images/kexin.jpg" alt="" /></a>
        <a href=""><img src="/images/police.jpg" alt="" /></a>
        <a href=""><img src="/images/beian.gif" alt="" /></a>
    </p>
</div>
<!-- 底部版权 end -->
<script src="http://static.runoob.com/assets/jquery-validation-1.14.0/lib/jquery.js"></script>
<script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js"></script>
<script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/localization/messages_zh.js"></script>
<script type="text/javascript">
    function bindPhoneNum(){
        //启用输入框
        $('#captcha').prop('disabled',false);

        var time=30;
        var interval = setInterval(function(){
            time--;
            if(time<=0){
                clearInterval(interval);
                var html = '获取验证码';
                $('#get_captcha').prop('disabled',false);
            } else{
                var html = time + ' 秒后再次获取';
                $('#get_captcha').prop('disabled',true);
            }

            $('#get_captcha').val(html);
        },1000);
        //点击触发发送短信
        $.post("<?=\yii\helpers\Url::to(['member/sms'])?>",{'tel':$("#tel").val()},function($data){
            console.log($data);
        })
    }
    $().ready(function() {
        // 在键盘按下并释放及提交后验证提交表单
        $("#signupForm").validate({
            rules: {
                username: {
                    required: true,//用户名 必填
                    minlength: 3,
                    maxlength: 20,
                    remote: "<?=\yii\helpers\Url::to(['member/validate-user'])?>"//验证用户名唯一
                },
                password: {
                    required: true,//密码必填
                    minlength: 6,
                    maxlength: 20
                },
//                repassword: {
//                    required: true,//密码必填
//                    minlength: 6,
//                    maxlength: 20,
//                    equalTo:"#password"
//                },
                email: {
                    required: true,//you必填
                    minlength: 6,
                    maxlength: 20,
                    email:true
                },
                tel: {
                    required: true,//手机号码必填
                    minlength: 11,
                    maxlength: 11,
                    //tel: 'isTel'
                },
                checkcode1:{
                    required: true,
                    remote: {
                        url: "<?=\yii\helpers\Url::to(['member/message'])?>",     //后台处理程序
                        type: "get",               //数据发送方式
                        //dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            tel: function() {
                                return $("#tel").val();
                            }
                        }
                    }
                },


                checkcode: "validateCaptcha"//使用自定义验证规则验证验证码

            },
            messages: {
                username: {
                    required: "请输入用户名",
                    minlength: "用户名必需由3个字母组成",
                    maxlength: "用户名不能超过20字母组成",
                    remote:"用户名已存在"
                },
                password: {
                    required: "请输入密码",
                    minlength: "密码至少6位",
                    maxlength: "密码不能超过20位"

                },
                repassword: {
                    required: "请输入密码",
                    minlength: "密码至少6位",
                    maxlength: "密码不能超过20位",
                    equalTo:"和原来密码必须一致"

                },
                email: {
                    required: "请输入邮箱",
                    email:"必须是邮箱格式"
                },
                tel: {
                    required: "请输入手机号码",
                    minlength: "号码必须是11位",
                    maxlength: "号码必须是11位",
                    tel:"请输入合法电话"
                }

            },
            errorElement:'span'//错误信息的标签
        });
    });
    var hash;
    //获取验证码
    var captcha_url = '<?=\yii\helpers\Url::to(['site/captcha'])?>'
    //获取新验证码的URL
    var change_captcha =function () {
        $.getJSON(captcha_url,{refresh:1},function (json) {
            $('#captcha_ima').attr('src',json.url);
            //获取哈希
            hash=json.hash1;
        });
    }

    //刷新验证码
    change_captcha();//验证码初始化
    $("#change_captcha").click(function(){
        change_captcha();
    });
    //添加自定义规则
    jQuery.validator.addMethod("validateCaptcha", function(value, element) {
        var h ;
        for (var i = value.length - 1, h = 0; i >= 0; --i) {
            h += value.charCodeAt(i);
        }
        return this.optional(element) || (h == hash);
    }, "验证码不正确");
</script>
</body>
</html>