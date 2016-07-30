$(document).ready(function() {
    console.log("page loaded");

    var canvas;
    var context;
    var ctx;
    var ctx_offscreen;

    var gw = 940;
    var gh = 779;
    var p  = 10;

    var BoundingBox;
    var offsetX;
    var offsetY;
    var dragok = false;

    var mousePressed = false;
    var startX = 0;
    var startY = 0;
    var mouseX = 0;
    var mouseY = 0;
    var mouseInCorner = 0;

    var hdObjectPtr = [];
    var mousePos;

    var menuitem_flag = false;

    function init_app()
    {
        canvas  = document.getElementById("drawCanvas");
        ctx     = canvas.getContext("2d");
        context = ctx;

        canvas.onmousedown = objDown;
        canvas.onmouseup = objUp;
        canvas.onmousemove = objMove;

        BoundingBox = canvas.getBoundingClientRect();
        offsetX = BoundingBox.left;
        offsetY = BoundingBox.top;

        canvasPosition = {
            x: $(canvas).offset().left,
            y: $(canvas).offset().top
        };
    }


    function togglemenu (menu_item) {
        var item = menu_item.text();
        menu_item.click(function() {
            if (item === "Anmelden") {
                $("#login_page").toggle();
                return;
            }

            if (menuitem_flag == false) {
                menuitem_flag = true;
                if (item === "Hilfe") {
                    $(".menu_help").toggle();
                }
            }
            else {
                menuitem_flag = false;
                if (item === "Hilfe") {
                    $(".menu_help").toggle();
                }
            }
        });
    }

    function drawBoardGrid_initial()
    {
        ctx.canvas.width  = gw;
        ctx.canvas.height = gh;

        ctx.lineWidth = 0.5;

        for (x = 0; x <= gw; x += 10) {
            ctx.moveTo(x, 0);
            ctx.lineTo(x, gh);
        }

        for (y = 0; y <= gh; y += 10) {
            ctx.moveTo(0, y);
            ctx.lineTo(gw, y);
        }

        ctx.strokeStyle = "gray";
        ctx.stroke();

        ctx_offscreen = ctx.getImageData(0,0,gw,gh);
    }

    function drawBoardGrid()
    {
        ctx.putImageData(ctx_offscreen,0,0);
    }

    var drawBoard = function()
    {
        init_app();
        drawBoardGrid_initial();

        hdptr = new hdObjectClass();

        hdptr.add("rect1", 200,200,200,200);
        hdptr.add("rect2", 100,100,100,100);
    };

    function writeMessage(canvas, message) {
      var context = canvas.getContext('2d');
      context.clearRect(0, 0, canvas.width, 32);
      context.font = '18pt Calibri';
      context.fillStyle = 'black';
      context.fillText(message, 10, 25);
    }
    function getMousePos(canvas, evt) {
      var rect = canvas.getBoundingClientRect();
      return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
      };
    }


    function prepareWorkPlace(username)
    {
        $("#login_page").hide();
        $("#menu_login").hide();

        $("#left_menupanel").height(300);
        $("#right_menupanel").hide();

        $("#middle_menupanel")
        .height(800)
        .width(962)

        $("#middle_menupanel_inner")
        .html(
        '<canvas id=\"drawCanvas\" width=\"937px\" height=\"774px\" ' +
        'style=\"border:2px solid #000000;\">' +
        '</canvas>')
        .css({ "border-width": "6px",
               "border-color": "black",
               "background-color": "rgb(230,230,230)",
               "left": "10px",
               "top": "10px",
               "width": "940px",
               "height": "779px",
               "position": "absolute"
              });
        /*
        $("#middle_menupanel_inner").append(
        "<div id='designerdiv' style='" +
        "border-width: 6px;  " +
        "border-color: black;" +
        "background-color: rgba(230,230,230,0.8);" +
        "left: 2px;" +
        "top: 2px;" +
        "width: 940px;" +
        "height: 779px;" +
        "position: absolute;'></div>").show();*/

        $("#page_container").height(1016);
        $("#workplace").height(820);

        drawBoard();
        canvas.addEventListener('mousemove', function(evt) {
            mousePos = getMousePos(canvas, evt);
            //var message = 'Mouse position: ' + mousePos.x + ',' + mousePos.y;
            var message = "Mouse: " + (mousePos.x) + ", mX: " + (mouseX);
            writeMessage(canvas, message);
         }, false);
        canvas.addEventListener('mousedown', function(evt) {
            objDown(evt);
        });
    }

    $(".login_page").mouseenter(function() {
        togglemenu($(this));
        return;
    });

    $("#login_page_button").click(function() {
        var UID = $("#login_username").val();
        var IDPASS = "gonzo"; $("#login_password").val();

        prepareWorkPlace(UID);
        return;



        $.ajax({
            type: "POST",
            url: "login.php",
            data: {user: UID, pass: IDPASS, mode: '0'},
            cache: false,
            success: function(msg)
            {
                if (msg === "info: ok. sel") {
                    prepareWorkPlace(UID);
                }
                else {
                    alert("Falsche Anmeldedaten!")
                }
            },
            error: function(data)         {
                alert("Es ist ein Fehler aufgetretten!\n" + data);
                return;
            }
        });     return;
    });

    $(".menu_item").mouseenter(function() {
        $(this).attr('style', 'background-color:red');
        $(this).fadeOut(100);
        $(this).fadeIn (100);  togglemenu($(this));
    }).mouseleave(function() {
        $(this).attr('style', 'background-color:#00e200');
        $(this).fadeOut(100);
        $(this).fadeIn (100);  togglemenu($(this));
    });

    function clear_drawArea() {
        ctx.clearRect(0, 0, gw, gh);
    }

    function update() {
        clear_drawArea();
        ctx.rect(0,0, gw,gh);
        drawBoardGrid()
        for (var idx = 0; idx < hdObjectPtr.length; idx++) {
            var r =  hdObjectPtr[idx];
            r._self.drawObject(r._x, r._y, r._width, r._height);

            if (mouseInCorner == 1) {
                r.drawMoverRect(r);
            }

            if (r._isdrag)
            r._self.drawMovers();
        }
    }

    function objDown(e) {
        e.preventDefault();
        e.stopPropagation();

        // get the current mouse position
        var mx = parseInt(e.clientX - offsetX);
        var my = parseInt(e.clientY - offsetY);

        dragok = false;
        for (var idx = 0; idx < hdObjectPtr.length; idx++) {
            var r = hdObjectPtr[idx];

            //{ _x2: x   -10, _y2: y    -10, _w2: width, _h2: height },
            //{ _x2: x   -10, _y2: y+height, _w2: width, _h2: height },
            //{ _x2: x+width, _y2: y    -10, _w2: width, _h2: height },
            //{ _x2: x+width, _y2: y+height, _w2: width, _h2: height }

            if (mx > r._x - 10 && mx < r._x + 10
            && (my > r._y - 10 && my < r._y + 10))
            {
                mouseInCorner = 1;
                mouseX = mx;
                mouseY = my;
                dragok = true;
                r._isdrag = true;
                break;
            }
            else

            if (mx > r._x && mx < r._x + r._width
            &&  my > r._y && my < r._y + r._height) {
                dragok = true;
                r._isdrag = true;
                break;
            }
        }

        // save the current mouse position
        startX = mx;
        startY = my;
    }

    function objUp(e) {
        e.preventDefault();
        e.stopPropagation();

        dragok = false;
        for (var idx = 0; idx < hdObjectPtr.length; idx++) {
            hdObjectPtr[idx]._isdrag = false;
        }
    }

    function objMove(e)
    {
        if (dragok) {

            // tell the browser we're handling this mouse event
            e.preventDefault();
            e.stopPropagation();

            // get the current mouse position
            var mx = parseInt(e.clientX - offsetX);
            var my = parseInt(e.clientY - offsetY);

            // calculate the distance the mouse has moved
            // since the last mousemove
            var dx = mx - startX;
            var dy = my - startY;

            // move each rect that isDragging
            // by the distance the mouse has moved
            // since the last mousemove
            for (var i = 0; i < hdObjectPtr.length; i++) {
                var r = hdObjectPtr[i];
                if (r._isdrag) {
                    r._x += dx;
                    r._y += dy;
                }
            }

            // redraw the scene with the new rect positions
            update();

            // reset the starting mouse position for the next mousemove
            startX = mx;
            startY = my;
        }
    }

    // ---------------------------------
    // Object class for the desinger ...
    // ---------------------------------
    var hdObjectClass  = function() {};
    hdObjectClass.prototype = {
        obj: null, // temp ptr
        _x: 0,
        _y: 0,
        _width: 0,
        _height: 0,
        _name: "0_0",
        _html: "",
        _isdrag: false,
        _self: this,

        _corners: [
            { _x2: 0, _y2:0, _w2: 0, _h2: 0 },
            { _x2: 0, _y2:0, _w2: 0, _h2: 0 },
            { _x2: 0, _y2:0, _w2: 0, _h2: 0 },
            { _x2: 0, _y2:0, _w2: 0, _h2: 0 },
        ],

        add: function(name,x,y,width,height) {
            obj = new hdObjectClass();
            obj._x = x;
            obj._y = y;
            obj._width = width;
            obj._height = height;
            obj._name = name;
            obj._isdrag = false;
            obj._self = this;

            obj._html = "<div id='obj_" + obj._name + "'></div>";
            $("#noDisplayArea").append(obj._html);

            this.drawObject(x,y,width,height);
            hdObjectPtr.push(obj);
        },
        drawObject: function (x, y, width, height) {
            context.beginPath();
            context.rect(x, y, width, height);
            context.fillStyle = 'yellow';
            context.fill();
            context.lineWidth = 7;
            context.strokeStyle = 'black';
            context.stroke();

            _corners = [
                { _x2: x   -10, _y2: y    -10, _w2: width, _h2: height },
                { _x2: x   -10, _y2: y+height, _w2: width, _h2: height },
                { _x2: x+width, _y2: y    -10, _w2: width, _h2: height },
                { _x2: x+width, _y2: y+height, _w2: width, _h2: height }
            ];
        },
        drawMoverRect: function(r) {
            context.beginPath();

            var xp = mousePos.x;
            var yp = mousePos.y;

            var ox = r._x;
            var oy = r._y;

            var xd, wn;
            var yd, hn;

            if (mousePos.x < mouseX)
            {
                xd = mouseX - mousePos.x;
                r._x     = r._width + xd;
                r._width = r._width + xd; //mouseX + (r._x - mouseX);
            }

            xp = r._x;

            hn = r._height;

            /*
            var xn = r._x - (xp + r._x);
            var yn = r._y - (yp - r._y);

            var hn = (yp - r._y) + r._height;


            var wn = r._width  - xn;
*/

            context.rect(xp,yp,wn,hn);
            context.lineWidth = 2;

            context.strokeStyle = 'red';
            context.stroke();
        },
        drawMovers: function(o) {
            this.drawActiveMovers(o);
        },
        drawActiveMovers: function(o) {
            context.beginPath();

            for (var i=0; i < 4; i++)
            context.rect(
                _corners[i]._x2,
                _corners[i]._y2, 10, 10
            );

            context.fillStyle = 'blue';
            context.fill();
            context.lineWidth = 1;

            context.strokeStyle = 'red';
            context.stroke();

        }
    };
});
