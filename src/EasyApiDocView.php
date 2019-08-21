<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo !empty($this->projectName)?$this->projectName.' - ':''; ?>Online API Document</title>
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.bootcss.com/semantic-ui/2.4.1/semantic.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    <link rel="stylesheet" href="https://cdn.bootcss.com/highlight.js/9.15.9/styles/default.min.css">
    <script src="https://cdn.bootcss.com/highlight.js/9.15.9/highlight.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>
<div class="ui fixed inverted menu">
    <div class="ui container">
        <a href="javascript:;" class="header item">
            <?php echo !empty($this->projectName)?$this->projectName.' - ':''; ?>Online API Document
        </a>
    </div>
</div>
<div class="row" style="margin-top: 60px;"></div>
<div id="menu_top" class="ui text container" style="max-width: none !important; width: 1200px">
    <div class="ui floating message">
        <?php if (!empty($errorMessage)) { ?>
            <div class="ui error message">
                <strong>Error：<?php foreach ($errorMessage as $error) {
                        echo $error; ?><br><?php } ?></strong>
            </div>
        <?php } ?>
        <?php if (!empty($apiList)) { ?>
            <div class="ui grid container" style="max-width: none !important;">
                <div class="four wide column">
                    <div class="ui vertical accordion menu">
                        <div class="item">
                            <h4>The API Service List</h4>
                        </div>
                        <?php foreach ($apiList as $menuGroup => $groupInfo) { ?>
                            <div class="item">
                                <!--以下title content iten 选中追加class名active-->
                                <h4 class="title " style="font-size:16px;margin:0px;">
                                    <i class="dropdown icon"></i>
                                    <?php echo $groupInfo['menuGroup'] ?>
                                </h4>
                                <div class="content " style="margin-left:-16px;margin-right:-16px;margin-bottom:-13px;">
                                    <?php foreach ($groupInfo['subList'] as $menuInfo) { ?>
                                        <a class="item " data-tab="<?php echo str_replace ('\\', '', $menuInfo['menuTag']); ?>"><?php echo $menuInfo['methodTitle'] ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="item">
                            <div class="content" style="margin:-13px -16px;">
                                <a class="item" href="#menu_top">Back To Top↑↑↑</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="twelve wide stretched column">
                    <?php $num2 = 0;
                    foreach ($apiList as $menuGroup => $groupInfo) {
                        foreach ($groupInfo['subList'] as $menuInfo) { ?>
                            <div class="ui  tab <?php if ($num2 == 0) { ?>active<?php } ?>" data-tab="<?php echo str_replace ('\\', '', $menuInfo['menuTag']); ?>">
                                <h2 class='ui header'><?php echo $menuInfo['methodTitle']; ?></h2><br/>
                                <span class='ui teal tag label' style="font-size: 0.96rem"><?php echo $menuInfo['methodPath']; ?></span>
                                <div class="ui raised segment">
                                    <span class="ui red ribbon label" style="font-size: 0.96rem">说明</span>
                                    <div class="ui message">
                                        <p><?php echo $menuInfo['methodDesc']; ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($menuInfo['methodParams'])) { ?>
                                    <h3><i class="sign in alternate icon"></i>接口参数</h3>
                                    <table class="ui red celled striped table">
                                        <thead>
                                        <tr>
                                            <th width="30%">参数名字</th>
                                            <th width="15%">参数类型</th>
                                            <th width="15%">是否必须</th>
                                            <th>说明</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($menuInfo['methodParams'] as $pkey => $pvalue) { ?>
                                            <tr>
                                                <td><?php echo $pvalue['name']; ?></td>
                                                <td><?php echo $pvalue['type']; ?></td>
                                                <td><?php if ($pvalue['require'] == 'true') { ?>
                                                        <span style="color: red">是</span>
                                                    <?php } else { ?>
                                                        否
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $pvalue['desc']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodReturns'])) { ?>
                                    <h3><i class="sign out alternate icon"></i>返回结果</h3>
                                    <table class="ui green celled striped table">
                                        <thead>
                                        <tr>
                                            <th width="30%">返回字段</th>
                                            <th width="15%">字段类型</th>
                                            <th width="15%">是否必返</th>
                                            <th>说明</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($menuInfo['methodReturns'] as $pkey => $pvalue) { ?>
                                            <tr>
                                                <td><?php echo $pvalue['name']; ?></td>
                                                <td><?php echo $pvalue['type']; ?></td>
                                                <td><?php if ($pvalue['require'] == 'true') { ?>
                                                        <span style="color: red">是</span>
                                                    <?php } else { ?>
                                                        否
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $pvalue['desc']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodExceptions'])) { ?>
                                    <h3><i class="bell icon"></i>异常情况</h3>
                                    <table class="ui red celled striped table">
                                        <thead>
                                        <tr>
                                            <th>错误码</th>
                                            <th>错误描述信息</th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($menuInfo['methodExceptions'] as $exItem) { ?>
                                            <tr>
                                                <td><?php echo $exItem[0]; ?></td>
                                                <td><?php echo isset($exItem[1]) ? $exItem[1] : ''; ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodPath'])) { ?>
                                    <h3>
                                        <i class="bug icon"></i>在线测试 &nbsp;&nbsp;
                                    </h3>
                                    <table class="ui green celled striped table">
                                        <thead>
                                        <tr>
                                            <th width="30%">参数</th>
                                            <th width="15%">是否必填</th>
                                            <th>值</th>
                                        </tr>
                                        </thead>
                                        <tbody id="params">
                                        <?php foreach ($menuInfo['methodParams'] as $pkey => $pvalue) { ?>
                                            <tr>
                                                <td><?php echo $pvalue['name']; ?></td>
                                                <td><?php if (isset($pvalue['require']) && $pvalue['require']) { ?>
                                                        <font color="red">必须</font><?php } else { ?>可选<?php } ?>
                                                </td>
                                                <td>
                                                    <div class="ui fluid input">
                                                        <input name="<?php echo $pvalue['name']; ?>" placeholder="<?php echo htmlspecialchars (trim ($pvalue['desc'])); ?>" style="width:100%;" class="C_input" <?php if ($pvalue['type'] == '文件') { ?>type="file" multiple="multiple" <?php }else { ?>type="text" <?php } ?>/>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="ui fluid action input">
                                        <input placeholder="请求的接口链接" type="text" name="request_url" value="<?php echo rtrim ($this->projectApiBaseUrl, '/').$menuInfo['methodPath']; ?>">
                                        <button class="ui button green" id="submit">请求当前接口</button>
                                    </div>
                                    <div class="ui blue message" id="json_output"></div>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodReturnsExample'])) { ?>
                                    <h3><i class="code icon"></i>接口返回示例</h3>
                                    <script>hljs.initHighlightingOnLoad();</script>
                                    <div class="ui text">
                                        <pre><code><?php echo $menuInfo['methodReturnsExample']; ?></code></pre>
                                    </div>
                                <?php } ?>

                                <div class="ui blue message">
                                    <strong>温馨提示：</strong>
                                    此接口服务列表根据后台代码自动生成。
                                </div>
                            </div>
                            <?php $num2++;
                        }
                    } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="ui inverted vertical footer segment" style="margin-top:30px; background: #1B1C1D none repeat scroll 0% 0%;">
    <div class="ui container">
        <div class="ui stackable inverted divided equal height stackable grid">
            <div class="eight wide column centered">
                <div class="column" align="center"></div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    $('.ui.sticky').sticky();
    $('.accordion.menu a.item').tab({'deactivate': 'all'}).click(function () {
        //当点击跳转链接后，回到页面顶部位置
        $('body,html').animate({scrollTop: 0}, 500);
        return false;
    });
    $('.ui.accordion').accordion({'exclusive': false});

    $("#json_output").hide();
    $("#submit").on("click", function () {
        var data = getData();
        var url_arr = $("input[name=request_url]").val().split('?');
        var url = url_arr.shift();
        var param = url_arr.join('?');
        param = param.length > 0 ? param + '&' + data.param : data.param;
        url = url + '?' + param;
        $.ajax({
            url: url,
            type: 'post',
            data: data.data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (res, status, xhr) {
                $("#json_output").html('<pre>' + res + '</pre>').show();
            },
            error: function (error) {
                console.log(error)
            }
        })
    });

    function getData() {
        var data = new FormData();
        var param = [];
        $("td input").each(function (index, e) {
            if ($.trim(e.value)) {
                if (e.type != '文件') {
                    if ($(e).data('source') == 'get') {
                        param.push(e.name + '=' + e.value);
                    } else {
                        data.append(e.name, e.value);
                    }
                } else {
                    var files = e.files;
                    if (files.length == 1) {
                        data.append(e.name, files[0]);
                    } else {
                        for (var i = 0; i < files.length; i++) {
                            data.append(e.name + '[]', files[i]);
                        }
                    }
                }
            }
        });
        param = param.join('&');
        return {param: param, data: data};
    }

</script>
</html>
