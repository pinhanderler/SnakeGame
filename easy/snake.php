<!DOCTYPE html>
<html>
<?php
//function savepoint(){
if(isset($_POST['login'])){
$file = "snake.json";
$dosya = fopen($file,"a+");
$name = $_POST['name'];; //isim
$namem = strval($name);
$score = $_POST['point'];; //puan
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
		xhr.open('GET', 'snake.json', true);
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

<div><canvas id="game" width="625" height="625" style="top:0;position:fixed!important;position:relative;"></canvas></div>
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
	var stopping = true;
	var kare = 25;
	var zzehir = 0;
	var wait = true;
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
    this.positionX = this.positionY = 10;
    this.appleX = this.appleY = 5;
    this.tailSize = 5;
    this.trail = [];
    this.gridSize = this.tileCount = kare;
    this.velocityX = this.velocityY = 0;
    // Oyun döngümüz çalışmaya başlıyor.
    this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
  }
  
  reset() {
    // Oyun göngüsünü durdur:
	hiz = 10;
    clearInterval(this.timer);
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
		document.getElementById('msgbx').style.display="block";
		sortJsonObjArray();
		var score = this.trail.length - 5;
		document.getElementById('point').value = score.toString();
		document.getElementById('scorem').innerHTML = "Skorunuz : " + score.toString();
		document.getElementById('form').method = "post";
		stopping = true;
		window.scrollTo(0, 0);
        // Bastıysa game over olduk, oyunu resetle:
			// document.getElementById('msgbx').style.display="block";                           GAME OVER mesajı gelsin istiyorum!
			// document.getElementById('msgbx').style.opacity = "1"; // Opaklık efekti (açılış)
			// clearInterval(this.timer);
			// break            OYUNU DURDURMAK İÇİN
			// this.reset();
      }
    });

    // Yılanın başını yılanın herbir karesini hafızada tuttuğumuz diziye koy
    this.trail.push({positionX: this.positionX, positionY: this.positionY});

    // Yılanın başını hareket ettirdik, şimdi kuyruktan kırpmamız gerekiyor
    while (this.trail.length > this.tailSize) {
      this.trail.shift();
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
		hiz = hiz + 0.5;
	  }
	  console.log(hiz);
	  console.log(kare);
      clearInterval(this.timer);
      this.timer = setInterval(this.loop.bind(this), 1000 / hiz);
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
    this.context.fillStyle = 'white';
    this.context.font = '25px Arial';
    this.context.fillText(kare.toString() + " x " + kare.toString(), 20, 90);

    // Yılanın herbir karesini sırayla ekrana çiziyoruz
    this.context.fillStyle = 'red';
    this.trail.forEach(t => {
      this.context.fillRect(t.positionX * this.gridSize, t.positionY * this.gridSize, this.gridSize - 5, this.gridSize - 5);
    });

    // Son olarak elmayı ekrana çizdirelim:
    this.context.fillStyle = 'yellow';
    this.context.fillRect(this.appleX * this.gridSize, this.appleY * this.gridSize, this.gridSize - 5, this.gridSize - 5);
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
		console.log(wx);
    }
function start(){
    game.init();
	game.reset();
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
window.onload = start();
</script>

<!--
<div id="restart" onclick="game.reset();" style="cursor:pointer;position:relative;top:00px;border:20px solid;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;"> R E S T A R T </div>
<div id="stop" onclick="stop();" style="cursor:pointer;position:relative;top:00px;border:20px solid;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;"> S T O P </div>
<div id="continue" onclick="continuex();" style="cursor:pointer;position:relative;top:00px;border:20px solid;width:500px;margin-left:50px;margin-top:50px;padding-left:30px;"> C O N T İ N U E </div>
-->

</body>
</html>
