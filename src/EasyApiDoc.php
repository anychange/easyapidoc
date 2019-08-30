<?php

namespace AnyChange\EasyApiDoc;
/**
 * Creat An API Document
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
    protected $selfMenuList = array ();

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

    public function setProjectIsExcludeParentClass ($projectIsExcludeParentClass) {
        $this->projectIsExcludeParentClass = $projectIsExcludeParentClass;
    }

    public function setSelfMenuGroup ($selfMenuGroup) {
        $this->selfMenuGroup = $selfMenuGroup;
    }

    public function setSelfMenuList ($menuGroup, $menuTitle, $methodDesc, $tableTitle = '', $tableList = array (), $menuGroupPosition = 'start') {
        $this->selfMenuList[$menuGroup]['menuGroup'] = $menuGroup;
        $this->selfMenuList[$menuGroup]['menuGroupPosition'] = $menuGroupPosition;
        $this->selfMenuList[$menuGroup]['subList'][] = array (
            'menuTag'     => $menuGroup . $menuTitle,
            'methodTitle' => $menuTitle,
            'methodDesc'  => nl2br (str_replace ('\n', '<br>', $methodDesc)),
            'tableTitle'  => $tableTitle,
            'tableList'   => $tableList,
        );
    }

    /**
     * Show The Document Online
     * @Author: zjm
     * @Date  : 2019-08-06 10:16
     */
    public function onlineShow () {
        $apiList = array ();
        $errorMessage = array ();
        if (empty($this->projectApiPath) || !is_dir ($this->projectApiPath)) {
            $files = array ();
            $errorMessage[] = 'The \'projectApiPath\' Is Not Found Or Is Not A Dir Path';
        } else {
            $files = $this->listDir ($this->projectApiPath);
        }
        try {
            foreach ($files as $aFile) {
                $apiClassPath = strstr ($aFile, $this->projectApiPath);
                $apiClassPath = str_replace (array ($this->projectApiPath, '/', '.php'), array ('', '\\', ''), $apiClassPath);
                $apiClassPath = ltrim ($apiClassPath, '\\');
                $menuPos = stripos ($apiClassPath, '\\');
                if ($menuPos !== false) {
                    $menuGroup = strtolower (substr ($apiClassPath, 0, $menuPos));
                } else {
                    $menuGroup = 'No Group';
                }
                //class
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
                //check the class
                if (!class_exists ($apiClassName)) {
                    $errorMessage[] = '"' . $apiClassName . '" Is Not Found,If The Class Exist Namespace,Please Set First.';
                    continue;
                }

                $classTitle = '';
                $classDesc = '';
                $classIgnore = false;

                $rClass = new \ReflectionClass($apiClassName);
                $classDocComment = $rClass->getDocComment ();

                $rClass->getDefaultProperties ();

                $all_exclude_methods = array ();

                while ($parent = $rClass->getParentClass ()) {
                    if ($this->projectIsExcludeParentClass === false) {
                        $classDocComment = $parent->getDocComment () . "\n" . $classDocComment;
                    } else {
                        $all_exclude_methods = array_merge ($all_exclude_methods, get_class_methods ($parent->getName ()));
                        break;
                    }
                    $rClass = $parent;
                }

                if ($classDocComment !== false) {
                    //Get The Title
                    $classDocCommentArr = explode ("\n", $classDocComment);
                    $classComment = trim ($classDocCommentArr[1]);
                    $classTitle = trim (substr ($classComment, strpos ($classComment, '*') + 1));
                    array_shift ($classDocCommentArr);
                    foreach ($classDocCommentArr as $classComment) {
//                        if (empty($classTitle) && strpos ($classComment, '@') === false && strpos ($classComment, '/') === false) {
//                            $classTitle = substr ($classComment, strpos ($classComment, '*') + 1);
//                            continue;
//                        }

                        $classPos = stripos ($classComment, '@desc');
                        if ($classPos !== false) {
                            $classDesc .= substr ($classComment, $classPos + 5);
                            continue;
                        }

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

                $methods = array_diff (get_class_methods ($apiClassName), $all_exclude_methods);
                sort ($methods);

                foreach ($methods as $mValue) {
                    $rMethod = new \Reflectionmethod($apiClassName, $mValue);
                    if (!$rMethod->isPublic () || strpos ($mValue, '__') === 0) {
                        continue;
                    }
                    $methodDocComment = $rMethod->getDocComment ();

                    $methodTitle = '';
                    $methodDesc = '';
                    $methodAuthor = '';
                    $methodDate = '';
                    $methodIgnore = false;
                    $methodParams = array ();
                    $methodReturns = array ();
                    $methodReturnsExample = 'No Examples Return';
                    $methodExceptions = array ();
                    if ($methodDocComment !== false) {
                        $methodDocCommentArr = explode ("\n", $methodDocComment);
                        $methodComment = trim ($methodDocCommentArr[1]);
                        $methodTitle = trim (substr ($methodComment, strpos ($methodComment, '*') + 1));
                        array_shift ($methodDocCommentArr);
                        foreach ($methodDocCommentArr as $methodComment) {
//                            if (empty($methodTitle) && strpos ($methodComment, '@') === false && strpos ($methodComment, '/') === false) {
//                                $methodTitle = substr ($methodComment, strpos ($methodComment, '*') + 1);
//                                continue;
//                            }

                            $methodPos = stripos ($methodComment, '@desc');
                            if ($methodPos !== false) {
                                $methodDesc .= substr ($methodComment, $methodPos + 5);
                                continue;
                            }

                            $methodPos = stripos ($methodComment, '@exception');
                            if ($methodPos !== false) {
                                $exArr = explode (' ', trim (substr ($methodComment, $methodPos + 10)));
                                $methodExceptions[$exArr[0]] = $exArr;
                                continue;
                            }

                            $methodPos = stripos ($methodComment, '@ignore');
                            if ($methodPos !== false) {
                                $methodIgnore = true;
                                continue;
                            }

                            $methodPos = stripos ($methodComment, '@param');
                            if ($methodPos !== false) {
                                $methodParamCommentArr = array_values (array_filter (explode (' ', substr ($methodComment, $methodPos + 7))));
                                $methodParams[] = array (
                                    'type'    => (string)array_shift ($methodParamCommentArr),
                                    'name'    => (string)array_shift ($methodParamCommentArr),
                                    'require' => (string)array_shift ($methodParamCommentArr),
                                    'desc'    => implode (' ',$methodParamCommentArr)
                                );
                                continue;
                            }

                            $methodPos = stripos ($methodComment, '@return');
                            if ($methodPos !== false) {
                                $methodReturnCommentArr = array_values (array_filter (explode (' ', substr ($methodComment, $methodPos + 8))));
                                $methodReturns[] = array (
                                    'type'    => (string)array_shift ($methodReturnCommentArr),
                                    'name'    => (string)array_shift ($methodReturnCommentArr),
                                    'require' => (string)array_shift ($methodReturnCommentArr),
                                    'desc'    => implode (' ',$methodReturnCommentArr)
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
        } catch (\Exception $e) {
            $errorMessage[] = $e->getMessage ();
        }

        if (!empty($this->selfMenuList)) {
            foreach ($this->selfMenuList as $gk => $gv) {
                if ($gv['menuGroupPosition'] == 'start') {
                    $apiList = array_merge (array ($gk => $gv), $apiList);
                } else {
                    $apiList = array_merge ($apiList, array ($gk => $gv));
                }
            }
        }

        include (dirname (__FILE__) . '/EasyApiDocView.php');
    }

    /**
     * Get The File List
     * @Author: zjm
     * @Date  : 2019-08-05 17:13
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
