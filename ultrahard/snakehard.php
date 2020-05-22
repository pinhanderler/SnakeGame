<!DOCTYPE html>
<html>
<?php
//function savepoint(){
if(isset($_POST['login'])){
$file = "snakehard.json";
$dosya = fopen($file,"a+");
$name = $_POST['name'];; //isim
$namem = strval($name);
$score = $_POST['point'];; //puan
if ($score > 0){
if ($score < 10){
	$score = "0".$score; //rakam ise 0 koy
}
$my_file = fopen($file, "rw"); 
$dosyam = "";
 while (! feof ($my_file)) 
  {
	$dosyam = $dosyam.fgets($my_file);
  }
	if (stripos($dosyam, $namem) !== false) {
		echo $namem." İsmi Kullanılmıştır.";
	// header("refresh: 3;");
	}else {
		$contents = file_get_contents($file);
		$contents = str_replace( ']', ',', $contents );
		$kaydet = "\n"."\n{\"name\": \"".$name."\",\n \"age\": \"".$score."\"\n}]";
		file_put_contents($file, $contents.$kaydet );
	}
// fclose($dosya);
}
}
?>
<head>
<div id="afooter"><?php include 'footer.html';?></div>
<meta charset="utf-8" />
<title>Snake Game - Demo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link id="pagestyle" href="snakestyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var mobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()))
if(mobile){
	document.getElementById('pagestyle').setAttribute('mobile.css', sheet);
}else{
	document.getElementById('afooter').style.display="none";  
}</script>
<script type="application/javascript">
		var jsonObjectArray;
        const xhr = new XMLHttpRequest();
		xhr.open('GET', 'snakehard.json', true);
		xhr.responseType = 'json';
		xhr.send(null);
		xhr.onload = function(e) {
			console.log('response', this.response);
			jsonObjectArray = this.response;
		};
 
        function compare(a,b) {
            if (a.name < b.name)
                return -1;
            if (a.name > b.name)
                return 1;
            return 0;
        }
 
        function compareAge(a,b) {
            if (a.age < b.age)
                return 1;
            if (a.age > b.age)
                return -1;
            return 0;
        }
 
        function sortJsonObjArray() {
            var tmpJsonObjectArray = jsonObjectArray;
            //first sort by name
            tmpJsonObjectArray.sort(compare);
            //first sort by age
            tmpJsonObjectArray.sort(compareAge);
 
            var str = "";
			document.getElementById("leaderboard").innerHTML = "";
            for(var i=0; i<tmpJsonObjectArray.length; i++) {
                // str += tmpJsonObjectArray[i].name + " - " + tmpJsonObjectArray[i].age + "\n";
				str = tmpJsonObjectArray[i].name + " - " + tmpJsonObjectArray[i].age;
				var ul = document.getElementById("leaderboard");
				var li = document.createElement("li");
				// li.setAttribute('id',str);
				li.appendChild(document.createTextNode(str));
				ul.appendChild(li);
            }
        }
    </script>
</head>
<body style="overflow-x:hidden;overscroll-behavior:none;" class="body">

<div stlye="position:fixed;"><canvas id="game" width="625" height="625" style="top:0;position:fixed!important;position:relative;"></canvas></div>
<div id="welcome" onclick="welcome();" style="top:0;position:relative;border:10px solid white;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;background-color:black;color:white;">
<font size="6"><center><br/>başlamak için tıkla<br/>
<h2 style="color:yellow;">Sarıları Topla</h2>
Beyazlar zehirdir, kanınızı kaynatır (hız++) Gri yılan da zehirlidir. 5 Defa zehirlenirseniz ölürsünüz.
<a style="color:#00ff00;">Yeşiller Panzehirdir.</a><br/><br/>
<a style="color:orange;" onclick="gameover();">Lider Tablosunu Görüntüle</a><br/><br/></center></font>
</div>
<div id="msgbx" style="display:none;top:0;position:relative;border:10px solid white;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;background-color:black;color:white;">
<center><font size="6"><h1 onclick="game.reset();" style="color:gray;">G A M E . O V E R </h1>
<a onclick="game.reset();" style="color:yellow;"><u>Click for Restrart!</u></a><br/>
<hr/><h1>- Liderlik Tablosu -</h1>
<ol id="leaderboard" style="font: 50px Arial;left:10px;"></ol>
<form id="form" method="post" action="">
<input id="name" name="name" value="Adınız" style="font:50px Arial;"/>
<input id="point" name="point" value="17" style="display:none;"/>
<h1 id="scorem"></h1>
<p><input type="submit" name="login" value="KAYDET" style="font:50px Arial;"/></p>
</form>
</font>
</center>
</div>
<script> 
	var hiz = 10;
	var stopping = false;
	var kare = 25;
	var zzehir = 0;
	var messagex = "";
	var colorr = "red";
	var wait = true;
	var zehirlenme = true;
	if(window.innerWidth >= window.innerHeight){
		var kare = Math.floor(Math.sqrt(window.innerHeight - 20));
	}
	if(window.innerWidth < window.innerHeight){
		var kare = Math.floor(Math.sqrt(window.innerWidth));
	}
