<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo !empty($this->projectName) ? $this->projectName . ' - ' : ''; ?>Online API Document</title>
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
            <?php echo !empty($this->projectName) ? $this->projectName . ' - ' : ''; ?>Online API Document
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
                                <!--title content iten add the class 'active' for choosing-->
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
                                    <span class="ui red ribbon label" style="font-size: 0.96rem">Instructions</span>
                                    <div class="ui message">
                                        <p><?php echo $menuInfo['methodDesc']; ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($menuInfo['tableList'])) { ?>
                                    <h3><i class="tree icon"></i><?php echo $menuInfo['tableTitle'];?></h3>
                                    <table class="ui blue celled striped table">
                                        <thead>
                                        <tr>
                                            <?php $tableHeader = array_shift ($menuInfo['tableList']); ?>
                                            <?php foreach ($tableHeader as $v) { ?>
                                                <th><?php echo $v ?></th>
                                            <?php } ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($menuInfo['tableList'] as $pvalue) { ?>
                                            <tr>
                                                <?php foreach ($pvalue as $v) { ?>
                                                    <td><?php echo $v; ?></td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                                <?php if (!empty($menuInfo['methodParams'])) { ?>
                                    <h3><i class="sign in alternate icon"></i>Inputs</h3>
                                    <table class="ui red celled striped table">
                                        <thead>
                                        <tr>
                                            <th width="30%">Field Name</th>
                                            <th width="15%">Field Type</th>
                                            <th width="15%">Require</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($menuInfo['methodParams'] as $pkey => $pvalue) { ?>
                                            <tr>
                                                <td><?php echo $pvalue['name']; ?></td>
                                                <td><?php echo $pvalue['type']; ?></td>
                                                <td><?php if ($pvalue['require'] == 'true') { ?>
                                                        <span style="color: red">true</span>
                                                    <?php } else { ?>
                                                        false
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $pvalue['desc']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodReturns'])) { ?>
                                    <h3><i class="sign out alternate icon"></i>Outputs</h3>
                                    <table class="ui green celled striped table">
                                        <thead>
                                        <tr>
                                            <th width="30%">Field Name</th>
                                            <th width="15%">Field Type</th>
                                            <th width="15%">Require</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($menuInfo['methodReturns'] as $pkey => $pvalue) { ?>
                                            <tr>
                                                <td><?php echo $pvalue['name']; ?></td>
                                                <td><?php echo $pvalue['type']; ?></td>
                                                <td><?php if ($pvalue['require'] == 'true') { ?>
                                                        <span style="color: red">true</span>
                                                    <?php } else { ?>
                                                        false
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $pvalue['desc']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodExceptions'])) { ?>
                                    <h3><i class="bell icon"></i>Exceptions</h3>
                                    <table class="ui red celled striped table">
                                        <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Message</th>
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
                                        <i class="bug icon"></i>Online Testing &nbsp;&nbsp;
                                    </h3>
                                    <table class="ui green celled striped table">
                                        <thead>
                                        <tr>
                                            <th width="30%">Param</th>
                                            <th width="15%">Require</th>
                                            <th>Value</th>
                                        </tr>
                                        </thead>
                                        <tbody id="params">
                                        <?php foreach ($menuInfo['methodParams'] as $pkey => $pvalue) { ?>
                                            <tr>
                                                <td><?php echo $pvalue['name']; ?></td>
                                                <td><?php if (isset($pvalue['require']) && $pvalue['require']) { ?>
                                                        <font color="red">true</font><?php } else { ?>false<?php } ?>
                                                </td>
                                                <td>
                                                    <div class="ui fluid input">
                                                        <input name="<?php echo $pvalue['name']; ?>" placeholder="<?php echo htmlspecialchars (trim ($pvalue['desc'])); ?>" style="width:100%;" class="C_input" <?php if ($pvalue['type'] == 'file') { ?>type="file" multiple="multiple" <?php }else { ?>type="text" <?php } ?>/>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="ui fluid action input">
                                        <input placeholder="Request Url" type="text" name="request_url" value="<?php echo rtrim ($this->apiBaseUrl, '/') . $menuInfo['methodPath']; ?>">
                                        <button class="ui button green" id="submit">Request Now</button>
                                    </div>
                                    <div class="ui blue message" id="json_output"></div>
                                <?php } ?>

                                <?php if (!empty($menuInfo['methodReturnsExample'])) { ?>
                                    <h3><i class="code icon"></i>Example</h3>
                                    <script>hljs.initHighlightingOnLoad();</script>
                                    <div class="ui text">
                                        <pre><code><?php echo $menuInfo['methodReturnsExample']; ?></code></pre>
                                    </div>
                                <?php } ?>

                                <div class="ui blue message">
                                    <strong>Tips：</strong>
                                    The API List is Generating By The Background Code.
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
                if (e.type != 'file') {
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
