<!DOCTYPE html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--    <link rel="stylesheet" href="style.css"> -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title>Title</title>
    <style>
        .sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: #f1f1f1;
        }

        .sidebar a {
            font-size: 18px;
        }

        }
    </style>
</head>
<body>
<section class="px-3" style="padding-top: 70px;">
    <div class="container py-3 shadow" style="border-radius: 15px;">
        <div class="row" id="tabBar">
            <div class="col">
                <div class="nav justify-content-center">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button class="nav-link" onclick=openTabContent('InfoContent','Info') id="Info">店鋪地址
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" onclick=openTabContent('ReservationContent','Reservation')
                                    id="Reservation">預約
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" onclick=openTabContent('PromoContent','Promo') id="Promo">推廣
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" onclick=openTabContent('MenuContent','Menu') id="Menu">菜單</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link"
                                    onclick="openTabContent('TransInstructionContent','TransInstruction')"
                                    id="TransInstruction">
                                交通指引
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-6">
                請上傳菜式圖片
                <input class="form-control" type="file" id="formFile">
            </div>
        </div>
</section>
<script>
   let shopLat = 0;
   let shopLng = 0;

   function id(elementIDName)
   {
      return document.getElementById(elementIDName);
   }

   function initAutocomplete()
   {
      const myLatlng = {lat: 22.3193039, lng: 114.1693611};
      const map = new google.maps.Map(document.getElementById("map"), {
         center: myLatlng,
         zoom: 11,
         clickableIcons: false,
         mapTypeId: "roadmap",
      });

      // Create the initial InfoWindow.
      let infoWindow = new google.maps.InfoWindow();
      // Configure the click listener.
      map.addListener("click", (mapsMouseEvent) =>
      {
         document.getElementById('clickMapLocationStatus').setAttribute('style', 'color:green');
         document.getElementById('clickMapLocationStatus').innerText = '(你已經點擊了)';
         shopLat = mapsMouseEvent.latLng.lat();
         shopLng = mapsMouseEvent.latLng.lng();
         console.log(shopLat, shopLng);
         infoWindow.close();
         // Create a new InfoWindow.
         infoWindow = new google.maps.InfoWindow({
            position: mapsMouseEvent.latLng,
         });
         infoWindow.setContent(
            "店鋪在這裡"
         );
         infoWindow.open(map);
      });

      // Create the search box and link it to the UI element.
      const input = document.getElementById("pac-input");
      const searchBox = new google.maps.places.SearchBox(input);

      //呢句會導致搜索欄 永遠在google地圖上
      /*map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);*/

      // Bias the SearchBox results towards current map's viewport.
      map.addListener("bounds_changed", () =>
      {
         searchBox.setBounds(map.getBounds());
      });

      let markers = [];

      // Listen for the event fired when the user selects a prediction and retrieve
      // more details for that place.
      searchBox.addListener("places_changed", () =>
      {
         const places = searchBox.getPlaces();
         console.log(places[0]);
         //獲取第一個結果的坐標
         //console.log(places[0].geometry.location.lat());

         if (places.length == 0)
         {
            return;
         }

         // Clear out the old markers.
         markers.forEach((marker) =>
         {
            marker.setMap(null);
         });
         markers = [];

         // For each place, get the icon, name and location.
         const bounds = new google.maps.LatLngBounds();

         places.forEach((place) =>
         {
            if (!place.geometry || !place.geometry.location)
            {
               console.log("Returned place contains no geometry");
               return;
            }

            const icon = {
               url: "Available/google_map_location_icon.png",
               size: new google.maps.Size(71, 71),
               origin: new google.maps.Point(0, 0),
               anchor: new google.maps.Point(17, 34),
               scaledSize: new google.maps.Size(60, 60),
            };

            // Create a marker for each place.
            markers.push(
               new google.maps.Marker({
                  map,
                  icon,
                  //地圖上的marker不能被點擊
                  clickable: false,
                  //marker放在的經緯度
                  position: place.geometry.location,
               })
            );
            if (place.geometry.viewport)
            {
               // Only geocodes have viewport.
               bounds.union(place.geometry.viewport);
            }
            else
            {
               bounds.extend(place.geometry.location);
            }
         });
         map.fitBounds(places[0].geometry.viewport);
      });
   }

   //默認打開店鋪地址為主頁
   let theOldTabName = "";
   openTabContent('MenuContent', 'Menu');

   function openTabContent(TabName, IDName)
   {
      //設置tab 的灰色邊框在誰的頭上
      let navBtns = document.getElementsByClassName('nav-link');
      for (let i = 0; i < navBtns.length; i++)
      {
         if (navBtns[i].id != IDName)
         {
            navBtns[i].className = 'nav-link';
         }
         else
         {
            navBtns[i].className = 'nav-link active';
         }
      }

      //如果其他tabs的內容曾經渲染了, 把他們隱藏
      let tabContentClasses = document.getElementsByClassName('tabContents');
      for (let i = 0; i < tabContentClasses.length; i++)
      {
         if (tabContentClasses[i].id != TabName)
         {
            tabContentClasses[i].setAttribute('style', 'display:none')
         }
      }

      //bk 店鋪地址內容
      if (TabName == 'InfoContent')
      {
         if (theOldTabName != TabName)
         {
            theOldTabName = TabName;

            if (!id('InfoContent'))
            {
               let addContent =
                  `
<div id="InfoContent" class="tabContents">
            <div class="row pt-3 " id="infoGoogleMap">
                <div class="col-9">
                    <div class="form-floating">
                        <input class="form-control" id="pac-input" placeholder="請輸入店鋪名字以定位店鋪在谷歌地圖上的位置">
                        <label id="coordinateLabel">請輸入店鋪名字以定位店鋪在谷歌地圖上的位置</label>
                    </div>
                </div>
                <div class="col d-flex align-items-center justify-content-end">
<button class="btn btn-warning" id="cannotFindMyShop">找不到我的店鋪</button>
                </div>
            </div>

            <div class="row py-2">
                <div class="col">
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-8">
<span>請放大地圖的比例及點擊店鋪的位置以精準定位</span> <span id="clickMapLocationStatus" style="color: red">(你還沒點擊)</span>
                </div>
                <div class="col d-flex align-items-center justify-content-end">
<button class="btn btn-primary me-2" id="cannotFindMyShop">例子</button>
<button class="btn btn-warning" id="cannotFindMyShop">找不到我的店鋪</button>

                </div>
            </div>


            <div class="row py-3" id="infoCountry">
                <div class="col">
                    <h5>請選擇店鋪位於的國家:</h5>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" id="countryText">
                            請選擇國家
                        </button>
                        <ul class="dropdown-menu text-center">
                            <li><a class="dropdown-item countryOptions" id="C1">中國</a></li>
                            <li><a class="dropdown-item countryOptions" id="C2">美國</a></li>
                            <li><a class="dropdown-item countryOptions" id="C3">英國</a></li>
                        </ul>
                    </div>
                </div>
            </div>
</div>
                `;
               document.getElementById('tabBar').insertAdjacentHTML('afterend', addContent);
               initAutocomplete();
            }
            else if (id('InfoContent') && id('InfoContent').style.display == '')
            {
               id('InfoContent').setAttribute('style', 'display:none');
            }
            else if (id('InfoContent') && id('InfoContent').style.display == 'none')
            {
               id('InfoContent').setAttribute('style', '');
            }

         }

         //店鋪地址的控制內容
         let countryBtns = document.getElementsByClassName('countryOptions');
         for (let i = 0; i < countryBtns.length; i++)
         {
            //選擇任何一個國家後
            countryBtns[i].addEventListener('click', function ()
            {
               //顯示已選擇的國家名字
               document.getElementById('countryText').innerHTML = countryBtns[i].innerHTML;

               //根據不同的國家顯示不同城市的名字
               let cityOptionsName = '';
               if (countryBtns[i].id == 'C1')
               {
                  cityOptionsName =
                     `
                <li><a class="dropdown-item cityOptions" id="C1C1">香港</a></li>
                <li><a class="dropdown-item cityOptions" id="C1C2">上海</a></li>
                <li><a class="dropdown-item cityOptions" id="C1C3">北京</a></li>
                <li><a class="dropdown-item cityOptions" id="C1C4">深圳</a></li>

                `;
               }
               if (countryBtns[i].id == 'C2')
               {
                  cityOptionsName =
                     `
                <li><a class="dropdown-item cityOptions" id="C2C1">紐約</a></li>
                <li><a class="dropdown-item cityOptions" id="C2C2">洛杉磯</a></li>
                <li><a class="dropdown-item cityOptions" id="C2C3">芝加哥</a></li>
                `;
               }
               if (countryBtns[i].id == 'C3')
               {
                  cityOptionsName =
                     `
                <li><a class="dropdown-item cityOptions" id="C3C1">倫敦</a></li>
                <li><a class="dropdown-item cityOptions" id="C3C2">伯明罕</a></li>
                <li><a class="dropdown-item cityOptions" id="C3C3">格拉斯哥</a></li>
                `;
               }

               //然後顯示城市選項
               let addHTMLContent =
                  `
            <div class="row py-3" id="infoCity">
                <div class="col">
                    <h5>請選擇店鋪位於的城市:</h5>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" id="cityText">
                            請選擇城市
                        </button>
                        <ul class="dropdown-menu text-center">
                            ${cityOptionsName}
                        </ul>
                    </div>
                </div>
            </div>
            `;
               //因為下面添加城市內容, 所以要刪除舊的城市內容
               if ($("#infoCity"))
               {
                  $("#infoCity").remove();
               }
               if ($("#infoRegion"))
               {
                  $("#infoRegion").remove();
               }
               if ($("#infoDistrict"))
               {
                  $("#infoDistrict").remove();
               }
               if ($("#infoStreet"))
               {
                  $("#infoStreet").remove();
               }

               //顯示完整的選擇城市內容
               document.getElementById('infoCountry').insertAdjacentHTML('afterend', addHTMLContent);

               //城市選項listener
               let cityOptions = document.getElementsByClassName('cityOptions');
               for (let i = 0; i < cityOptions.length; i++)
               {
                  //點擊城市名後, 顯示城市名, 再顯示地區內容
                  cityOptions[i].addEventListener('click', function ()
                  {
                     //點擊城市名後, 顯示城市名
                     document.getElementById('cityText').innerHTML = cityOptions[i].innerHTML;

                     //選擇完城市名, 顯示地區內容給用戶選擇
                     let regionOptions = "";
                     if (cityOptions[i].id == 'C1C1')
                     {
                        regionOptions = `
                            <li><a class="dropdown-item regionOptions" id="C1C1R1">九龍</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C1R2">新界</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C1R3">香港島</a></li>

                        `;
                     }
                     if (cityOptions[i].id == 'C1C4')
                     {
                        regionOptions = `
                            <li><a class="dropdown-item regionOptions" id="C1C4R1">福田</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R2">羅湖</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R3">南山</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R4">鹽田</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R5">寶安</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R6">龍崗</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R7">龍華</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R8">坪山</a></li>
                            <li><a class="dropdown-item regionOptions" id="C1C4R9">光明</a></li>
                        `;
                     }
                     let addRegionContent =
                        `
            <div class="row py-3" id="infoRegion">
                <div class="col">
                    <h5>請選擇店鋪位於的地區:</h5>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" id="regionText">
                            請選擇地區
                        </button>
                        <ul class="dropdown-menu text-center">
                            ${regionOptions}
                        </ul>
                    </div>
                </div>
            </div>
                    `;

                     //如果有的話,刪除上個地區內容,以免重複顯示
                     if ($("#infoRegion"))
                     {
                        $("#infoRegion").remove();
                     }
                     if ($("#infoDistrict"))
                     {
                        $("#infoDistrict").remove();
                     }
                     if ($("#infoStreet"))
                     {
                        $("#infoStreet").remove();
                     }
                     document.getElementById('infoCity').insertAdjacentHTML('afterend', addRegionContent);

                     //地區選項listener
                     let regionOptionsBtns = document.getElementsByClassName('regionOptions');
                     for (let i = 0; i < regionOptionsBtns.length; i++)
                     {
                        regionOptionsBtns[i].addEventListener('click', function ()
                        {
                           document.getElementById('regionText').innerText = regionOptionsBtns[i].innerHTML;

                           let addInfoDistrictBody = "";
                           if (regionOptionsBtns[i].id == "C1C1R1")
                           {
                              addInfoDistrictBody = `
                            <li><a class="dropdown-item districtOptions" id="C1C1R1D1">九龍城區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R1D2">觀塘區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R1D3">深水埗區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R1D4">黃大仙區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R1D5">油尖旺區</a></li>
                                    `;
                           }
                           if (regionOptionsBtns[i].id == "C1C1R2")
                           {
                              addInfoDistrictBody = `
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D1">離島區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D2">葵青區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D3">北區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D4">西貢區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D5">沙田區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D6">大埔區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D7">荃灣區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D8">屯門區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R2D9">元朗區</a></li>
                                    `;
                           }
                           if (regionOptionsBtns[i].id == "C1C1R3")
                           {
                              addInfoDistrictBody = `
                            <li><a class="dropdown-item districtOptions" id="C1C1R3D1">中西區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R3D2">東區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R3D3">西區</a></li>
                            <li><a class="dropdown-item districtOptions" id="C1C1R3D4">灣仔區</a></li>
                                    `;
                           }
                           //顯示小區內容
                           let addInfoDistrict = `
            <div class="row py-3" id="infoDistrict">
                <div class="col">
                    <h5>請選擇店鋪位於的區域:</h5>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" id="districtText">
                            請選擇區域
                        </button>
                        <ul class="dropdown-menu text-center">
                            ${addInfoDistrictBody}
                        </ul>
                    </div>
                </div>
            </div>
                                `;
                           //如有的話, 刪除上次的舊內容
                           if ($("#infoDistrict"))
                           {
                              $("#infoDistrict").remove();
                           }
                           if ($("#infoStreet"))
                           {
                              $("#infoStreet").remove();
                           }
                           //添加info district
                           document.getElementById('infoRegion').insertAdjacentHTML("afterend", addInfoDistrict);

                           //
                           let districtOptionsBtns = document.getElementsByClassName("districtOptions");
                           for (let i = 0; i < districtOptionsBtns.length; i++)
                           {
                              districtOptionsBtns[i].addEventListener('click', function ()
                              {
                                 document.getElementById("districtText").innerText = districtOptionsBtns[i].innerHTML;
                                 let addStreetInfo = `
            <div id="infoStreet">
                <div class="row py-3 text-center">
                    <div class="col">
                        <div class="form-floating">
                            <input class="form-control" id="streetName" placeholder="請輸入店鋪位於的街道名稱">
                            <label id="streetNameLabel">(必填) 請輸入店鋪位於的街道名稱</label>
                        </div>
                    </div>
                </div>
                <div class="row py-3">
                    <div class="col">
                        <div class="form-floating">
                            <input class="form-control" id="buildingName" placeholder="(如有) 請輸入店鋪位於的大廈、商場或建築物名稱">
                            <label>(如有) 請輸入店鋪位於的大廈、商場或建築物名稱</label>
                        </div>
                    </div>
                </div>
                <div class="row py-3 text-center">
                    <div class="col">
                        <button class="btn btn-primary" id="saveAddressBtn">保存以上所有地址信息</button>
                    </div>
                </div>
            </div>
                                    `;

                                 //如有, 刪除上次顯示的
                                 if ($("#infoStreet"))
                                 {
                                    $("#infoStreet").remove();
                                 }
                                 document.getElementById("infoDistrict").insertAdjacentHTML('afterend', addStreetInfo);
                                 document.getElementById("saveAddressBtn").addEventListener('click', function ()
                                 {
                                    //沒用輸入街道名稱
                                    if (document.getElementById("streetName").value == "")
                                    {
                                       document.getElementById("streetNameLabel").setAttribute('style', 'color:red;');
                                       document.getElementById("streetName").addEventListener('input', function ()
                                       {
                                          document.getElementById("streetNameLabel").setAttribute('style', 'color:black');
                                       });
                                    }
                                    if (document.getElementById("pac-input").value == "")
                                    {
                                       document.getElementById("coordinateLabel").setAttribute('style', 'color:red');
                                       document.getElementById("pac-input").addEventListener('input', function ()
                                       {
                                          document.getElementById("coordinateLabel").setAttribute('style', 'color:black');
                                       });
                                    }

                                    //彈出確認信息的modal
                                    if (document.getElementById("streetName").value != "" && document.getElementById("pac-input").value != "")
                                    {
                                       let modalContent = `
 <!-- Modal -->
<div class="modal fade" id="confirmModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
                                            `;
                                       document.getElementById('infoStreet').insertAdjacentHTML('afterend', modalContent);
                                       $("#confirmModal").modal('show');
                                    }
                                 });

                              });
                           }
                        });// end region btn listener
                     }// end for
                  });
               }
            });//點擊選擇國家listener
         }
      }

      if (TabName == 'ReservationContent')
      {
         //上次點擊和這次點擊不一樣
         if (theOldTabName != TabName)
         {
            theOldTabName = TabName;

            if (!id('ReservationContent'))
            {
               let addContent = `
<div id="ReservationContent" class="tabContents">
                    <h3>Reservation</h3>
                    <p>Paris is the capital of France.</p>
</div>
                `;
               id('tabBar').insertAdjacentHTML('afterend', addContent);
            }
            else if (id('ReservationContent') && id('ReservationContent').style.display === '')
            {
               id('ReservationContent').style.display = 'none';
            }
            else if (id('ReservationContent') && id('ReservationContent').style.display === 'none')
            {
               id('ReservationContent').style.display = '';
            }
         }
      }

      if (TabName == 'PromoContent')
      {
         //上次點擊和這次點擊不一樣
         if (theOldTabName != TabName)
         {
            theOldTabName = TabName;

            if (!id('PromoContent'))
            {
               let addContent = `
<div id="PromoContent" class="tabContents">
                    <h3>推廣內容</h3>
                    <p>Tokyo is the capital of Japan.</p>
</div>
                `;
               id('tabBar').insertAdjacentHTML('afterend', addContent);
            }
            else if (id('PromoContent') && id('PromoContent').style.display === '')
            {
               id('PromoContent').style.display = 'none';
            }
            else if (id('PromoContent') && id('PromoContent').style.display === 'none')
            {
               id('PromoContent').style.display = '';
            }
         }
      }

      if (TabName == 'MenuContent')
      {
         //上次點擊和這次點擊不一樣
         if (theOldTabName != TabName)
         {
            theOldTabName = TabName;

            //如果沒有任何菜式以及沒有按 添加菜式, 顯示提示
            function showAlertNoMenu()
            {
               let classAddMenu = document.getElementsByClassName('classAddMenu');
               let classShowMenu = document.getElementsByClassName('classShowMenu');
               if (classAddMenu.length == 0 && classShowMenu.length == 0)
               {
                  let addContent = `
        <div class="row text-center" id="alertNoMenu">
            <div class="col">
                <h3 class="alert alert-warning py-5 mt-5" style="border-radius: 15px;">店鋪暫時沒有添加任何菜式</h3>
            </div>
        </div>
                  `;
                  id('menuHeadBtns').insertAdjacentHTML('afterend', addContent);
               }
               else
               {
                  $("#alertNoMenu").remove();
               }
            }

            if (!id('MenuContent'))
            {
               let addContent = `
<div id="MenuContent" class="tabContents pt-3">
        <div class="row" id="menuHeadBtns">
            <div class="col d-flex">
                <h5 class="mt-2">店鋪的菜單</h5>
                <button class="btn btn-primary me-3 ms-auto" id="addMenu">添加菜式</button>
                <button class="btn btn-primary" id="saveMenuChange">保存修改</button>
            </div>
        </div>
</div>
                `;
               id('tabBar').insertAdjacentHTML('afterend', addContent);
               showAlertNoMenu();
               id('addMenu').addEventListener('click', function ()
               {
                  let addContent = `

                  `;
               })
            }
            else if (id('MenuContent') && id('MenuContent').style.display === '')
            {
               id('MenuContent').style.display = 'none';
            }
            else if (id('MenuContent') && id('MenuContent').style.display === 'none')
            {
               id('MenuContent').style.display = '';
            }
         }
      }

      if (TabName == 'TransInstructionContent')
      {
         //上次點擊和這次點擊不一樣
         if (theOldTabName != TabName)
         {
            theOldTabName = TabName;

            if (!id('TransInstructionContent'))
            {
               let addContent = `
<div id="TransInstructionContent" class="tabContents">
                    <h3>菜單內容</h3>
                    <p>Tokyo is the capital of Japan.</p>
</div>
                `;
               id('tabBar').insertAdjacentHTML('afterend', addContent);
            }
            else if (id('TransInstructionContent') && id('TransInstructionContent').style.display === '')
            {
               id('TransInstructionContent').style.display = 'none';
            }
            else if (id('TransInstructionContent') && id('TransInstructionContent').style.display === 'none')
            {
               id('TransInstructionContent').style.display = '';
            }
         }
      }

      let tabNameContentControl = TabName + 'Content';
      //document.getElementById(tabNameContentControl).setAttribute('style', 'display:none;');
      $(tabNameContentControl).fadeIn(0);
      $(tabNameContentControl).fadeTo(200, 0.6);
      $(tabNameContentControl).fadeTo(200, 1);
   }
</script>
<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"
>
</script>
<!-- Async script executes immediately and must be after any DOM elements used in callback. -->
<script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcXgca2h-tx-iKr-u5OomLp-ViAD9IdZU&libraries=places&v=weekly"
        async
></script>
</body>
</html>