class SnakeGame {
  constructor() {
    this.canvas = document.getElementById('game');
    this.context = this.canvas.getContext('2d');
    
    // Basılan tuşları algılıyoruz:
    document.addEventListener('keydown', this.onKeyPress.bind(this));
  }
  init() {
    // Yeni oyun için ilk değer atamaları:
	var c = Math.floor(Math.random() * 3);
	if(c == 0){ colorr = "red"}
	if(c == 1){ colorr = "blue"}
	if(c == 2){ colorr = "green"}
    this.positionX = this.positionY = 10;
    this.position2X = this.position2Y = 15;
    this.appleX = this.appleY = 5;
    this.zehirX = this.zehirY = 3;
    this.zehir2X = this.zehir2Y = -1;
    this.zehir3X = this.zehir3Y = -1;
    this.zehir4X = this.zehir4Y = -1;
    this.pzehirX = this.pzehirY = -1;
    this.tailSize = 5;
    this.trail = [];
    this.trail2 = [];
    this.gridSize = this.tileCount = kare;
    this.velocityX = this.velocityY = 0;
    this.velocity2X = this.velocity2Y = 0;
    // Oyun döngümüz çalışmaya başlıyor.
    // Her saniyede 15 kez çalışacak, yani 15 FPS olacak.
    // Üç boyutlu çok daha büyük oyunlar genelde 60 FPS üzerinde çalışıyor.
    this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
    clearInterval(this.timer2);
    this.timer2 = setInterval(this.update2.bind(this), 1000 / 15);
	window.setTimeout(enemy, 3000);
  }
  
  reset() {
    // Oyun göngüsünü durdur:
	hiz = 10;
	zzehir = 0;
	messagex = "";
    clearInterval(this.timer);
    clearInterval(this.timer2);
    // Oyun ile alakalı detayları en baştaki haline geri döndür:
    this.init();
	stopping = false;
	// document.getElementById('msgbx').style.display="none";
	document.getElementById('msgbx').style.display="none";   
  }

  // Oyun döngümüz
  loop() {
    // Matematiksel hesaplamaları yap:
    this.update();
    
    // Sonrasında ekrana çizimi gerçekleştir
    this.draw();
	
	// Zehirli Yılanı Yönlendir
  }
  
