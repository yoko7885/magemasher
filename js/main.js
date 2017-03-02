/* global $ location bootbox navigator */
$(function()
{

});

window.MageMasher = {};

var ua = navigator.userAgent;
if (ua.indexOf('iPhone') > 0
        || ua.indexOf('iPod') > 0
        || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0)
{
    window.is_mobile = true;
}
else if (ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0)
{
    window.is_mobile = true;
}
else
{
    window.is_mobile = false;
}

/**
 * Js側でのjsoon形式への変換用関数を定義
 */
$.extend({
    stringify : function stringify(obj) {
        var t = typeof (obj);
        if (t != "object" || obj === null) {
            // simple data type
            if (t == "string") obj = '"' + obj + '"';
            return String(obj);
        } else {
            // recurse array or object
            var n, v, json = [], arr = (obj && obj.constructor == Array);
 
            for (n in obj) {
                v = obj[n];
                t = typeof(v);
                if (obj.hasOwnProperty(n)) {
                    if (t == "string") v = '"' + v + '"'; else if (t == "object" && v !== null) v = $.stringify(v);
                    json.push((arr ? "" : '"' + n + '":') + String(v));
                }
            }
            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
    }
});

/**
 * JSONP通信の共通モジュール
 */
$.getJSONP = function(params, callback, showLoading)
{
	// フラグの状態により通信中画像の表示を制御
	if (showLoading == null || showLoading == true) showLoading = "small";
	
	if ($(".modal"+".in").size() > 0 && showLoading != false)
	{
		showLoading = "modal"
	}
	
	// 通信先URLを生成(アクセス先と同じにする)
    if (window.location.href.indexOf('?')>0)
    {
        var url = window.location.href.substring(0, window.location.href.indexOf('?'));
    }
    else
    {
        var url = window.location.href;
    }
    
    // 通信中画像の表示切替
    if (showLoading)
    {
	    if (showLoading == "small")
    	{
    		$("#loading_small").hide();
		    $("#loading_small").show();
    	}
	    else if (showLoading == "modal")
    	{
	    	if ($(".loading-spinner"+".in").size() <= 0)
    		{
		    	$('body').modalmanager('loading');
    		}
		}
    }
    
    /**-------------------------------------**
     * 通信完了時処理を定義
     **-------------------------------------**/
    var complete = function()
    {
        if (showLoading)
        {
    	    if (showLoading == "small")
        	{
        		$("#loading_small").show();
        		$("#loading_small").fadeOut();
        	}
    	    if (showLoading == "modal")
        	{
    	    	if ($(".loading-spinner"+".in").size() > 0)
        		{
    	    		$('body').modalmanager('removeLoading');
        		}
        	}
        }
    }
    /**-------------------------------------**
     * 通信エラー時処理を定義
     **-------------------------------------**/
    var error = function(jqXHR, textStatus, errorThrown)
    {
    	// ステータスが正常でエラー処理になった場合はリラン
    	if (jqXHR.responseText == "" && jqXHR.status == 200)
		{
    	    return $.ajax({
    	        url : url, data : params, dataType : "jsonp",
    	        success : callback, complete : complete, error : error
    	    });
		}
    	else if (jqXHR.responseText == "" && jqXHR.status == 0)
		{
			// 原因不明その他エラー
    		return;
		}
    	var error_msg = "<div class='alert alert-error'>" + errorThrown.message + "</div>" + 
					"<div class='well'><code>status</code>&nbsp;" + jqXHR.status +  
					"&nbsp;<code>statusText</code>&nbsp;" + jqXHR.statusText +
					"<br/>" + jqXHR.responseText + "</div>";
    	
    	// エラーダイアログ表示
    	bootbox.dialog
    	(
    		{
    			title : textStatus
    			, message : error_msg
				, buttons: {
					send:
					{
						label: "エラー内容を送信",
						className: "btn",
						callback: function() {
							
							// エラー内容送信ボタンで、エラーログ送出要求を行う
							var e_params = {};
							e_params.appId = 'ex';
							e_params.formId = 'error';
							e_params.submitStatus = '';
							e_params.error = {"errorThrown":errorThrown, "textStatus":textStatus, "jqXHR":{
										  "readyState":jqXHR.readyState
										, "responseText":jqXHR.responseText
										, "status":jqXHR.status
										, "statusText":jqXHR.statusText}};
							
						    return $.ajax({
						        url : url, data : e_params, dataType : "jsonp"
						        ,success : function(jsonp){ bootbox.alert("エラー内容を送信しました。"); }
						    	, error : function(jqXHR, textStatus, errorThrown){ bootbox.alert("送信できませんでした。"); }
						    });
						}
					},
					ok:
					{
						label: "閉じる",
						className: "btn-primary",
						callback: function() {}
					},
					logout:
					{
						label: "ログアウト",
						className: "btn-inverse",
						callback: function()
						{
							$("#form_navbar > input[name=appId]").val("ex");
							$("#form_navbar > input[name=formId]").val("logout");
                            $("#form_navbar > input[name=submitStatus]").val("execInit");
							$("#form_navbar").submit();
						}
					}
				}
    		}
    	);
    }
    
    return $.ajax({
        url : url, data : params, dataType : "jsonp", type: 'POST',
        success : callback, complete : complete, error : error
    });
}

/**
 * COOKIEへセット
 */
function setCookie(key, value)
{
   var days = 365 * 10 ;
   var str = key + "=" + escape(value) + "; ";           // 書き出す値１ : key=value
   if (days != 0) {                                     /* 日数 0 の時は省略 */
        var dt = new Date();                            // 現在の日時
        dt.setDate(dt.getDate() + days) ;               // days日後の日時
        str += "expires=" + dt.toGMTString() + "; ";     // 書き出す値２ : 有効期限
   }
   if (location.hostname.indexOf("dapo.jp") > -1) {
       var domain = "dapo.jp";
       str += "domain="+domain+"; ";           // domainの設定
   }
   str += "path=/; ";           // pathの設定
   document.cookie = str;                               // Cookie に書き出し
}

/**
 * COOKIEから取得
 */
function getCookie(key)
{
   var sCookie = document.cookie;                       // Cookie文字列
   var aData = sCookie.split(";");                      // ";"で区切って"キー=値"の配列にする
   var oExp = new RegExp(" ", "g");                     // すべての半角スペースを表す正規表現
   var rtnWord = "" ;
   key = key.replace(oExp, "");                         // 引数keyから半角スペースを除去

   var i = 0;
   while (aData[i]) {                                    /* 語句ごとの処理 : マッチする要素を探す */
        var aWord = aData[i].split("=");                 // さらに"="で区切る
        aWord[0] = aWord[0].replace(oExp, "");           // 半角スペース除去

        if (key == aWord[0]){
            rtnWord = unescape(aWord[1]);                // マッチしたら値を返す
            if( rtnWord == "undefined" ){
                rtnWord = "" ;
            }
            return rtnWord ;
        }
        if (++i >= aData.length) break;                  // 要素数を超えたら抜ける
   }
   return "";                                            // 見つからない時は空文字を返す
}

/* --------------------------------------------
 * 背景色から白黒の自動算出
 * -------------------------------------------- */
function bc_background_color(bgcolor)
{
	if (bgcolor == null) return "";
	
	var str = bgcolor.toUpperCase();
	if (str.substr(0,1) == "#" )
	{
		if (str.length == 7)
		{
			var r = parseInt(str.substr(1, 2), 16);
			var g = parseInt(str.substr(3, 2), 16);
			var b = parseInt(str.substr(5, 2), 16);
		}
		else if (str.length == 4 ) {
			var r = str.substr(1, 1);
			var g = str.substr(2, 1);
			var b = str.substr(3, 1);
			r = parseInt(r.toString() + r.toString(), 16);
			g = parseInt(g.toString() + g.toString(), 16);
			b = parseInt(b.toString() + b.toString(), 16);
		}
	}
	var bright = ((r*299)+(g*587)+(b*114))/1000;
	
	if (bright > 127.5)
	{
		var add = 200;
		r = (r-add)<=0?0:(r-add);
		g = (g-add)<=0?0:(g-add);
		b = (b-add)<=0?0:(b-add);
		r = ('00' + r.toString(16)).slice(-2);;
		g = ('00' + g.toString(16)).slice(-2);;
		b = ('00' + b.toString(16)).slice(-2);;
		var t_color = "#" + r + g + b;
	}
	else
	{
		var add = 200;
		r = (r+add)>=255?255:(r+add);
		g = (g+add)>=255?255:(g+add);
		b = (b+add)>=255?255:(b+add);
		r = ('00' + r.toString(16)).slice(-2);;
		g = ('00' + g.toString(16)).slice(-2);;
		b = ('00' + b.toString(16)).slice(-2);;
		var t_color = "#" + r + g + b;
	}
	if (str)
	{
		return t_color;
	}
	return bgcolor;
}
/**
 * @param birthday: yyyymmdd 形式の誕生日
 */
function calculateAge(birthday)
{
	var birth = [];
	birth[0] = birthday.substr(0,4);
	birth[1] = birthday.substr(4,2);
	birth[2] = birthday.substr(6,2);
	var today = new Date();
	if ( parseInt(birth[1], 10) * 100 + parseInt(birth[2], 10) > (today.getMonth() + 1) * 100 + today.getDate() )
	{
		return today.getFullYear() - parseInt(birth[0], 10) - 1;
	}
	return today.getFullYear() - parseInt(birth[0], 10);
}