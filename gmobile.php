<?php 
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$mode = $_GET['mode'];
$hotelno = $_GET['hotelno'];
$uagent = $_SERVER['HTTP_USER_AGENT'];
$upper = '';
$lower = '';

//$pos = strpos($uagent,"Android");
if($lat==''||$lng==''){
	$lat='35.6065914';
	$lng='139.7513225';
}
if($mode=='')
	$mode = 'hotel';
if($hotelno!='')
	$mode = 'gourmet';

if (strpos($uagent, 'Android') !== false) {
	$uagent='Android';
}else {
	$uagent='Desktop';
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
html {
	height: 100%
}

body {
	height: 100%;
	margin: 0px;
	padding: 0px
}

#map_canvas {
	height: 100%
}
</style>
<script src= "json.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
	var HotelXHR = new XMLHttpRequest();
	var HotelJSON;   
	var HotelUrl = 'geohoteljson.php';

	var GourmetXHR = new XMLHttpRequest();
	var GourmetJSON;   
	var GourmetUrl = 'geogourmetjson.php';

	var StationXHR = new XMLHttpRequest();
	var StationJSON;   
	var StationUrl = 'geostationjson.php';

	var DiningXHR = new XMLHttpRequest();
	var DiningJSON;   
	var DiningUrl = 'stationdiningjson.php';

	var LocalXHR = new XMLHttpRequest();
	var LocalJSON;   
	var LocalUrl = 'hotelidcouponjson.php';
	
	//var console = document.getElementById('console');
	var curl;
	var infowindow = new Array;
	var markers = new Array;

	var hmarker = null;
	var hwindow;
	
	var map = null;
	var mode = null;
	var station = null;
	var hotelid = null;
	var lat = null;
	var lng = null;
	var device = '<?echo $uagent?>';
	
	function connectHotel(){
		if(infowindow.length!=0)
			closeInfoWindows();
		infowindow = new Array;

		if(markers.length!=0)
			for ( var i= 0; i < markers.length; i++)
				markers[i].setMap(null);
		markers = new Array;

		hmarker = null;
		hwindow = null;

		ihotel = document.getElementById('ihotel');
		curl = HotelUrl+"?lat="+escape(lat)+"&lng="+escape(lng);
		HotelXHR.open('GET', curl, true);  
		HotelXHR.onreadystatechange = function (aEvt) {  
			if (HotelXHR.readyState == 4) {  
				if(HotelXHR.status == 200){
					if(HotelXHR.responseText!=']')
						HotelJSON = eval(HotelXHR.responseText);
					else 
						HotelJSON = JSON.parse("[]");

					//HotelJSON = JSON.parse(HotelXHR.responseText);
					//var result="";
					for ( var i= 0; i < HotelJSON.length; i++) {
						//result += (HotelJSON[i].hotelName+"<br>");
						var location = new google.maps.LatLng(HotelJSON[i].latitude, HotelJSON[i].longitude);
	
						var contentString = 
							"<div class=iw_buttons align='center'><input type='button' style='background-color:lightpink' class='bt_goumetsearch' value='グルメ検索' onclick=\"setStation('"+HotelJSON[i].nearestStation+"');setLatLng("+HotelJSON[i].latitude+","+HotelJSON[i].longitude+");saveHotel("+i+",'"+HotelJSON[i].hotelNo+"');setGourmetMode();update();\"></div>"+
							"<div align='center' class=iw_images>"+"<a href='"+HotelJSON[i].hotelInformationUrl+"'>"+"<img style='height:64' src='"+HotelJSON[i].hotelThumbnailUrl+"'/>"+"</a>"+"</div>"+
							"<div align='center' class=iw_names><b>"+HotelJSON[i].hotelName+"</b></div>"+
							//"<div align='center' class=iw_names><b>最寄駅名称</b>"+HotelJSON[i].nearestStation+"</div>"+
							"<div align='center' class=iw_mincharges><b>最安料金</b>:"+HotelJSON[i].hotelMinCharge+"円</div>";
							//"<div align='center' class=iw_addresses>"+HotelJSON[i].address+"</div>";
							
						infowindow[i] = new google.maps.InfoWindow({
						    content: contentString
						});
						
						markers[i] = new google.maps.Marker({
							  map:map,
						      position: location,
						      title:HotelJSON[i].hotelName,
						      icon:'http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=accomm|00FF00',
						      //shadow:'http://chart.apis.google.com/chart?chst=d_map_pin_shadow'
							  shadow:new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
								      // The shadow image is larger in the horizontal dimension
								      // while the position and offset are the same as for the main image.
								      new google.maps.Size(64, 64),
								      new google.maps.Point(-20,-28),
								      null)
						  });
						//marker.setMap(map);  
						google.maps.event.addListener(markers[i], 'click', infoCallback(infowindow[i],markers[i],HotelJSON[i].latitude,HotelJSON[i].longitude));
					}
					if(HotelJSON.length>0)
						ihotel.innerHTML = "<img style='height:35' title='"+HotelJSON.length+"件' src='http://travel.rakuten.co.jp/share/themes/header/images/logo_travel_w89.gif'/>";
					else
						ihotel.innerHTML = "<img style='height:35' title='0件' src='grey/logo_travel_w89_grey.png'/>";
					//
				}else  
					ihotel.innerHTML="［楽天トラベル］  ERROR";
					//alert("Error loading Hotel\n");  
			}else ihotel.innerHTML="［楽天トラベル］  connecting...";  
		};  
		HotelXHR.send(null);
		setColorMode('lightgreen');
		google.maps.event.addListener(map, 'click', function() {
			//setLatLng(map.getCenter().b,map.getCenter().c);
			closeInfoWindows();
		});
	}

	function initConnect(){
		if(infowindow.length!=0)
			closeInfoWindows();
		infowindow = new Array;

		if(markers.length!=0)
			for ( var k= 0; k < markers.length; k++)
				markers[k].setMap(null);
		markers = new Array;
	}

	function connectGourmet(){
		igourmet = document.getElementById('igourmet');
		igourmet.innerHTML="［ぐるナビ］ connecting...";
		curl = GourmetUrl+"?lat="+escape(lat)+"&lng="+escape(lng);
		GourmetXHR.open('GET', curl, true);  
		GourmetXHR.onreadystatechange = function (aEvt) {  
			if (GourmetXHR.readyState == 4) {  
				if(GourmetXHR.status == 200){
					if(GourmetXHR.responseText!=']')
						GourmetJSON = eval(GourmetXHR.responseText);
					else 
						GourmetJSON = JSON.parse("[]");

					//GourmetJSON = JSON.parse(GourmetXHR.responseText);
					//var result="";
					var baseiw = infowindow.length;
					var basemk = markers.length;
					for ( var i= 0; i < GourmetJSON.length; i++) {
						var location = new google.maps.LatLng(GourmetJSON[i].latitude, GourmetJSON[i].longitude);
	
						var contentString =  
							"<div align='center' class=iw_images>"+"<a href='"+GourmetJSON[i].restaurantUrl+"'>"+"<img style='height:64' src='"+GourmetJSON[i].restaurantImageUrl+"'/>"+"</a>"+"</div>"+ 
							"<div align='center' class=iw_names><b>"+GourmetJSON[i].name+"</b></div>"+
							"<div class=iw_addresses><b>カテゴリ</b>："+GourmetJSON[i].category+"</div>"+
							"<div class=iw_prs><b>平均予算：</b>"+GourmetJSON[i].budget+"円</div>";
							"<div class=iw_addresses><b>住所</b>："+GourmetJSON[i].address+"</div>"+
							"<div class=iw_prs><b>PR：</b>"+GourmetJSON[i].pr+"</div>";

						if(GourmetJSON[i].coupon.length>0){
							contentString+="<div class=iw_coupons>";
							for ( var x = 0; x < GourmetJSON[i].coupon.length; x++) {
								contentString += "<div>";
								contentString += GourmetJSON[i].coupon[x];
								contentString += "</div>"; 
							}
							contentString+="</div>";
						}
								
							//"<div class=iw_buttons align='center'><input type='button' style='background-color:lightblue' class='bt_goumetsearch' value='グルメ検索' onclick='setLatLng("+GourmetJSON[i].latitude+","+GourmetJSON[i].longitude+");update();'></div>";
							
						infowindow[i+baseiw] = new google.maps.InfoWindow({
						    content: contentString
						});
						
						markers[i+basemk] = new google.maps.Marker({
							  map:map,
						      position: location,
						      title:GourmetJSON[i].hotelName,
						      icon:'http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=restaurant|ffb6c1',
						      //shadow:'http://chart.apis.google.com/chart?chst=d_map_pin_shadow'
							  shadow:new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
								      // The shadow image is larger in the horizontal dimension
								      // while the position and offset are the same as for the main image.
								      new google.maps.Size(64, 64),
								      new google.maps.Point(-20,-28),
								      null)
						  });
						//marker.setMap(map);  
						google.maps.event.addListener(markers[i+basemk], 'click', infoCallback(infowindow[i+baseiw],markers[i+basemk],GourmetJSON[i].latitude,GourmetJSON[i].longitude));
					}

					if(hmarker!=null){
						hmarker.setMap(map);
						ihotel = document.getElementById('ihotel');
						ihotel.innerHTML = "<img style='height:35' src='http://travel.rakuten.co.jp/share/themes/header/images/logo_travel_w89.gif'/>";
						if(GourmetJSON.length>0)
							igourmet.innerHTML = "<img style='height:35' title='"+GourmetJSON.length+"件' src='image/rsz_l_gnavi.png'/>";
						else
							igourmet.innerHTML = "<img style='height:35' title='0件' src='grey/rsz_l_gnavi_grey.png'/>";
					}else{
						if(GourmetJSON.length>0)
							igourmet.innerHTML = "<img style='height:35' title='"+GourmetJSON.length+"件' src='image/rsz_l_gnavi.png'/>";
						else
							igourmet.innerHTML = "<img style='height:35' title='0件' src='grey/rsz_l_gnavi_grey.png'/>";
					}
					//google.maps.event.addListener(hmarker, 'click', infoCallback(hwindow,hmarker,hmarker.position.b,hmarker.position.c));
					
					//alert("gourmet finished");
				}else 
					igourmet.innerHTML="［ぐるナビ］ ERROR"; 
					//alert("Error loading Gourmet\n");  
			}else igourmet.innerHTML="［ぐるナビ］ loading...";  
		};  

		GourmetXHR.send(null);
		setColorMode('lightpink');
		google.maps.event.addListener(map, 'click', function() {
			//setLatLng(map.getCenter().b,map.getCenter().c);
			closeInfoWindows();
		});
	}
	
	function connectDining(){
		curl = DiningUrl+"?station="+station;
		idining = document.getElementById('idining');
		idining.innerHTML="［楽天ダイニング］ connecting..."; 
		DiningXHR.open('GET', curl, true);  
		DiningXHR.onreadystatechange = function (aEvt) {  
			if (DiningXHR.readyState == 4) {  
				if(DiningXHR.status == 200){
					if(DiningXHR.responseText!=']')
						DiningJSON = eval(DiningXHR.responseText);
					else 
						DiningJSON = JSON.parse("[]");
					//var result="";
					var baseiw = infowindow.length;
					var basemk = markers.length;
					for ( var i= 0; i < DiningJSON.length; i++) {
						var location = new google.maps.LatLng(DiningJSON[i].latitude, DiningJSON[i].longitude);
	
						var contentString = 
							"<div align='center' class=iw_images>"+"<a href='"+DiningJSON[i].restaurantUrl+"'>"+"<img style='height:64' src='"+DiningJSON[i].restaurantImageUrl+"'/>"+"</a>"+"</div>"+ 
							"<div align='center' class=iw_names><b>"+DiningJSON[i].name+"</b></div>"+
							"<div class=iw_addresses><b>カテゴリ</b>："+DiningJSON[i].category+"</div>"+
							"<div class=iw_prs><b>平均予算：</b>"+DiningJSON[i].budget+"円</div>";
							"<div class=iw_addresses><b>住所</b>："+DiningJSON[i].address+"</div>"+
							"<div class=iw_prs><b>PR：</b>"+DiningJSON[i].pr+"</div>";

							contentString+="<div class=iw_coupons>";
							contentString += ("<div>"+DiningJSON[i].point+"</div>");
							contentString += ("<div>"+DiningJSON[i].reserve+"</div>"); 
							contentString+="</div>";
							
							//"<div class=iw_buttons align='center'><input type='button' style='background-color:lightblue' class='bt_goumetsearch' value='グルメ検索' onclick='setLatLng("+DiningJSON[i].latitude+","+DiningJSON[i].longitude+");update();'></div>";
							
						infowindow[i+baseiw] = new google.maps.InfoWindow({
						    content: contentString
						});
						
						markers[i+basemk] = new google.maps.Marker({
							  map:map,
						      position: location,
						      title:DiningJSON[i].hotelName,
						      icon:'http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=restaurant|ff0000',
						      //shadow:'http://chart.apis.google.com/chart?chst=d_map_pin_shadow'
							  shadow:new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
								      // The shadow image is larger in the horizontal dimension
								      // while the position and offset are the same as for the main image.
								      new google.maps.Size(64, 64),
								      new google.maps.Point(-20,-28),
								      null)
						  });
						//marker.setMap(map);  
						google.maps.event.addListener(markers[i+basemk], 'click', infoCallback(infowindow[i+baseiw],markers[i+basemk],DiningJSON[i].latitude,DiningJSON[i].longitude));
					}

					if(hmarker!=null){
						hmarker.setMap(map);
						ihotel = document.getElementById('ihotel');
						ihotel.innerHTML = "<img style='height:35' src='http://travel.rakuten.co.jp/share/themes/header/images/logo_travel_w89.gif'/>";
						if(DiningJSON.length>0)
							idining.innerHTML = "<img style='height:35' title='"+DiningJSON.length+"件' src='http://static.dining.rakuten.co.jp/img/img_dining_logo.gif'/>";
						else
							idining.innerHTML = "<img style='height:35' title='0件' src='grey/img_dining_logo_grey.png'/>";
					}else{
						if(DiningJSON.length>0)
							idining.innerHTML = "<img style='height:35' title='"+DiningJSON.length+"件' src='http://static.dining.rakuten.co.jp/img/img_dining_logo.gif'/>";
						else
							idining.innerHTML = "<img style='height:35' title='0件' src='grey/img_dining_logo_grey.png'/>";
					}
					//http://static.dining.rakuten.co.jp/img/img_dining_logo.gif
				}else  
					idining.innerHTML="［楽天ダイニング］ ERROR";//alert("Error loading Dining\n");  
			}else idining.innerHTML="［楽天ダイニング］ loading...";  
		};  

		DiningXHR.send(null);
	}

	function connectLocal(){
		curl = LocalUrl+"?id="+hotelid;
		ilocal = document.getElementById('ilocal');
		ilocal.innerHTML="［楽天ローく～］ connecting..."; 
		LocalXHR.open('GET', curl, true);  
		LocalXHR.onreadystatechange = function (aEvt) {  
			if (LocalXHR.readyState == 4) {  
				if(LocalXHR.status == 200){
					if(LocalXHR.responseText!=']')
						LocalJSON = eval(LocalXHR.responseText);
					else 
						LocalJSON = JSON.parse("[]");
					//var result="";
					var baseiw = infowindow.length;
					var basemk = markers.length;
					for ( var i= 0; i < LocalJSON.length; i++) {
						var location = new google.maps.LatLng(LocalJSON[i].latitude, LocalJSON[i].longitude);
	
						var contentString = 
							//"<div align='center' class=iw_images>"+"<a href='"+LocalJSON[i].restaurantUrl+"'>"+"<img style='height:64' src='"+LocalJSON[i].restaurantImageUrl+"'/>"+"</a>"+"</div>"+ 
							"<div align='center' class=iw_names><b>"+LocalJSON[i].name+"</b></div>"+
							"<div class=iw_addresses><b>カテゴリ</b>："+LocalJSON[i].category+"</div>"+
							"<div class=iw_prs><b>平均予算：</b>"+LocalJSON[i].budget+"円</div>";
							//"<div class=iw_addresses><b>住所</b>："+LocalJSON[i].address+"</div>"+
							//"<div class=iw_prs><b>PR：</b>"+LocalJSON[i].pr+"</div>";

							if(LocalJSON[i].coupon.length>0){
								contentString+="<div class=iw_coupons>";
								for ( var x = 0; x < LocalJSON[i].coupon.length; x++) {
									contentString += "<div>";
									contentString += LocalJSON[i].coupon[x];
									contentString += "</div>"; 
								}
								contentString+="</div>";
							}
							
							//"<div class=iw_buttons align='center'><input type='button' style='background-color:lightblue' class='bt_goumetsearch' value='グルメ検索' onclick='setLatLng("+LocalJSON[i].latitude+","+LocalJSON[i].longitude+");update();'></div>";
							
						infowindow[i+baseiw] = new google.maps.InfoWindow({
						    content: contentString
						});
						
						markers[i+basemk] = new google.maps.Marker({
							  map:map,
						      position: location,
						      title:LocalJSON[i].hotelName,
						      icon:'http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=restaurant|add8e6',
						      //shadow:'http://chart.apis.google.com/chart?chst=d_map_pin_shadow'
							  shadow:new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
								      // The shadow image is larger in the horizontal dimension
								      // while the position and offset are the same as for the main image.
								      new google.maps.Size(64, 64),
								      new google.maps.Point(-20,-28),
								      null)
						  });
						//marker.setMap(map);  
						google.maps.event.addListener(markers[i+basemk], 'click', infoCallback(infowindow[i+baseiw],markers[i+basemk],LocalJSON[i].latitude,LocalJSON[i].longitude));
					}

					if(hmarker!=null){
						hmarker.setMap(map);
						ihotel = document.getElementById('ihotel');
						ihotel.innerHTML = "<img style='height:35' src='http://travel.rakuten.co.jp/share/themes/header/images/logo_travel_w89.gif'/>";
						if(LocalJSON.length>0)
							ilocal.innerHTML = "<img style='height:35' title='"+LocalJSON.length+"件' src='image/localcoupon.png'/>";
						else
							ilocal.innerHTML = "";
					}else{
						if(LocalJSON.length>0)
							ilocal.innerHTML = "<img style='height:35' title='"+LocalJSON.length+"件' src='image/localcoupon.png'/>";
						else
							ilocal.innerHTML = "";
					}
					//http://static.dining.rakuten.co.jp/img/img_dining_logo.gif
				}else  
					ilocal.innerHTML="［楽天ローく～］ ERROR";//alert("Error loading Dining\n");  
			}else ilocal.innerHTML="［楽天ローく～］ loading...";  
		};  

		LocalXHR.send(null);
	}
	
	function connectStation(){
		lat = document.getElementById('lat').value;
		lng = document.getElementById('lng').value;
		curl = StationUrl+"?lat="+escape(lat)+"&lng="+escape(lng);
		StationXHR.open('GET', curl, false);
		/*StationXHR.onreadystatechange = function (aEvt) {  
			if (StationXHR.readyState == 4) {  
				if(StationXHR.status == 200){
					var StationJSON = JSON.parse(StationXHR.responseText);
					station = StationJSON.nearestStation;
				}else  
					alert("Error loading page\n");  
			}else console.innerHTML="connecting...";  
		};  */

		StationXHR.send(null);
		StationJSON = JSON.parse(StationXHR.responseText);
		station = StationJSON.nearestStation;
	}

	function connectSingleHotel(){
		//lat = document.getElementById('lat').value;
		//lng = document.getElementById('lng').value;
		curl = "hotelidgeojson.php?id="+hotelid;
		var SHotelXHR = new XMLHttpRequest();
		SHotelXHR.open('GET', curl, false);
		/*StationXHR.onreadystatechange = function (aEvt) {  
			if (StationXHR.readyState == 4) {  
				if(StationXHR.status == 200){
					var StationJSON = JSON.parse(StationXHR.responseText);
					station = StationJSON.nearestStation;
				}else  
					alert("Error loading page\n");  
			}else console.innerHTML="connecting...";  
		};  */

		SHotelXHR.send(null);
		var SHotelJSON;

		if(SHotelXHR.responseText!=']')
			SHotelJSON = eval(SHotelXHR.responseText);
		else 
			SHotelJSON = JSON.parse("[]");
		
		hmarker = null;
		hwindow = null;

		ihotel = document.getElementById('ihotel');
		
		for ( var i= 0; i < SHotelJSON.length; i++) {
			//result += (HotelJSON[i].hotelName+"<br>");
			var location = new google.maps.LatLng(SHotelJSON[i].latitude, SHotelJSON[i].longitude);
			map.setCenter(location);

			var contentString = 
				"<div class=iw_buttons align='center'><input type='button' style='background-color:lightpink' class='bt_goumetsearch' value='グルメ検索' onclick=\"setStation('"+SHotelJSON[i].nearestStation+"');setLatLng("+SHotelJSON[i].latitude+","+SHotelJSON[i].longitude+");saveHotel("+i+",'"+SHotelJSON[i].hotelNo+"');setGourmetMode();update();\"></div>"+
				"<div align='center' class=iw_images>"+"<a href='"+SHotelJSON[i].hotelInformationUrl+"'>"+"<img style='height:64' src='"+SHotelJSON[i].hotelThumbnailUrl+"'/>"+"</a>"+"</div>"+
				"<div align='center' class=iw_names><b>"+SHotelJSON[i].hotelName+"</b></div>"+
				//"<div align='center' class=iw_names><b>最寄駅名称</b>"+HotelJSON[i].nearestStation+"</div>"+
				"<div align='center' class=iw_mincharges><b>最安料金</b>:"+SHotelJSON[i].hotelMinCharge+"円</div>";
				//"<div align='center' class=iw_addresses>"+HotelJSON[i].address+"</div>";
				
			infowindow[i] = new google.maps.InfoWindow({
			    content: contentString
			});
			
			markers[i] = new google.maps.Marker({
				  map:map,
			      position: location,
			      title:SHotelJSON[i].hotelName,
			      icon:'http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=accomm|00FF00',
			      //shadow:'http://chart.apis.google.com/chart?chst=d_map_pin_shadow'
				  shadow:new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
					      // The shadow image is larger in the horizontal dimension
					      // while the position and offset are the same as for the main image.
					      new google.maps.Size(64, 64),
					      new google.maps.Point(-20,-28),
					      null)
			  });
			//marker.setMap(map);  
			google.maps.event.addListener(markers[i], 'click', infoCallback(infowindow[i],markers[i],SHotelJSON[i].latitude,SHotelJSON[i].longitude));
		}
		
		ihotel.innerHTML = "<img style='height:35' src='http://travel.rakuten.co.jp/share/themes/header/images/logo_travel_w89.gif'/>";

		google.maps.event.addListener(map, 'click', function() {
			//setLatLng(map.getCenter().b,map.getCenter().c);
			closeInfoWindows();
		});
		
	}
	

	function initialize(ilat,ilng,imode,ihotelno) {
		lat = ilat;
		lng = ilng;
		mode = imode;

		if(ihotelno!=''){
			hotelid = ihotelno;
			//alert(hotelid);
		}else 
			hotelid=null;

	    var latlng = new google.maps.LatLng(lat, lng);
	    var myOptions = {
      		zoom: 16,
      		center: latlng,
      		mapTypeId: google.maps.MapTypeId.ROADMAP
    	};
    	map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

    	connectStation();

    	if(mode=='hotel')
    		connectHotel();
    	else if(mode='gourmet'){
        	if(hotelid==null){
	        	connectGourmet();
				connectDining();
        	}else{
				//alert("single hotel");
            	connectSingleHotel();
        		connectGourmet();
				connectDining();
				connectLocal();
            }
        }
    	updateMode();
  	}

  	function update(){
		lat = document.getElementById('lat').value;
		lng = document.getElementById('lng').value;

		 var latlng = new google.maps.LatLng(lat, lng);
		    var myOptions = {
	      		zoom: 16,
	      		center: latlng,
	      		mapTypeId: google.maps.MapTypeId.ROADMAP
	    	};
	    map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
	    if(mode=='hotel')
    		connectHotel();
    	else if(mode='gourmet'){
        	initConnect();
        	connectStation();
        	connectGourmet();
        	connectDining();
        	if(hotelid!=null)
        		connectLocal();
    	}
	}

	function setLatLng(slat,slng){
		document.getElementById('lat').value = slat;
		document.getElementById('lng').value = slng;
	}

  	function infoCallback(cinfowindow, marker,ilat,ilng) {
  		return function() {
  			closeInfoWindows();
  			cinfowindow.open(map, marker);
  			setLatLng(ilat,ilng);
  		};
  	}

  	function closeInfoWindows(){
  		for ( var j = 0; j < infowindow.length; j++) {
	  			infowindow[j].close();
		}
  	}

  	function setColorMode(ccolor){
  		document.f.style.background = ccolor;
  		document.getElementById('lower').style.background = ccolor;
  		if(ccolor == "lightblue"){
  			document.getElementById('lower').style.border="4px solid blue";
  			document.getElementById('bt_search').style.background = "blue";
  		}else if(ccolor == "lightpink"){
  			document.getElementById('lower').style.border="4px solid red";
  			document.getElementById('bt_search').style.background = "red";
  		}else if(ccolor == "lightgreen"){
  			document.getElementById('lower').style.border="4px solid green";
  			document.getElementById('bt_search').style.background = "green";
  		}
  	  	//document.getElementById('bt_test1').style.background = ccolor;
  		//document.getElementById('bt_test2').style.background = ccolor;
  		//document.getElementById('bt_test3').style.background = ccolor;
  		//document.getElementById('bt_test4').style.background = ccolor;
  		document.getElementById('ihotel').style.background = 'white';
  		document.getElementById('igourmet').style.background = 'white';
  		document.getElementById('idining').style.background = 'white';
  		document.getElementById('ilocal').style.background = 'white';
  		document.getElementById('console').style.background = 'white';
  	}

  	function setHotelMode(){
		mode='hotel';
		document.getElementById('bt_toggle').value = "グルメ検索に⇔切り替え";
		document.getElementById('bt_toggle').style.background = 'lightpink';
  	}

  	function setGourmetMode(){
		mode='gourmet';
		document.getElementById('bt_toggle').value = "ホテル検索に⇔切り替え";
		document.getElementById('bt_toggle').style.background = 'lightgreen';
		//hmarker = null;
		//hwindow = null;
		//if(hotelid!=null)
		//hotelid = null;
  	}

  	function setStation(sstation){
  	  	station = sstation;
  	  	//alert(station);
  	}

  	function updateMode(){
		if(mode=='hotel')
			setHotelMode();
		else if(mode=='gourmet')
			setGourmetMode();
		//	initialize();
  	}

  	function toggleMode(){
  		if(mode=='gourmet'){
  	  		station = null;
			setHotelMode();
			resetConsole();
			connectHotel();
		}else if(mode=='hotel'){
			setGourmetMode();
			resetConsole();
			initConnect();
			connectGourmet();
			connectDining();
		}
  		setLatLng(map.getCenter().b,map.getCenter().c);
		update();
  	}

  	function saveHotel(index,hotelNo){
  	  	if(mode=='hotel'){
	  		hmarker = markers[index];
	  		hwindow = infowindow[index];
	  		hotelid = hotelNo;
  	  	}
  	}

  	function resetConsole(){
  		document.getElementById('ihotel').innerHTML = '';
  		document.getElementById('igourmet').innerHTML = '';
  		document.getElementById('idining').innerHTML = '';
  		document.getElementById('ilocal').innerHTML = '';
  	}

  	//http://api.gnavi.co.jp/api/manual_files/l_gnavi.gif
  	//http://travel.rakuten.co.jp/share/themes/header/images/logo_travel_w89.gif