  update2() {
    if(stopping) return;
    this.position2X += this.velocity2X;
    this.position2Y += this.velocity2Y;
	
	if (this.position2X < 0) {
      this.position2X = this.tileCount - 1;
    } else if (this.position2Y < 0) {
      this.position2Y = this.tileCount - 1;
    } else if (this.position2X > this.tileCount - 1) {
      this.position2X = 0;
    } else if (this.position2Y > this.tileCount - 1) {
      this.position2Y = 0;
    }
  }
  update() {
    if(stopping) return;
    // Yılanın başını X ve Y koordinat düzleminde gittiği yöne hareket ettir
    this.positionX += this.velocityX;
    this.positionY += this.velocityY;

    // Yılan sağ, sol, üst ya da alt kenarlara değdi mi?
    // Değdiyse ekranın diğer tarafından devam ettir
    if (this.positionX < 0) {
      this.positionX = this.tileCount - 1;
    } else if (this.positionY < 0) {
      this.positionY = this.tileCount - 1;
    } else if (this.positionX > this.tileCount - 1) {
      this.positionX = 0;
    } else if (this.positionY > this.tileCount - 1) {
      this.positionY = 0;
    }

    // Yılan kendi üstüne bastı mı?
    this.trail.forEach(t => {
      if (this.trail.length > 5 && this.positionX === t.positionX && this.positionY === t.positionY) {
		gameover();
        // Bastıysa game over olduk, oyunu resetle:
			// document.getElementById('msgbx').style.display="block";                           GAME OVER mesajı gelsin istiyorum!
			// document.getElementById('msgbx').style.opacity = "1"; // Opaklık efekti (açılış)
			// clearInterval(this.timer);
			// break            OYUNU DURDURMAK İÇİN
			// this.reset();
	  }
    });
	this.trail2.forEach(t2 => {
		if (this.trail.length > 5 && this.positionX === t2.positionX && this.positionY === t2.positionY) {
		if(zehirlenme == true){
		zzehir = zzehir + 1;
		messagex = "Yılandan Zehirlendiniz (" + zzehir.toString() + ")";
		console.log('zehir :' + zzehir.toString());
		if (zzehir >= 5){
			gameover();
		}
		hiz = hiz + 3;
		// this.tailSize--;
		clearInterval(this.timer);
		this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
		zehirlenme = false;
		setTimeout(zehiracik,1000);
		}
	  }
	});

    // Yılanın başını yılanın herbir karesini hafızada tuttuğumuz diziye koy
    this.trail.push({positionX: this.positionX, positionY: this.positionY});
    this.trail2.push({positionX: this.position2X, positionY: this.position2Y});

    // Yılanın başını hareket ettirdik, şimdi kuyruktan kırpmamız gerekiyor
    while (this.trail.length > this.tailSize) {
      this.trail.shift();
    }
	while (this.trail2.length > 6) {
      this.trail2.shift();
    }

    // Yılan elma yedi mi?
    if (this.appleX === this.positionX && this.appleY === this.positionY) {
      // Yediyse yılanın boyutu uzat:
      this.tailSize++;
      
      // Ekrana yeni bir elma koymak lazım.
      // Rasgele X ve Y koordinatı üretip oraya elmayı atalım:
      this.appleX = Math.floor(Math.random() * this.tileCount);
      this.appleY = Math.floor(Math.random() * this.tileCount);
      if(hiz < 20 ){
		hiz = hiz + 1;
	  }else {
	    if(hiz < 35){
			hiz = hiz + 0.25;
		}
	  }
	if(hiz > 21 && this.zehir2X == -1){
		this.zehir2X = this.zehir2Y = 4;
	}
	if(hiz > 22 && this.pzehirX == -1){
      this.pzehirX = Math.floor(Math.random() * this.tileCount);
      this.pzehirY = Math.floor(Math.random() * this.tileCount);
	}else{
		this.pzehirX = this.pzehirY = -1;
	}
	if(hiz > 25 && this.zehir3X == -1){
		this.zehir3X = this.zehir3Y = 6;
	}
	if(hiz > 30 && this.zehir4X == -1){
		this.zehir4X = this.zehir4Y = 7;
	}
	  console.log(hiz);
      clearInterval(this.timer);
      this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
    }
	
	// Yılan Panzehir Yedi mi?
	if (this.pzehirX === this.positionX && this.pzehirY === this.positionY) {
		if(zzehir > 0){ 
		zzehir = zzehir - 1;
		messagex = "Panzehir aldınız";
		this.randomly();
		hiz = hiz - 3;
		clearInterval(this.timer);
		this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
		}else{
		messagex = "Panzehire ihtiyacınız yok";
		}
    }
	
	// Yılan Zehir yedi mi?
	if (this.zehirX === this.positionX && this.zehirY === this.positionY || this.zehir2X === this.positionX && this.zehir2Y === this.positionY || this.zehir3X === this.positionX && this.zehir3Y === this.positionY || this.zehir4X === this.positionX && this.zehir4Y === this.positionY) {
		// Yediyse ölürdocument.getElementById('msgbx').style.display="block";
		if(zehirlenme == true){
		zzehir = zzehir + 1;
		messagex = zzehir.toString() + " defa zehirlendiniz";
		if(zzehir >= 5){
			gameover();
		}
		this.randomly();
		if(hiz < 20){
			hiz = hiz + 4;
		}else {
			hiz = hiz + 2;
		}
		// this.tailSize++;
		clearInterval(this.timer);
		this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
		zehirlenme = false;
		setTimeout(zehiracik,1000);
		}
    }
  }
  
