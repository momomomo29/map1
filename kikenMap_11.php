<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="map1.css">
    <link rel="stylesheet" href="stylesheet.css" media="all">
    <title>危険対策マップ</title>
</head>

<body>
    <header>
        <h1 class="title" style="text-align:center">危険対策マップ</h1>
        <div id="navArea">
            <nav class="bauger">
                <div class="inner">
                    <ul>
                        <h2 class="setthing" style="text-align:center">詳細設定一覧</h2>
                        <h3>年月期間の選択</h3>
                        <div class="year-area">
                        <input type="radio" id="2020year" name="radio03" checked="checked">
                        <label for="2020year" class="radio03">2020年</label>
                        <br><input type="radio" id="2021year" name="radio03">
                        <label for="2021year" class="radio03">2021年</label>
                        </div>
                        <hr>
                        <details>
                            <summary style="text-align:left">街の安全性の確認</summary>
                            <div class="ziko">
                            <input type="checkbox" id="koutuujiko" name="checkbox03">
                            <label for="koutuujiko" class="checkbox03">交通事故</label>
                            <input type="checkbox" id="satujin" name="checkbox03">
                            <label for="satujin" class="checkbox03">殺人</label>
                            <input type="checkbox" id="husinsya" name="checkbox03">
                            <label for="husinsya" class="checkbox03">不審者</label>
                            <input type="checkbox" id="settou" name="checkbox03">
                            <label for="settou" class="checkbox03">窃盗</label>
                            </div>
                        </details>
                        <hr>
                        <details>
                            <summary style="text-align:left">境界線</summary>
                            <select id="ku">
                            <option >選択してください</option>
                                <option value="16">名古屋市全域</option>
                                <option value="0">千種区</option>
                                <option value="1">東区</option>
                                <option value="2">北区</option>
                                <option value="3">西区</option>
                                <option value="4">中村区</option>
                                <option value="5">中区</option>
                                <option value="6">昭和区</option>
                                <option value="7">瑞穂区</option>
                                <option value="8">熱田区</option>
                                <option value="9">中川区</option>
                                <option value="10">港区</option>
                                <option value="11">南区</option>
                                <option value="12">守山区</option>
                                <option value="13">緑区</option>
                                <option value="14">名東区</option>
                                <option value="15">天白区</option>
                            </select>
                        </details>
                        <hr>
                        </div>
                    </ul>
                </div>
            </nav>

            <div class="toggle_btn">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div id="mask"></div>
        </div>
    </header>
    <main>
        <div id="map"></div>
        <input id="pac-input" class="controls" type="text" placeholder="住所検索" />
        <script>


<?php
// DBへ接続
$mysqli = new mysqli('localhost', 'root', "", 'kiken_map');
if (mysqli_connect_error()){
    die("データベースの接続に失敗しました");
}
$latlng_array = array();
for($i = 0; $i < 383; $i++){
    array_push($latlng_array,array());
}

for($i = 0; $i < 383; $i++){
$num = $i + 3019;
$sql = "SELECT * FROM kyoukai_11 WHERE town_id = '$num' " ;
$stmt = $mysqli->query($sql);

foreach($stmt as $row){

    array_push($latlng_array[$i],array($row['lat'],$row['lng']));

}

$json_latlng = json_encode($latlng_array);
}
// 接続を閉じる
$mysqli->close();
?>

let js_array = <?php echo $json_latlng; ?>;




            function initAutocomplete() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat:35.085306,lng:    136.924480},
                    zoom: 13,
                    mapTypeId: "roadmap",
                });
                // Create the search box and link it to the UI element.
                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);

                // Bias the SearchBox results towards current map's viewport.
                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });

                let markers = [];

                // [START maps_places_searchbox_getplaces]
                // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.
                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    // Clear out the old markers.
                    markers.forEach((marker) => {
                        marker.setMap(null);
                    });
                    markers = [];

                    // For each place, get the icon, name and location.
                    const bounds = new google.maps.LatLngBounds();

                    places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            console.log("Returned place contains no geometry");
                            return;
                        }

                        const icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25),
                        };

                        // Create a marker for each place.
                        markers.push(
                            new google.maps.Marker({
                                map,
                                icon,
                                title: place.name,
                                position: place.geometry.location,
                            })
                        );
                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            
                // [END maps_places_searchbox_getplaces]
                var bermudaTriangle;
    
    //var map = new google.maps.Map
//(document.getElementById("map_canvas"),
    //    myOptions);

    var coloer_list = ["#ff0000","#ffff00","#ff7f50","#ff8c00","#f0f8ff"];
    for(let j = 0; j < 383; j++){
    
    var triangleCoords = [];

    for(let i = 0; i < js_array[j].length; i++){
        
        var lat = parseFloat(js_array[j][i][0]);
        var lng = (parseFloat(js_array[j][i][1]));
        triangleCoords.push(new google.maps.LatLng(lat,lng))
    }
    console.log(triangleCoords)

    let num = Math.floor(Math.random() * (0 + 1 - 4)) + 4;
    console.log(coloer_list[num])
    // Construct the polygon
    bermudaTriangle = new google.maps.Polygon({
      paths: triangleCoords,
      strokeColor: "#FF0000",
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: coloer_list[num],
      fillOpacity: 0.35
    });
    
    bermudaTriangle.setMap(map);
}

function inputChange(event){
    
    var town_id_sent =  event.currentTarget.value
    console.log(town_id_sent);
    window.location.href = './kikenMap_' + town_id_sent + '.php';

}

let address = document.getElementById('ku');
address.addEventListener('change', inputChange);


            }
  // [END maps_places_searchbox]
        </script>
    </main>
    <script src="map1.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
       src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAHjiLTgXzTcjlJOtzP_c09U5sLBdXI7OY&callback=initAutocomplete&libraries=places&v=weekly"
        async></script>
</body>

</html>
