<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w1210 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li><?php if(!Yii::$app->user->isGuest){
                        echo "用户：".Yii::$app->user->identity->username;
                    } ?>
                </li>
                <li>您好，欢迎来到易购！
                    <?php if(!Yii::$app->user->isGuest){
                        echo "[<a href=\"/member/logout\">注销</a>]";
                    }else{
                        echo "[<a href=\"/member/login\">登录</a>] [<a href=\"/member/regist\">免费注册</a>]";
                    } ?>
                </li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