  randomly() {
		this.zehirX = Math.floor(Math.random() * this.tileCount);
		this.zehirY = Math.floor(Math.random() * this.tileCount);
		if(hiz > 21){
		this.zehir2X = Math.floor(Math.random() * this.tileCount);
		this.zehir2Y = Math.floor(Math.random() * this.tileCount);
		}
		if(hiz > 22){
		this.pzehirX = Math.floor(Math.random() * this.tileCount);
		this.pzehirY = Math.floor(Math.random() * this.tileCount);
		}
		if(hiz > 25){
		this.zehir3X = Math.floor(Math.random() * this.tileCount);
		this.zehir3Y = Math.floor(Math.random() * this.tileCount);
		}
		if(hiz > 30){
		this.zehir4X = Math.floor(Math.random() * this.tileCount);
		this.zehir4Y = Math.floor(Math.random() * this.tileCount);
		}
  }

  // Ekrana çizim gerçekleştiriyor:
  draw() {
    // İlk olarak siyah arkaplanı çiziyoruz
    this.context.fillStyle = 'black';
    this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);

    // Skoru ekranın sol üst köşesine yazdıralım
    this.context.fillStyle = 'white';
    this.context.font = '50px Arial';
    this.context.fillText(this.tailSize - 5, 20, 50);
	
	// Çözünürlüğü ekranın sol üst köşesine yazdıralım
    this.context.fillStyle = 'gray';
    this.context.font = '25px Arial';
    this.context.fillText(kare.toString() + " x " + kare.toString(), 20, 90);
	
	// Durumun ekranın sol üst köşesine yazdıralım
    this.context.fillStyle = 'white';
    this.context.font = '40px Arial';
    this.context.fillText(messagex, 20, 130);

    // Yılanın herbir karesini sırayla ekrana çiziyoruz
    this.context.fillStyle = colorr;
    this.trail.forEach(t => {
      this.context.fillRect(t.positionX * this.gridSize, t.positionY * this.gridSize, this.gridSize - 5, this.gridSize - 5);
    });

    // Son olarak elmayı ekrana çizdirelim:
    this.context.fillStyle = 'yellow';
    this.context.fillRect(this.appleX * this.gridSize, this.appleY * this.gridSize, this.gridSize - 5, this.gridSize - 5);

    // Panzehir
    this.context.fillStyle = '#00ff00';
    this.context.fillRect(this.pzehirX * this.gridSize, this.pzehirY * this.gridSize, this.gridSize - 5, this.gridSize - 5);
	
	// Zehir
	this.context.fillStyle = 'white';
    this.context.fillRect(this.zehirX * this.gridSize, this.zehirY * this.gridSize, this.gridSize - 5, this.gridSize - 5);
	
	// Zehir2
	this.context.fillStyle = 'white';
    this.context.fillRect(this.zehir2X * this.gridSize, this.zehir2Y * this.gridSize, this.gridSize - 5, this.gridSize - 5);
	
	// Zehir3
	this.context.fillStyle = 'white';
    this.context.fillRect(this.zehir3X * this.gridSize, this.zehir3Y * this.gridSize, this.gridSize - 5, this.gridSize - 5);
	
	// Zehir4
	this.context.fillStyle = 'white';
    this.context.fillRect(this.zehir4X * this.gridSize, this.zehir4Y * this.gridSize, this.gridSize - 5, this.gridSize - 5);
	
