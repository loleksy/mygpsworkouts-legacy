var myGpsWorkouts = myGpsWorkouts || {};

myGpsWorkouts.utils = {
    hex2rgb:function(hex){
        if (hex.lastIndexOf('#') > -1) {
            hex = hex.replace(/#/, '0x');
        } else {
            hex = '0x' + hex;
        }
        var r = hex >> 16;
        var g = (hex & 0x00FF00) >> 8;
        var b = hex & 0x0000FF;
        return [r, g, b];
    },
    calcLuminate: function(r,g,b){
        // http://stackoverflow.com/a/1754281
        return (r*0.299 + g*0.587 + b*0.114) / 256;
    },
    isColorDark: function(r, g, b){
        return myGpsWorkouts.utils.calcLuminate(r,g,b) < 128;
    },
    geo:{
        getPointAtDistance:function(polyline, metres){
            // === A method which returns a GLatLng of a point a given distance along the path ===
            // === Returns null if the path is shorter than the specified distance ===
            // source: http://www.geocodezip.com/scripts/v3_epoly.js
                // some awkward special cases
                if (metres == 0) return polyline.getPath().getAt(0);
                if (metres < 0) return null;
                if (polyline.getPath().getLength() < 2) return null;
                var dist=0;
                var olddist=0;
                for (var i=1; (i < polyline.getPath().getLength() && dist < metres); i++) {
                    olddist = dist;
                    dist += google.maps.geometry.spherical.computeDistanceBetween(polyline.getPath().getAt(i),polyline.getPath().getAt(i-1));
                }
                if (dist < metres) {
                    return null;
                }
                var p1= polyline.getPath().getAt(i-2);
                var p2= polyline.getPath().getAt(i-1);
                var m = (metres-olddist)/(dist-olddist);
                return new google.maps.LatLng( p1.lat() + (p2.lat()-p1.lat())*m, p1.lng() + (p2.lng()-p1.lng())*m);
            }
    },
    formatDate: function(date){
        var yyyy = date.getFullYear().toString();
        var mm = (date.getMonth()+1).toString(); // getMonth() is zero-based
        var dd  = date.getDate().toString();
        return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
    }
};



