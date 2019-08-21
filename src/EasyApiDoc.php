<?php

namespace AnyChange\EasyApiDoc;
/**
 * 生成文档
 */
class EasyApiDoc {
    /**
     * @var string
     */
    protected $projectName = '';
    /**
     * @var string
     */
    protected $projectApiBaseUrl = '';
    /**
     * @var string
     */
    protected $projectNamespace = '';
    /**
     * @var string
     */
    protected $projectApiPath = '';
    /**
     * @var array
     */
    protected $projectExcludeClassList = array ();
    /**
     * @var array
     */
    protected $projectExcludeFuncList = array ();
    /**
     * @var bool
     */
    protected $projectIsExcludeParentClass = true;
    /**
     * @var array
     */
    protected $selfMenuGroup = array ();
    /**
     * @var array
     */
    protected $selfMenuStartList = array ();
    /**
     * @var array
     */
    protected $selfMenuStopList = array ();
    /**
     * @var array
     */
    protected $typeMaps = array (
        'string'  => '字符串',
        'int'     => '整型',
        'float'   => '浮点型',
        'boolean' => '布尔型',
        'date'    => '日期',
        'array'   => '数组',
        'fixed'   => '固定值',
        'enum'    => '枚举类型',
        'object'  => '对象',
        'file'    => '文件'
    );

    /**
     * EasyApiDoc constructor.
     */
    public function __construct () {
        header ('Content-Type:text/html;charset=utf-8');
    }

    public function setProjectName ($projectName) {
        $this->projectName = $projectName;
    }

    public function setProjectApiBaseUrl ($projectApiBaseUrl) {
        $this->projectApiBaseUrl = $projectApiBaseUrl;
    }

    public function setProjectNamespace ($projectNamespace) {
        $this->projectNamespace = $projectNamespace;
    }

    public function setProjectApiPath ($projectApiPath) {
        $this->projectApiPath = $projectApiPath;
    }

    public function setProjectExcludeClassList ($projectExcludeClassList) {
        $this->projectExcludeClassList = $projectExcludeClassList;
    }

    public function setProjectExcludeFuncList ($projectExcludeFuncList) {
        $this->projectExcludeFuncList = $projectExcludeFuncList;
    }

    public function setProjectIsExcludeParentClass ($projectIsExcludeParentClass) {
        $this->projectIsExcludeParentClass = $projectIsExcludeParentClass;
    }

    public function setSelfMenuGroup ($selfMenuGroup) {
        $this->selfMenuGroup = $selfMenuGroup;
    }

    public function setSelfMenuStartList ($menuGroup, $menuTitle, $methodDesc) {
        $this->selfMenuStartList[$menuGroup]['menuGroup'] = $menuGroup;
        $this->selfMenuStartList[$menuGroup]['subList'][] = array (
            'menuTag'     => $menuTitle,
            'methodTitle' => $menuTitle,
            'methodDesc'  => nl2br ($methodDesc),
        );
    }

    public function setSelfMenuStopList ($menuGroup, $menuTitle, $methodDesc) {
        $this->selfMenuStopList[$menuGroup]['menuGroup'] = $menuGroup;
        $this->selfMenuStopList[$menuGroup]['subList'][] = array (
            'menuTag'     => $menuTitle,
            'methodTitle' => $menuTitle,
            'methodDesc'  => nl2br ($methodDesc),
        );
    }

