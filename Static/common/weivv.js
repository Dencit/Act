var weivv=function(){
    //自定义标签名
    this.weiPage='wvv';
    this.weiblock='block';
    this.weifield='field';
    this.weiList='list';
    //函数调试
    this.iDebug={'ale':'false', 'con':'true'};
    //对象调试
    this.oDebug={'objCon':'true'};
    //页面调试
    this.pDebug={'ale':'false', 'con':'true'};
    //
    this.postSubmitData='';
    this.postListSubmitData='';
    //
    this.getSubmitData='';
    this.getListSubmitData='';
    //
    this.dataChecked=true;
    //
};
weivv.prototype={
    ////start
    init:function(){
        //自定义标签初始化
        $("["+this.weiPage+"]").hide();
        //
        //模态窗初始化
        this.selector='.modal_bg';
        var html='<div class="modal" >'+
            '<p class="modal_content">提示内容</p>'+
            '<p class="modal_ok">知道了</p>'+
            '</div>';
        var div = document.createElement("div");
        div.setAttribute("class", "modal_bg");
        div.innerHTML=html;

        document.body.appendChild(div);
        //
    },


    ////页面工具
    page:function(type,action){
        var allPage=$("["+this.weiPage+"]");
        allPage.hide();
        var currPage=$("["+this.weiPage+"='"+type+"']");
        switch (action){
            default :  break;
            case 'show': currPage.show(); break;
            case 'hide': currPage.hide(); break;
            case 'fadeIn': currPage.fadeIn(); break;
            case 'fadeOut': currPage.fadeOut(); break;
            case 'slideDown': currPage.slideDown(); break;
            case 'slideUp': currPage.slideUp(); break;
        }

        this.currPage=type;


        var block="["+this.weiPage+"='"+this.currPage+"'] ";
        var field="["+this.weifield+"]";
        this.Block=block;
        this.Field=field;
        //console.log(block);console.log(field);//
        var listBlock="["+this.weiPage+"='"+this.currPage+"'] ["+this.weiList+"='"+type+"'] ";
        var listField="["+this.weifield+"]";
        this.ListBock=listBlock;
        this.ListField=listField;

        //
        this.postSubmitData='';
        this.postListSubmitData='';
        //
        this.getSubmitData='';
        this.getListSubmitData='';
        //

        return this;
    },
    doCallback : function (fn,args)
    {
        fn.apply(this, args);
    },
    block:function(type,action,allAction){
        var allblock="["+this.weiPage+"='"+this.currPage+"'] ["+this.weiblock+"] ";
        var block="["+this.weiPage+"='"+this.currPage+"'] ["+this.weiblock+"='"+type+"'] ";
        var field="["+this.weifield+"]";
        this.Block=block;
        this.Field=field;
        //console.log(block);console.log(field);//

        var allBlock=$(allblock);
        var currBlock=$(block);

        if(allAction!=undefined){ allBlock.hide(); }

        if(action!=undefined){
            switch (action){
                default :  break;
                case 'show': currBlock.show(); break;
                case 'hide': currBlock.hide(); break;
                case 'fadeIn': currBlock.fadeIn(); break;
                case 'fadeOut': currBlock.fadeOut(); break;
                case 'slideDown': currBlock.slideDown(); break;
                case 'slideUp': currBlock.slideUp(); break;
            }
        }

        return this;
    },
    thisData:function(action,mydata){
        switch (action){
            default : this.submitData=this.submitData(this.Block,this.Field);
                break;
            case 'post':
                if(mydata!=undefined){  this.postSubmitData=mydata; }//需要发送自定义数据时,修改当前数据缓存
                else{this.postSubmitData=this.submitData(this.Block,this.Field);}
                //console.log(postSubmitData);//
                break;
            case 'get':
                if(mydata!=undefined){  this.getSubmitData=mydata; }//需要发送自定义数据时,修改当前数据缓存
                else{this.getSubmitData=this.submitData(this.Block,this.Field);}
                //console.log(getSubmitData);//
                break;
        }

        return this;
    },
    List:function(type){
        var listBlock="["+this.weiPage+"='"+this.currPage+"'] ["+this.weiList+"='"+type+"'] ";
        var listField="["+this.weifield+"]";
        this.ListBock=listBlock;
        this.ListField=listField;
        //console.log(listBlock);console.log(listField);//

        return this;
    },
    thisListData:function(action,mydata){
        switch (action){
            default : this.postListSubmitData=this.submitListData(this.ListBock,this.ListField);
                break;
            case 'post':
                if(mydata!=undefined){  this.postListSubmitData=mydata; }//需要发送自定义数据时,修改当前数据缓存
                else{
                    this.postListSubmitData=this.submitListData(this.ListBock,this.ListField);
                }
                //console.log(this.postListSubmitData);//
                break;
            case 'get':
                if(mydata!=undefined){  this.getListSubmitData=mydata; }//需要发送自定义数据时,修改当前数据缓存
                else{
                    this.getListSubmitData=this.submitListData(this.ListBock,this.ListField);
                }
                //console.log(this.getListSubmitData);//
                break;
        }

        return this;
    },
    check:function(func){

        var checked=func(this.postSubmitData);

        //console.log(checked);//

        if(checked==false){
            this.dataChecked=false;
        }else{
            this.dataChecked=true;
        }
        //console.log(this.dataChecked);//

        return this;
    },
    listCheck:function(func){

        var checked=func(this.postListSubmitData);

        //console.log(checked);//

        if(checked==false){
            this.dataChecked=false;
        }else{
            this.dataChecked=true;
        }
        //console.log(this.dataChecked);//

        return this;
    },
    //把块内 元素 发送到后端
    toPost:function(url,func){
        if(this.dataChecked!=false){
            this.post(url,this.postSubmitData,func);
        }
    },
    //把块内 列表元素 发送到后端
    toPostList:function(url,func){
        if(this.dataChecked!=false){
            this.post(url,this.postListSubmitData,func);
        }
    },
    //把后端数据 填入块内元素
    toGet:function(url,func){
        var This=this;

        this.post(url,this.getSubmitData,function(result){

            //console.log('here');

            resultData=result.data;
           // console.log(resultData);//

            This.setResult(This.Block,This.Field,resultData);

            func(result);
        });

    },
    //把后端 列表数据 填入块内元素
    toGetList:function(url,func,dataLevel,tag){

        var This=this;

        this.post(url,this.getListSubmitData,function(result){

            var resultData=eval('result.data.'+dataLevel);
            console.log(resultData);//

            This.setResultList(This.ListBock,This.ListField,resultData,tag);

            func(result);
        });

    },
    //\\


    ////模态窗
    set:function(content,redirect){

        $(this.selector+' .modal_content').text(content);

        if(redirect!=''){this.redirectto=redirect}

        var This=this;
        $('.modal_ok').bind('click',function(){
            This.hide();
            if(This.redirectto){
                setTimeout(function(){
                    window.location.replace(This.redirectto);
                },200);
            }
        });
    },
    show:function(){
        $(this.selector).fadeIn();
    },
    hide:function(){
        $(this.selector).fadeOut();
    },
    toggle:function(timeout){
        var sec=2000;
        if(timeout!=undefined){ sec=timeout; }
        this.show();

        var This=this;
        setTimeout(function(){
            This.hide();
        },sec);

        if(this.redirectto) {
            setTimeout(function(){
                window.location.replace(This.redirectto);
            },sec+320);
        }
    },
    //\\


    ////公用

    //静态页获取序列值[?]
    request :function (paras){
        var url = location.href;
        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
        var paraObj = {};
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }
        var returnValue = paraObj[paras.toLowerCase()];
        if(typeof(returnValue)=="undefined"){
            return "";
        }else{
            return returnValue;
        }
    },
    //静态页获取序列值[#]
    htmReq:function (paras){
        var url = location.href;
        var paraString = url.substring(url.indexOf("#")+1,url.length).split("&");
        var paraObj = {};
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }
        var returnValue = paraObj[paras.toLowerCase()];
        if(typeof(returnValue)=="undefined"){
            return "";
        }else{
            return returnValue;
        }
    },
    //静态页获取序列值[#]
    find : function (str,cha,num){
        var x=str.indexOf(cha);
        for(var i=0;i<num;i++){
            x=str.indexOf(cha,x+i);
        }
        return x;
    },
    pathReq : function (paras){
        var url = location.href;

        //console.log( find(url,'/',6) );//
        var paraString = url.substring(find(url,'/',6)+1,url.length).split("/");
        //console.log(paraString);//

        var paraObj = {};
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("-")).toLowerCase()] =decodeURI( j.substring(j.indexOf("-")+1,j.length) );
        }
        //console.log(paraObj);//

        var returnValue = paraObj[paras.toLowerCase()];
        if(typeof(returnValue)=="undefined"){
            return "";
        }else{
            return returnValue;
        }
    },
    uri_get : function ($sign,$start,$url){
        if($sign==undefined){$sign='?'}
        if($start==undefined){$start='1'}

        var url='';
        if($url==undefined){  url= location.href; }
        else{ url=$url }

        //console.log( url );
        //console.log( find(url,$sign,$start) );//地址栏,符合(斜杠),第几个斜杠
        var paraString = url.substring(find(url,$sign,$start)+1,url.length).split($sign);
        //console.log(paraString);//
        return paraString;
    },

    //获取日期：前天、昨天、今天、明天、后天
    GetDateStr : function (AddDayCount) {
        var dd = new Date();
        dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
        var y = dd.getFullYear();
        var m = dd.getMonth()+1;//获取当前月份的日期
        var d = dd.getDate();
        return y+"-"+m+"-"+d;
    },

    //去掉所有html标记
    delHtmlTag : function (str){
        return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
    },

    //随机值
    random : function (n, m){
        return Math.floor(Math.random()*(m-n+1)+n);
    },

    //验证集合
    name_Reg:/^[\u4e00-\u9fa5|a-zA-Z]*$/,
    mobile_Reg:/^1[3|4|5|7|8]\d{9}$/,
    ctMobile_Reg:/^(133|153|177|180|181|189)\d{8}$/,
    vCode_Reg:/^[0-9]{6}$/,
    phone_Reg:/^(020)?[0-9]{7,8}$/,
    account_Reg:/^(020)?[0-9]{7,8}$/,
    city_Reg:/^[\u4e00-\u9fa5|a-zA-Z]*$/,
    sex_Reg:/^(男|女)$/,
    nameReg:function(data){
        if(data==''||$name_Reg.test(data)===false){
            alert("请输入中文或英文姓名！");
            return false;
        }
    },
    mobileReg:function(data){
        if(data==''||$mobile_Reg.test(data)===false){
            alert("请输入手机号码！");
            return false;
        }
    },
    ctMobileReg:function(data){
        if(data==''||$ctMobile_Reg.test(data)===false){
            alert("请输入广州电信手机号码！");
            return false;
        }
    },
    vCodeReg:function(data){
        if(data==''||$vCode_Reg.test(data)===false){
            alert("请输入六位数验证码！");
            return false;
        }
    },
    phoneReg:function(data){
        if(data==''||$phone_Reg.test(data)===false){
            alert("请输入广州本地固话号码！");
            return false;
        }
    },
    accountReg:function(data){
        if(data==''){
            alert("请输入广州本地宽带号码！");
            return false;
        }
    },

    //测试用弹窗
    iAlert:function(data){
        if(this.iDebug.ale=='true'){
            alert("iAlert[   "+data+"   ]");
        }
        if(this.iDebug.con=='true'){
            console.log("iConsole[   "+data+"   ]");
        }
    },
    iObjCon:function(objRes){
        if(this.oDebug.objCon=='true'){
            console.log(objRes);
        }
    },
    pAlert:function(data){
        if(this.pDebug.ale=='true'){
            alert("pAlert[   "+data+"   ]");
        }
        if(this.pDebug.con=='true'){
            console.log("pConsole[   "+data+"   ]");
        }
    },
    //json_text to json
    jsonGet:function(data){
    var $jsonObj=eval("("+data+")");
    return $jsonObj;
    },
    //\\


    ////异步工具
    //纯json post
    post:function(url,inputData,func,async){
        if(async==''){
            $.ajaxSetup({async : true});//默认异步型
        }else{
            $.ajaxSetup({async : false});//阻断型
        }
        var This=this;
        $.ajax({
            url: url,
            type: 'POST',
            data: inputData,
            dataType: 'html',
            cache:false,
            success: function (result) {
                This.iAlert("返回HTML数据::↓↓::postBackHtmlData");//正确时 输出html内容//错误时 直接输出后端报错信息
                This.iAlert(result);
                var res = This.jsonGet(result);//转为Object对象//json
                This.iAlert("返回JSON数据::↓↓::getBackJsonData");
                This.iObjCon(res);

                func(res);

            },
            error:function(msg){

                This.iAlert("getBackResult::↓↓");
                This.iObjCon(msg.toSource());//

            }
        });

    },
    imgUpload:function($selector,$num,$function){

        $file=$($selector).val(); //console.log($file);//
        if($file==''){ alert('请选择文件！'); return false; }

        if (!(window.File || window.FileReader || window.FileList || window.Blob)) { alert('该浏览器不支持文件上传！');}

        $file_list=$($selector).prop('files');//console.log( $file_list );//
        $file_desc=$file_list[0]; //console.log( $file_desc );//

        if(!/\.(?:jpg|png|gif|bmp)$/.test($file_desc.name)){ alert('请上传jpg、png、bmp、gif格式的文件！'); return false;}

        var $M=2*131072; if($num!=''){ $M=$num*131072; } //console.log($M);return false;//
        if($file_desc.size>$M){ alert('上传文件不能大于'+$num+'m ！'); return false; }

        if( $file_desc.type.indexOf('image')=='0' && /\.(?:jpg|png|gif|bmp)$/.test($file_desc.name) ){

            var $reader = new FileReader();//新建一个FileReader
            $reader.readAsDataURL($file_list[0]);//读取文件
            $reader.onload = function($e){ //读取完文件之后会回来这里
                var $fileString = $e.target.result; //console.log($fileString);//

                $function($fileString);

            };

        }

    },


    //获取列表多单元数据,整合成单个post对象
    //$list_selector//列表单元 li对象
    //$child_obj//表达式说明// ([变量名]/[目标选择器]/[对象值],[...]/[...]/[...]) //

    listData:function($list_selector,$child_obj,$condition){

        $list=$($list_selector);

        $child=$child_obj.split(',');//切割 子对象表达式 中的 ','
        //console.log($child);//
        $listData={};

        $list.each(function(index){
            $index=index;

            //切割单个 子-子对象 表达式 获得key和val
            $child2=new Array;
            for(var i=0;i<$child.length;i++){
                $child2[i]=$child[i].split('/');//切割 子-子对象表达式 中的 '/'
                //console.log( $child2[i][0] );
            }
            //console.log($child2);//

            //拼接成 json 字符串
            var $json_str="{";
            for(var j=0;j<$child2.length;j++){
                var $val='0';

                //对选择器 取值类型 做判断
                switch ($child2[j][2]){
                    default :
                        var $data_type=$child2[j][2];
                        if($data_type.substring(4,0)=='data'){
                            $data=$data_type.substr(5);
                            //alert($data);//
                            $val=$($list_selector+' '+$child2[j][1]).eq($index).data($data);
                        }
                        break;
                    case 'text':
                        $val=$($list_selector+' '+$child2[j][1]).eq($index).text();
                        break;
                    case 'val':
                        $val=$($list_selector+' '+$child2[j][1]).eq($index).val();
                        break;
                }

                //开始拼接
                if(j!=$child2.length-1 ){
                    $json_str+="'"+$child2[j][0]+"':'"+$val+"',";
                }else{
                    $json_str+="'"+$child2[j][0]+"':'"+$val+"'}";
                }

            }
            //console.log($json_str);//


            //目标val 是否符合 筛选规则
            $condi=$condition;
            if($condi!=undefined){
                var $condi_arr=$condi.split('/');
                //console.log($condi_arr);//

                var $cval='',$sval='',$ctype='',$csymbol='';

                $ctype=$condi_arr[1].substr(4);
                //console.log($ctype);//
                $sval=$($list_selector+' '+$condi_arr[0]).eq($index).val();
                //console.log( $sval );//
                $cval=$condi_arr[1].substring(5);
                //console.log( $cval );//
                $csymbol=$condi_arr[1].substring(3,5);
                //console.log($csymbol);//

                switch($csymbol){//符号判断
                    case '!=' :
                        if($sval!=$cval){
                            $listData[$index]=eval( "("+$json_str+")" );
                        }
                        break;
                    case '==':
                        if($sval==$cval){
                            $listData[$index]=eval( "("+$json_str+")" );
                        }
                        break;
                    case '>>':
                        if($sval>$cval){
                            $listData[$index]=eval( "("+$json_str+")" );
                        }
                        break;
                    case '<<':
                        if($sval<$cval){
                            $listData[$index]=eval( "("+$json_str+")" );
                        }
                        break;
                    case '>=':
                        if($sval>=$cval){
                            $listData[$index]=eval( "("+$json_str+")" );
                        }
                        break;
                    case '<=':
                        if($sval>=$cval){
                            $listData[$index]=eval( "("+$json_str+")" );
                        }
                        break;
                }
            }else{
                $listData[$index]=eval( "("+$json_str+")" );
            }

        });

        //console.log($listData);//

        if(Object.keys($listData).length === 0){
            return false;
        }else{
            return $listData;
        }


    },

    submitData:function(selector,submit_child,index){

        //console.log( $(selector) );//
        //console.log( $(submit_child) );//

        $child=$(selector).find(submit_child);
        if(index!=undefined){ $child=$(selector).eq(index).find(submit_child) }
        //console.log( $child );//

        var submitData={};

        $child.each(function(index){

            $tagName=$(this)[0].tagName;
            $tagField=$(this).attr('field');

            //console.log( $tagName );//
            //console.log( $tagField );//

            switch($tagName){
                default : break;
                case 'HEADER':case 'BODY':case 'P':case 'B':
                case 'H1':case 'H2':case 'H3':case 'H4':case 'H5':case 'H6':
                case 'UL':case 'OL':case 'LI':case'DL':case'DT':case'DD':
                case 'TH':case 'TR':case 'TD':case 'TBODY':
                case 'SECTION':case 'DIV': case 'SPAN':
                    $val=$(this).text();
                    eval('submitData.'+$tagField+'="'+$val+'"');
                    break;
                case 'INPUT':
                    $val=$(this).val();
                    eval('submitData.'+$tagField+'="'+$val+'"');
                    break;
                case 'SELECT':
                    $val=$(this).find('option:checked').val();
                    eval('submitData.'+$tagField+'="'+$val+'"');
                    break;
                case 'TEXTAREA':
                    $val=$(this).val();
                    eval('submitData.'+$tagField+'="'+$val+'"');
                    break;
            }

        });


        //console.log(submitData);//
        return submitData;


    },

    submitListData:function(selector,submit_child,$filter){

        //console.log( $(selector) );//
        //console.log( $(submit_child) );//

        var liSelector=selector+' li';
        $liSelector=$(liSelector);
        //console.log( $liSelector );//

        var submitListData={};

        var This=this ;

        $liSelector.each(function(ind){

            eval('submitListData['+ind+']={}');
            //console.log( submitListData );//


            var liData=This.submitData(liSelector,submit_child,ind);
            //console.log(liData);//

            eval('submitListData['+ind+']=liData');


        });

        //console.log( submitListData );//
        return submitListData;

    },


    setResult:function(selector,set_child,result,index){

        //console.log( $(selector) );//
        //console.log( $(set_child) );//

        $child=$(selector).find(set_child);

        if(index!=undefined){ $child=$(selector).eq(index).find(set_child) }

        //console.log( $child );//

        $child.each(function(index){

            //console.log( $(this) );//

            $tagName=$(this)[0].tagName;
            $tagField=$(this).attr('field');

            //console.log( $tagName );//
            //console.log( $tagField );//

            var setResult=result;

            switch($tagName){
                default : break;
                case 'HEADER':case 'BODY':case 'P':case 'B':
                case 'H1':case 'H2':case 'H3':case 'H4':case 'H5':case 'H6':
                case 'UL':case 'OL':case 'LI':case'DL':case'DT':case'DD':
                case 'TH':case 'TR':case 'TD':case 'TBODY':
                case 'SECTION':case 'DIV': case 'SPAN':
                    var field=eval('setResult.'+$tagField);
                    $(this).html(field);
                    break;
                case 'INPUT':
                    var field=eval('setResult.'+$tagField);
                    $(this).val(field);
                    break;
                case 'SELECT':
                    var field=eval('setResult.'+$tagField);
                    $(this).find("option[value='"+field+"']").prop('selected',true);
                    break;
                case 'TEXTAREA':
                    var field=eval('setResult.'+$tagField);
                    $(this).val(field);
                    break;
                case 'IMG':
                    var field=eval('setResult.'+$tagField);
                    $(this).attr('src',field);
                    break;
            }

        });

        return this;
    },
    setResultList:function(selector,set_child,resultData,tag){

        //console.log( $(selector) );//
        //console.log( $(set_child) );//

        if(tag==undefined){ tag='li'; }

        var liSelector=selector+' '+tag;
        $liSelector=$(liSelector);
        //console.log( $liSelector );//

        $liSample=$liSelector.html();
        //console.log( $liSample );//

        var html='';
        for(var i in resultData){
            html+='<'+tag+'>' + $liSample + '</'+tag+'>';
        }
        $(selector).html(html);

        $liSelector=$(liSelector);//重新引用
        //console.log( $liSelector );//

        for( var n in resultData ){

            //console.log(n);//
            this.setResult(liSelector,set_child,resultData[n],n);
        }

    },

    msg:function(errmsg,redirect,style){



        if(errmsg){

            if(style==undefined||style==0){ alert( errmsg ); }
            if(style==1||style=='bootstrap'){


                $('#glob_modal').html(
                    '<div id="g_modal" style="top:20%;" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true"> ' +
                    '<div class="modal-dialog modal-sm">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '<span class="sr-only">Close</span></button> ' +
                    '<h4 class="modal-title" id="myModalLabel">提示</h4> ' +
                    '</div> ' +
                    '<div class="modal-body">...</div> ' +
                    '<div class="modal-footer"> ' +
                    '<button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button> ' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );


                $('.modal-body').text(errmsg);
                $('#g_modal').modal('show');

            }
            if(style==2){

                this.set(errmsg,redirect);
                this.toggle();
            }

        }

        if(errmsg==''||errmsg==undefined&&redirect){
            window.location.replace(redirect);
        }

        if(redirect&&style!=2&&style!=1){
            window.location.replace(redirect);
        }
    },

    msgbox:function(result,style){

        //console.log(result);

        if(style==undefined){ style=0 }

        switch (result.errcode){
            case '-1' :
                //错误 带提示
                this.msg(result.errmsg,result.redirect,style);
                break;
            case '0' :
                //正确 带提示
                this.msg(result.errmsg,result.redirect,style);
                break;
            case '1' :
                //正确 不提示 直接跳转
                this.msg(result.errmsg,result.redirect,style);
                break;
        }

    }

    //\\end

};

wvv=new weivv();

