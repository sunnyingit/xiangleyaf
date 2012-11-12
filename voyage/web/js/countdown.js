var Countdown = function()
{
    var self = this;

    this.container = null;
    this.seconds   = null;
    this.format    = null;
    this.callback  = null;

    this.init = function(options)
    {
        this.container = options.container || "clock";
        this.seconds   = options.seconds || 10;
        this.format    = options.format || "dd:hh:mm:ss";
        this.callback  = options.callback;

        if (this.seconds < 1) {
            return false;
        }

        // 开始执行
        this.countBack(this.seconds);
    };

    this.calculate = function(secs, num1, num2)
    {
        var str = (Math.floor(secs / num1) % num2).toString();
        if (str.length < 2) {
            str = "0" + str;
        }
        return str;
    };

    this.countBack = function(secs)
    {
        if (secs < 0) {
            if (this.callback) {
                this.callback();
            }
            return;
        }

        var string = this.format;

        if (string.indexOf("dd") != -1) {
            string = string.replace(/dd/g, this.calculate(secs, 86400, 100000));
        }

        if (string.indexOf("hh") != -1) {
            string = string.replace(/hh/g, this.calculate(secs, 3600, 24));
        }

        string = string.replace(/mm/g, this.calculate(secs, 60, 60));
        string = string.replace(/ss/g, this.calculate(secs, 1, 60));

        document.getElementById(this.container).innerHTML = string;

        setTimeout(function() {
            self.countBack(secs - 1);
        }, 990);
    };

    // 离目标日期还有多少秒
    this.targetDateSecs = function()
    {
        var ddiff = new Date(new Date(this.targetDate) - new Date());
        return Math.floor(ddiff.valueOf() / 1000);
    };
};