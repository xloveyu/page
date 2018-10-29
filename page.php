<?php
    
    header('content-type:text/html;charset=utf-8');
    
    //定义一个分页函数
    //参数说明
    /*
        $count 要显示的数据总数
        $page_size 每页显示的数量
        $page $_GET[]过来的参数 ( 当前页码数 )
        $btn  显示多少页码数
    */
    function page($count,$page_size,$btn,$page='page'){
        //判断$page的参数是否合法
        if(empty($_GET[$page]) || $_GET[$page]<=1 || !is_numeric($_GET[$page])){
            $_GET[$page]=1;
        }
        //计算一共有多少页
        $page_all=ceil($count/$page_size);
        if($_GET[$page]>$page_all){
            $_GET[$page]=$page_all;
        }
        echo '当前页：'.$_GET[$page].'<br>';
        //limit部分
        $list=($_GET[$page]-1)*$page_size;
        $limit="limit {$list},{$page_size}";
        
        //url部分
        $url=$_SERVER['REQUEST_URI'];   //获取当前url地址
        $arr_url=parse_url($url);       //将当前路径和参数拆分成数组
        $path=$arr_url['path'];
        if(isset($arr_url['query'])){
            parse_str($arr_url['query'],$arr_query);
            unset($arr_query[$page]);
            if(empty($arr_query)){
                $path_url="{$path}?{$page}=";
            }else{
                $then=http_build_query($arr_query);
                $path_url="{$path}?{$then}&{$page}=";
            }
            //print_r($arr_query);
        }else{
            $path_url="{$path}?{$page}=";
        }        
        //html部分
        //判断要显示的页码数量是否合法
        $html=array();
        if($btn>=$page_all){
            for($i=1;$i<=$page_all;$i++){
                if($_GET[$page]==$i){
                    $html[$i]="<span>{$i}</span>";
                }else{
                    $html[$i]="<a href='{$path_url}{$i}'>{$i}</a>";
                }
            }
        }
        //$st=1;
        //计算左边的页码数量
        $btn_left=floor(($btn-1)/2);
        //计算起始页码数
        $st=$_GET[$page]-$btn_left;
        //通过起始页码计算出结束页码数
        $end=$st+($btn-1);
        if($st<1){
            $st=1;
        }
        if($end>$page_all){
            $st=$page_all-($btn-1);
        }
        
        if($btn<$page_all){
            for($i=0;$i<$btn;$i++){
                if($_GET[$page]==$st){
                    $html[$st]="<span>{$st}</span>";
                }else{
                    $html[$st]="<a href='{$path_url}{$st}'>{$st}</a>";
                }
                $st++;
            }
        }
        //页码按钮大于3的时候显示省略号效果
        if($btn>=3){
            reset($html);
            $arr_kai=key($html);
            end($html);
            $arr_end=key($html);
            if($arr_kai!=1){
               array_shift($html);
               array_unshift($html,"<a href='{$path_url}1'>1...</a>");
            }
            if($arr_end!=$page_all){
               array_pop($html);
               array_push($html,"<a href='{$path_url}{$page_all}'>...{$page_all}</a>");
            }
        }
        $prev=$_GET[$page]-1;
        $next=$_GET[$page]+1;
        if($_GET[$page]!=1){
            array_unshift($html,"<a href='{$path_url}{$prev}'>上一页</a>");
        }
        if($_GET[$page]!=$page_all){
            array_push($html,"<a href='{$path_url}{$next}'>下一页</a>");
        }
        $html=implode(' ',$html);
        $data=array(
            'html'=>$html,
            'limit'=>$limit,
        );
        return $data;
    }
    
    $page=page(150,10,6);
    echo '<pre>';
    print_r($page['html']);