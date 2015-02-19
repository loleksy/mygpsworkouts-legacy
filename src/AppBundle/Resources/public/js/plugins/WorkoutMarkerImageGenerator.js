var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.plugins = myGpsWorkouts.plugins || {};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator = function(){
    this.canvas = null;
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize = 24;
myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images = {};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.background = new Image();
myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.background.src = 'data:image/png;base64,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';
myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.end = new Image();
myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.end.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAMxwAADMcBrInqMQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAGTSURBVEiJtdZBiE5RFAfw35lhx0izVRrFTiRmN7EQsbGxQIkQG0maZqFsrVjYSLGwQ03KgrISdqZmNGWhRlOiKGUxw0JxLOYuHr3ve9/XfO/WXbw69/8///85594XmanNNdQq+moJImIiIta0QhARW3EGl7sGZmbjxgh2YLh8r8VNbME8xjqdjaYiR8RhXMQ09uAzDhTwE9iPj5l5r+58rX8RsQ6j2FdihvEUe3EH77Eb4ziCdxGxlJmP/sfqVIMreIivWMA3TOEkTuEGXuAXzuMNNtYidfB8CDN4gOc4i204hrDi/8FCPInf2FmHVWtRZv6JiAslsyhkV/EKx7EZ33EbT7CcmW/rsLoWOSJu4RC24xyWsQuL+Fkyf5mZCx1BGtpzPT6V/QPjvbT1Pxg9zMBRJK71C56ZzZOcmdN4hi9NsX1bVFExhg/YMHAFJYlF3MX1VhRU7p95fRa6v45gArPKpTcwiypqX2MOlwZuUUXFqJVB2zRwiyokp/G4l9iuz10X1fcjYqmX2MYHZ7Wr9b+Kv+owZ9BS1VBMAAAAAElFTkSuQmCC5591af9a5e4e8dde8afa9e206f818163';
myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.start = new Image();
myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.start.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAE7wAABO8BJ9fcHwAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAGXSURBVDiNjZM/T1VBEMV/5+wL8nxYaGHjn1YMsSFqiBhCR4yh0MTCxI7EmBBM5JNIp0JlrXaGysSGSvgUdlbEIKLAHYs7XtcXEDe5ye7OOWfumZ0hIjjqAy4I7wjvAOPH4XoMLUmnocwZzwXsAxg/lXrrcPghInb/wmc2JN0EesKvgYHgI/AjcacCZoG9oHkQEZudQgpcE94S3jdeAUbzfgqYyv2o8YrwdygPO6sZvCz8Dbx4nNc/tfGi8FfgYkS0Fiy/AfpNNHeHa3LUsvwe+NlEcw/gjHADTJ+UvXqhaeFDYABwu/XFyBDI4Ge13yo2kpxbQFkQ3jZeA/oV6KpwCB8AZ6v7vvGa8DaUBYCZLEoZyiLj58arQm+BK1WsJGcGYCz9TP7D8znjl1Ae5XkyOWNEBKJsGK/+R/EGEUH7V2Wj7oMJ4T0o8yeLlPkWy0QnkA2yLLwLXqpafJwcJEDgpcQs/+Z1s9DOQ+++iBfAZ8F6S8qOhzvApUBPIg7edZxaoBXRefBjETdA15O/GegTNK8i4kuN/wVgmWuS5dF6dAAAAABJRU5ErkJgggfbe3532ff2e759cfd192b670576a73b1';


myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.generateStartImage = function(color){
    this.prepareCanvas();
    this.renderBackground(color);
    this.renderStartIcon(color);
    return this;
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.generateEndImage = function(color){
    this.prepareCanvas();
    this.renderBackground(color);
    this.renderEndIcon(color);
    return this;
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.generateNumberImage = function(color, number){
    this.prepareCanvas();
    this.renderBackground(color);
    this.renderNumber(number);
    return this;
};


myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.prepareCanvas = function(){
    this.canvas = document.createElement('canvas');
    this.canvas.width = myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize;
    this.canvas.height = myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize;
    this.canvasCtx = this.canvas.getContext("2d");
    this.canvasCtx.clearRect(0, 0, myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize, myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize);
    return this;
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.renderBackground = function(color){
    this.canvasCtx.drawImage(myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.background,0,0, myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize, myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize);
    var map = this.canvasCtx.getImageData(0, 0, myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize, myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize);
    var imdata = map.data;
    var rgbColor = myGpsWorkouts.utils.hex2rgb(color);
    for(var p = 0, len = imdata.length; p < len; p+=4) {
        imdata[p] = imdata[p]/255 * rgbColor[0];
        imdata[p+1] = imdata[p+1]/255 * rgbColor[1];
        imdata[p+2] = imdata[p+2]/255 * rgbColor[2];
        // alpha channel (p+3) is ignored
    }
    this.canvasCtx.putImageData(map,0,0);
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.renderNumber = function(number){
    this.canvasCtx.translate(0,0);
    this.canvasCtx.font = '8pt Arial';
    this.canvasCtx.fillStyle = '#ffffff';
    this.canvasCtx.textAlign = 'center';
    this.canvasCtx.fillText(String(number), myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize/2, 8  + (myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.imageSize-8)/2);
};


myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.renderStartIcon = function(color){
    var rgbColor = myGpsWorkouts.utils.hex2rgb(color);
    if(myGpsWorkouts.utils.isColorDark(rgbColor[0], rgbColor[1], rgbColor[2])){
        //color is dark, invert icon to white
        var invertedIconImage = this.getInvertedIconImage(myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.start)
        this.canvasCtx.drawImage(invertedIconImage,4,4, 16, 16);
    }
    else{
        this.canvasCtx.drawImage(myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.start,4,4, 16, 16);
    }
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.renderEndIcon = function(color){
    var rgbColor = myGpsWorkouts.utils.hex2rgb(color);
    if(myGpsWorkouts.utils.isColorDark(rgbColor[0], rgbColor[1], rgbColor[2])){
        //color is dark, invert icon to white
        var invertedIconImage = this.getInvertedIconImage(myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.end)
        this.canvasCtx.drawImage(invertedIconImage,4,4, 16, 16);
    }
    else{
        this.canvasCtx.drawImage(myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.images.end,4,4, 16, 16);
    }
};

myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.getInvertedIconImage = function(iconImage){
    var tmpCanvas = document.createElement('canvas');
    tmpCanvas.width = 16;
    tmpCanvas.height = 16;
    var tmpCanvasCtx = tmpCanvas.getContext("2d");
    tmpCanvasCtx.drawImage(iconImage, 0, 0, 16, 16);
    var map = tmpCanvasCtx.getImageData(0, 0, 16, 16);
    var imdata = map.data;
    for(var p = 0, len = imdata.length; p < len; p+=4) {
        imdata[p] = 255 - imdata[p]/255;
        imdata[p+1] = 255 - imdata[p+1]/255;
        imdata[p+2] = 255 - imdata[p+2]/255;
        // alpha channel (p+3) is ignored
    }
    tmpCanvasCtx.putImageData(map,0,0);
    var img = new Image();
    img.src = tmpCanvas.toDataURL();
    return img;
};


myGpsWorkouts.plugins.WorkoutMarkerImageGenerator.prototype.getCanvas = function(){
    return this.canvas;
};









