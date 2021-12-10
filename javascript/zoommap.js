jQuery.fn.zoommap = function (){
    $('.zoommap',this).each(function(){
        var pThis=$(this);
        var imgUrl=pThis.data('mapimage')
        var userMapImg = new Image();

        userMapImg.onload = function(){
          var height = userMapImg.height;
          var width = userMapImg.width;

          // use the dimensions of the loaded image
          var text=pThis.html();
          pThis.text('').show();
          var map = L.map(pThis[0], {crs: L.CRS.Simple,minZoom: -5});
          var bounds = [[0,0], [height,width]];
          var image = L.imageOverlay(imgUrl, bounds).addTo(map);
          map.fitBounds(bounds);

          var lines = text.split("<br>");
          var setInitialZoom=false;
          for(var i=0;i<lines.length;i++){
              var line=$.trim((lines[i]));
              if(line){
                  var bits=line.split('|');
                  var xy=$.trim(bits[0]).split(',');
                  if(xy.length==2){
                        var x=$.trim(xy[0]);
                        var y=$.trim(xy[1]);
                        var markerPos = L.latLng([ height-y, x]);
                        var marker=L.marker(markerPos);
                        var label=bits.length>=1 && $.trim(bits[1]);
                        if(label){
                            marker.bindPopup(label);
                        }
                        marker.addTo(map);
                        var labelClass=bits.length>=2 && $.trim(bits[2]);
                        if(labelClass){
                            marker._icon.className+=(' leaflet-div-icon-'+labelClass.toLowerCase());
                        }

                        if(!setInitialZoom){
                            map.setView([height-y,x],-1);
                            setInitialZoom=true;
                        }
                    }
                }
            }

            if(!setInitialZoom){
                map.setView([height/2,width/2],-2);
            }
        }
        userMapImg.src = imgUrl;
    });
    return this;
};

$(function() {
	$('.post').zoommap();
});