</script>
</head>
<body onload="initialize(<?php echo $lat?>,<?php echo $lng?>,'<?php echo $mode?>','<?php echo $hotelno?>');">
<!--  div id="upper" style="width: 100%; height: 10%"-->
<div id="map_canvas" style="width: 99.5%; height: 83.5%"></div>
<div id="lower" style="width: 99%; height: 15%; border:4px solid red;">
<div align='left' id='console'>
	<div style='float: left; text-align: left;' id='ihotel'></div>
	<div style='float: left; text-align: left;' id='igourmet'></div>
	<div style='float: left; text-align: left;' id='idining'></div>
	<div style='float: left; text-align: left;' id='ilocal'></div>
</div>
<div style='clear: left; text-align: left;' id='formdiv'>
<form name='f' id='f'>
	<input type='button' id='bt_search' value='現在地検索' style='background-color:red;color:white' onclick='setLatLng(map.getCenter().b,map.getCenter().c);update();'>
	<input type='button' id='bt_toggle' value='<?php echo $mode?>' onclick='hotelid=null;toggleMode();'>
	<br>
	<input type='hidden' id='lat' value=<?php echo "'".$lat."'";?>>
	<input type='hidden' id='lng' value=<?php echo "'".$lng."'";?>>
	<!-- <input type='button' id='bt_update' value='Update' onclick='update();'>
	<br>
	Test Button:<br>
	
	<input type='button' id='bt_test1' value='品川シーサイド' style='background-color:lightpink' onclick='setLatLng(35.6065914,139.7513225);update();'>
	<input type='button' id='bt_test2' value='東京駅' style='background-color:lightpink' onclick='setLatLng(35.68114,139.76732);update();'>
	<input type='button' id='bt_test3' value='お台場' style='background-color:lightpink' onclick='setLatLng(35.628849159630974,139.78121303726198);update();'>
	<input type='button' id='bt_test4' value='函館' style='background-color:lightpink' onclick='setLatLng(41.763830850657364,140.71876331680298);update();'>
	 -->
</form>
</div>
</div>
</body>
</html>