    // Yılanın herbir karesini sırayla ekrana çiziyoruz
    this.context.fillStyle = 'rgb(128, 128, 128, 0.5)';
    this.trail2.forEach(t2 => {
    this.context.fillRect(t2.positionX * this.gridSize, t2.positionY * this.gridSize, this.gridSize - 5, this.gridSize - 5);
    });
  }

  // Kullanıcı websayfasındayken bir tuşa bastığında çağrılıyor:
  onKeyPress(e) {
    // Kullanıcı sol oka bastı, yılan sağa gitmiyorsa yılanı sola döndür
    if (e.keyCode === 37 && this.velocityX !== 1 && wait == true) {
      this.velocityX = -1;
      this.velocityY = 0;
	  window.scrollTo(0, 0);
	  wait = false;
	  setTimeout(vait,20);
    }
    
    // Kullanıcı yukarı oka bastı, yılan aşağı gitmiyorsa yılanı yukarı döndür
    else if (e.keyCode === 38 && this.velocityY !== 1 && wait == true) {
      this.velocityX = 0;
      this.velocityY = -1;
	  window.scrollTo(0, 0);
	  wait = false;
	  setTimeout(vait,20);
    }
    
    // Kullanıcı sağ oka bastı, yılan sola gitmiyorsa yılanı sağa döndür
    else if (e.keyCode === 39 && this.velocityX !== -1 && wait == true) {
      this.velocityX = 1;
      this.velocityY = 0;
	  window.scrollTo(0, 0);
	  wait = false;
	  setTimeout(vait,20);
    }
    
    // Kullanıcı aşağı oka bastı, yılan yukarı gitmiyorsa yılanı aşağı döndür
    if (e.keyCode === 40 && this.velocityY !== -1 && wait == true) {
      this.velocityX = 0;
      this.velocityY = 1;
	  window.scrollTo(0, 0);
	  wait = false;
	  setTimeout(vait,20);
    }
  }
}
// Yeni oyun oluştur:
const game = new SnakeGame();
// Sayfa yüklendiğinde oyunu oynanabilir hale getir:  
function swipe() {
	var pozx;
	var pozx1;
	var pozy;
	var pozy1;
	var kontrolx;
	var kontroly;
	var mydiv=document.querySelector("#touchsurface");
	window.addEventListener("touchstart", function(e) {
		e=e || windows.evet;
		pozx= e.changedTouches[0].pageX;
		pozy= e.changedTouches[0].pageY;
	})
	window.addEventListener("touchend", function(e) {
		e=e || windows.evet;
		pozx1= e.changedTouches[0].pageX;
		pozy1= e.changedTouches[0].pageY;
		kontrolx = pozx1-pozx;
		kontroly = pozy1-pozy;
		if (Math.abs(kontrolx) > Math.abs(kontroly)){
			if (kontrolx>10  && game.velocityX !== -1) { //39
				console.log("right swipe");
				game.velocityX = 1;
				game.velocityY = 0;
			}
			if (kontrolx<-10  && game.velocityX !== 1) { //37
				console.log("left swipe");
				game.velocityX = -1;
				game.velocityY = 0;
			}
		} else {
			if (kontroly>10 && game.velocityY !== -1) { //40
				console.log("down swipe");
				game.velocityX = 0;
				game.velocityY = 1;
			}
			if (kontroly<-10 && game.velocityY !== 1) { //38
				console.log("up swipe");
				game.velocityX = 0;
				game.velocityY = -1;
			}
		}
	})
}
// $("#msgbx").on("click",function(){
// $("#msgbx").animate({opacity: 'hide'},1000); }
function maxWindow() {
        window.moveTo(0, 0);
		var wx = kare * kare;
		document.getElementById('game').width = wx;
		document.getElementById('game').height = wx;
		var wx2 = wx - 150;
		document.getElementById('msgbx').style.width = wx2 + "px";
		document.getElementById('welcome').style.width = wx2 + "px";
		console.log(wx);
    }
function start(){
    game.init();
	swipe();
	maxWindow();
}
function stop(){
    stopping = true;
}
function continuex(){
    stopping = false;
}
function vait(){
	wait = true;
}
function zehiracik(){
	zehirlenme = true;
}
function welcome(){
	document.getElementById('welcome').style.display="none";
}
function enemy(){
	var dx = Math.floor((Math.random() * 3) - 1);
	var dy = Math.floor((Math.random() * 3) - 1);
	game.velocity2X = dx;
	game.velocity2Y = dy;
	window.setTimeout(enemy, 3000);
  }
function gameover(){
	// document.getElementById('welcome').style.display="none";
	document.getElementById('msgbx').style.display="block";
	sortJsonObjArray();
	var score = game.trail.length - 5;
	document.getElementById('point').value = score.toString();
	document.getElementById('scorem').innerHTML = "Skorunuz : " + score.toString();
	document.getElementById('form').method = "post";
	stopping = true;
	console.log("stop");
	window.scrollTo(0, 0);
	clearTimeout(enemy);
}
window.onload = start();
</script>

<!-- 
<div id="restart" onclick="game.reset();" style="cursor:pointer;position:relative;top:00px;border:20px solid;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;"> R E S T A R T </div>
<div id="stop" onclick="stop();" style="cursor:pointer;position:relative;top:00px;border:20px solid;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;"> S T O P </div>
<div id="continue" onclick="continuex();" style="cursor:pointer;position:relative;top:00px;border:20px solid;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;"> C O N T İ N U E </div>
-->

</body>
</html>