    /**
     * 显示
     * @Author: zjm
     * @Date  : 2019-08-06 10:16
     * Instruction：param 类型 参数名 是否必须 描述 | return 类型 参数名 描述
     * @desc
     *
     * @param
     *
     * @return
     * @return mixed
     */
    public function onlineShow () {
        $apiList = array ();
        if (!empty($this->selfMenuStartList)) {
            $apiList = array_merge ($apiList, $this->selfMenuStartList);
        }
        $errorMessage = array ();
        if (empty($this->projectApiPath) || !is_dir ($this->projectApiPath)) {
            $files = array ();
            $errorMessage[] = 'The \'projectApiPath\' Is Not Found Or Is Not A Dir Path';
        } else {
            $files = $this->listDir ($this->projectApiPath);
        }
        foreach ($files as $aFile) {
            $apiClassPath = strstr ($aFile, $this->projectApiPath);
            $apiClassPath = str_replace (array ($this->projectApiPath, '/', '.php'), array ('', '\\', ''), $apiClassPath);
            $apiClassPath = ltrim ($apiClassPath, '\\');
            $menuPos = stripos ($apiClassPath, '\\');
            if ($menuPos !== false) {
                $menuGroup = strtolower (substr ($apiClassPath, 0, $menuPos));
            } else {
                $menuGroup = '未分组';
            }

            //类名
            if (empty($this->projectNamespace)) {
                $apiClassNameArr = explode ('\\', $apiClassPath);
                if (empty($apiClassNameArr)) {
                    $apiClassName = $apiClassPath;
                } else {
                    $apiClassName = array_pop ($apiClassNameArr);
                }
                if (!class_exists ($apiClassName)) {
                    include ($aFile);
                }
            } else {
                $apiClassName = $this->projectNamespace . '\\' . $apiClassPath;
            }

            //检测类是否存
            if (!class_exists ($apiClassName)) {
                $errorMessage[] = '"' . $apiClassName . '" Is Not Found,If The Class Exist Namespace,Please Set First.';
                continue;
            }
            //特定类排除
            if (in_array ($apiClassName, $this->projectExcludeClassList)) {
                continue;
            }

            //类标题
            $classTitle = '';
            //类说明
            $classDesc = '';
            // 是否屏蔽此接口类
            $classIgnore = false;
            //处理类注释
            try {
                $rClass = new \ReflectionClass($apiClassName);
                $classDocComment = $rClass->getDocComment ();
            } catch (\Exception $e) {
                $errorMessage[] = $e->getMessage ();
                $classDocComment = false;
            }
            $rClass->getDefaultProperties ();
            //待排除方法
            $all_exclude_methods = array ();
            // 排除父类的方法
            while ($parent = $rClass->getParentClass ()) {
                if ($this->projectIsExcludeParentClass === false) {
                    if (in_array ($parent->getName (), $this->projectExcludeClassList)) {
                        $all_exclude_methods = array_merge ($all_exclude_methods, get_class_methods ($parent->getName ()));
                        break;
                    }
                    $classDocComment = $parent->getDocComment () . "\n" . $classDocComment;
                } else {
                    $all_exclude_methods = array_merge ($all_exclude_methods, get_class_methods ($parent->getName ()));
                    break;
                }
                $rClass = $parent;
            }

            foreach ($this->projectExcludeFuncList as $funcv) {
                if (stripos ($funcv, $apiClassName) !== false) {
                    array_push ($all_exclude_methods, str_replace (array ($apiClassName, '\\'), array ('', ''), strstr ($funcv, $apiClassName)));
                }
            }

            //获取注释类信息
            if ($classDocComment !== false) {
                //以第一行为标题
                $classDocCommentArr = explode ("\n", $classDocComment);
                $classComment = trim ($classDocCommentArr[1]);
                $classTitle = trim (substr ($classComment, strpos ($classComment, '*') + 1));
                array_shift ($classDocCommentArr);
                foreach ($classDocCommentArr as $classComment) {
//                        //标题描述
//                        if (empty($classTitle) && strpos ($classComment, '@') === false && strpos ($classComment, '/') === false) {
//                            $classTitle = substr ($classComment, strpos ($classComment, '*') + 1);
//                            continue;
//                        }
                    //获取类说明
                    $classPos = stripos ($classComment, '@desc');
                    if ($classPos !== false) {
                        $classDesc .= substr ($classComment, $classPos + 5);
                        continue;
                    }
                    //是否屏蔽
                    $classPos = stripos ($classComment, '@ignore');
                    if ($classPos !== false) {
                        $classIgnore = true;
                        continue;
                    }
                }
            }

            if ($classIgnore) {
                continue;
            }
            //获取接口方法
            $methods = array_diff (get_class_methods ($apiClassName), $all_exclude_methods);
            sort ($methods);
            //处理方法注释
            foreach ($methods as $mValue) {
                try {
                    $rMethod = new \Reflectionmethod($apiClassName, $mValue);
                    if (!$rMethod->isPublic () || strpos ($mValue, '__') === 0) {
                        continue;
                    }
                    $methodDocComment = $rMethod->getDocComment ();
                } catch (\Exception $e) {
                    $errorMessage[] = $e->getMessage ();
                    $methodDocComment = false;
                }
                //方法标题
                $methodTitle = '';
                //方法描述
                $methodDesc = '';
                //方法作者
                $methodAuthor = '';
                //方法时间
                $methodDate = '';
                //是否屏蔽方法
                $methodIgnore = false;
                //方法入参
                $methodParams = array ();
                //方法出参
                $methodReturns = array ();
                //返回示例
                $methodReturnsExample = '暂时无返回示例';
                //错误说明
                $methodExceptions = array ();
                if ($methodDocComment !== false) {
                    //以第一行为标题
                    $methodDocCommentArr = explode ("\n", $methodDocComment);
                    $methodComment = trim ($methodDocCommentArr[1]);
                    $methodTitle = trim (substr ($methodComment, strpos ($methodComment, '*') + 1));
                    array_shift ($methodDocCommentArr);
                    foreach ($methodDocCommentArr as $methodComment) {
//                            //标题
//                            if (empty($methodTitle) && strpos ($methodComment, '@') === false && strpos ($methodComment, '/') === false) {
//                                $methodTitle = substr ($methodComment, strpos ($methodComment, '*') + 1);
//                                continue;
//                            }
                        //@desc注释
                        $methodPos = stripos ($methodComment, '@desc');
                        if ($methodPos !== false) {
                            $methodDesc .= substr ($methodComment, $methodPos + 5);
                            continue;
                        }
                        //@exception注释
                        $methodPos = stripos ($methodComment, '@exception');
                        if ($methodPos !== false) {
                            $exArr = explode (' ', trim (substr ($methodComment, $methodPos + 10)));
                            $methodExceptions[$exArr[0]] = $exArr;
                            continue;
                        }
                        //@ignore注释
                        $methodPos = stripos ($methodComment, '@ignore');
                        if ($methodPos !== false) {
                            $methodIgnore = true;
                            continue;
                        }
                        //@param注释
                        $methodPos = stripos ($methodComment, '@param');
                        if ($methodPos !== false) {
                            //将数组中的空值过滤掉，同时将需要展示的值返回
                            $methodParamCommentArr = array_values (array_filter (explode (' ', substr ($methodComment, $methodPos + 7))));
                            $methodParams[] = array (
                                'name'    => isset($methodParamCommentArr[1]) ? $methodParamCommentArr[1] : '',
                                'type'    => isset($methodParamCommentArr[0]) ? (!empty($this->typeMaps[$methodParamCommentArr[0]]) ? $this->typeMaps[$methodParamCommentArr[0]] : $methodParamCommentArr[0]) : '',
                                'require' => isset($methodParamCommentArr[2]) ? $methodParamCommentArr[2] : 'false',
                                'desc'    => isset($methodParamCommentArr[3]) ? $methodParamCommentArr[3] : ''
                            );
                            continue;
                        }
                        //@return注释
                        $methodPos = stripos ($methodComment, '@return');
                        if ($methodPos !== false) {
                            //将数组中的空值过滤掉，同时将需要展示的值返回
                            $methodReturnCommentArr = array_values (array_filter (explode (' ', substr ($methodComment, $methodPos + 8))));
                            $methodReturns[] = array (
                                'name'    => isset($methodReturnCommentArr[1]) ? $methodReturnCommentArr[1] : '',
                                'type'    => isset($methodReturnCommentArr[0]) ? (!empty($this->typeMaps[$methodReturnCommentArr[0]]) ? $this->typeMaps[$methodReturnCommentArr[0]] : $methodReturnCommentArr[0]) : '',
                                'require' => isset($methodReturnCommentArr[2]) ? $methodReturnCommentArr[2] : 'false',
                                'desc'    => isset($methodReturnCommentArr[3]) ? $methodReturnCommentArr[3] : ''
                            );
                            continue;
                        }
                    }
                }

                if ($methodIgnore) {
                    continue;
                }

                $menuGroup = !empty($this->selfMenuGroup[$menuGroup]) ? $this->selfMenuGroup[$menuGroup] : $menuGroup;
                $apiList[$menuGroup]['menuGroup'] = $menuGroup;
                $menuTag = $apiClassName . '\\' . $mValue;
                $apiList[$menuGroup]['subList'][] = array (
                    'menuTag'              => $menuTag,
                    'methodTitle'          => $methodTitle,
                    'methodPath'           => '/' . strtolower (str_replace (array ($this->projectNamespace, '\\'), array ('', '/'), $apiClassPath . '/' . ucfirst ($mValue))),
                    'methodDesc'           => $methodDesc,
                    'methodAuthor'         => $methodAuthor,
                    'methodDate'           => $methodDate,
                    'methodParams'         => $methodParams,
                    'methodReturns'        => $methodReturns,
                    'methodExceptions'     => $methodExceptions,
                    'methodReturnsExample' => $methodReturnsExample
                );
            }
        }

        if (!empty($this->selfMenuStopList)) {
            $apiList = array_merge ($apiList, $this->selfMenuStopList);
        }

        include (dirname (__FILE__) . '/EasyApiDocView.php');
    }

    /**
     * 获取文件列表
     * @Author: zjm
     * @Date  : 2019-08-05 17:13
     * 说明：param 类型 参数名 是否必须 描述 | return 类型 参数名 描述
     * @desc  遍历源码所在文件夹,获取文件列表
     *
     * @param string $dir true 文件目录路径
     *
     * @return array 无 true 文件名列表（带路径）
     */
    private function listDir ($dir) {
        $dir .= substr ($dir, -1) == '/' ? '' : '/';
        $dirInfo = array ();
        foreach (glob ($dir . '*') as $v) {
            if (is_dir ($v)) {
                $dirInfo = array_merge ($dirInfo, $this->listDir ($v));
            } else {
                $dirInfo[] = $v;
            }
        }
        return $dirInfo;
    }

}
